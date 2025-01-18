<?php

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

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
}
