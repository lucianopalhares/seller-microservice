<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Sales\Sale;
use App\Domain\Sellers\Seller;
use App\Domain\Sales\SaleRepository;
use App\Models\Sale as EloquentSale;

class SaleEloquentRepository implements SaleRepository
{
    public function save(Sale $sale): Sale
    {
        $eloquentSale = new EloquentSale();
        $eloquentSale->seller_id = $sale->getSeller()->getId();
        $eloquentSale->sale_value = $sale->getValue();
        $eloquentSale->sale_commission = $sale->getCommission();
        $eloquentSale->save();

        $sale->setId($eloquentSale->id);
        $sale->setCommission($eloquentSale->sale_commission);
        $sale->setSaleDate($eloquentSale->created_at);

        return $sale;
    }

    public function findBySeller(int $sellerId): array
    {
        return EloquentSale::where('seller_id', $sellerId)->get()->map(function ($sale) {
            $eloquentSeller = $sale->seller;

            $seller = new Seller(
                $eloquentSeller->id,
                $eloquentSeller->name,
                $eloquentSeller->email
            );
            return new Sale($sale->id, $seller, $sale->sale_value, $sale->sale_commission, $sale->created_at);
        })->toArray();
    }

    public function findAll(): array
    {
        return EloquentSale::all()->map(function ($sale) {
            return new Sale($sale->id, $sale->seller, $sale->sale_value, $sale->sale_commission, $sale->created_at);
        })->toArray();
    }
}
