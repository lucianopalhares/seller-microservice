<?php

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Elastic\Elasticsearch\ClientBuilder;

class SaleEloquentModel extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = ['seller_id', 'sale_value', 'sale_commission'];

    /**
     * Venda pertence a um vendedor
     *
     * @return mixed
     */
    public function seller()
    {
        return $this->belongsTo(SellerEloquentModel::class, 'seller_id');
    }


    /**
     * Definir o valor da comissão da venda
     *
     * @param float $commissionValue
     * @return void
     */
    public function setCommissionValue(float $commissionValue): void
    {
        $this->sale_commission = $commissionValue;
    }

    /**
     * Indexar dados no Elasticsearch.
     */
    public function indexToElasticsearch()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

        $params = [
            'index' => 'sales',
            'id'    => $this->id,
            'body'  => [
                'seller_id' => $this->seller_id,
                'sale_value' => $this->sale_value,
                'sale_commission' => $this->sale_commission,
                'sale_date' => $this->created_at->toDateString(),
            ]
        ];

        $params = [
            'index' => 'test',
            'body'  => [
                'settings' => [
                    'number_of_shards' => 1,  // Example of setting index properties
                ],
                'mappings' => [
                    'properties' => [
                        'name' => ['type' => 'text'],
                        'email' => ['type' => 'text'],
                    ]
                ]
            ]
        ];


        $response = $client->index($params);

        return $response;
    }
}
