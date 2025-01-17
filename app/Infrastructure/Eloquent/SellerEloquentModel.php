<?php

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Elastic\Elasticsearch\ClientBuilder;

class SellerEloquentModel extends Model
{
    use HasFactory;

    protected $table = 'sellers';

    protected $fillable = [
        'name',
        'email'
    ];

    /**
     * Define the relationship that a seller has many sales.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany(SaleEloquentModel::class, 'seller_id');
    }

    /**
     * Indexar dados no Elasticsearch.
     */
    public function indexToElasticsearch()
    {
        try {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

            $params = [
                'index' => 'sellers',
                'id'    => $this->id,
                'body'  => [
                    'name' => $this->name,
                    'email' => $this->email
                ]
            ];

            $client->indices()->create($params);

            return true;
        } catch (\Exception $e) {
            print_r($e);exit;
        }

    }
}
