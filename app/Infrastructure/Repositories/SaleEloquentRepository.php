<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Sales\Sale;
use App\Domain\Sellers\Seller;
use App\Domain\Sales\SaleRepository;
use App\Infrastructure\Eloquent\SaleEloquentModel;
use Illuminate\Support\Facades\DB;

class SaleEloquentRepository implements SaleRepository
{
    public function save(Sale $sale): Sale
    {
        try {
            DB::beginTransaction();

            $SaleEloquentModel = new SaleEloquentModel();
            $SaleEloquentModel->seller_id = $sale->getSeller()->getId();
            $SaleEloquentModel->sale_value = $sale->getValue();
            $SaleEloquentModel->sale_commission = $sale->getCommission();
            $SaleEloquentModel->save();

            $SaleEloquentModel->indexToElasticsearch();
            $sale->setId($SaleEloquentModel->id);
            $sale->setCommission($SaleEloquentModel->sale_commission);
            $sale->setSaleDate($SaleEloquentModel->created_at);

            $SaleEloquentModel->indexToElasticsearch();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $sale;
    }

    public function findBySeller(int $sellerId): array
    {
        return SaleEloquentModel::where('seller_id', $sellerId)->get()->map(function ($sale) {
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
        return SaleEloquentModel::all()->map(function ($sale) {
            return new Sale($sale->id, $sale->seller, $sale->sale_value, $sale->sale_commission, $sale->created_at);
        })->toArray();
    }
}
