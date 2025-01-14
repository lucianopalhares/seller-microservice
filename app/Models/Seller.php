<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];

    /**
     * Vendedor possui muitas vendas
     *
     * @return mixed
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
