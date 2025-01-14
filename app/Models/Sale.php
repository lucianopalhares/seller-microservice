<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'sale_value', 'sale_commission'];

    /**
     * Venda pertence a um vendedor
     *
     * @return mixed
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
