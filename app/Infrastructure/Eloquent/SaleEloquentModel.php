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
     * Indexar dados da venda no Elasticsearch.
     */
    public function indexToElasticsearch()
    {
        try {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

            $params = [
                'index' => 'sales',
                'id'    => $this->id,
                'body'  => [
                    'name' => $this->seller->name,
                    'email' => $this->seller->email,
                    'value' => $this->sale_value,
                    'commission' => $this->sale_commission,
                    'date' => $this->created_at->toDateString(),
                ]
            ];

            $client->index($params);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
