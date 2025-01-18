<?php

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
