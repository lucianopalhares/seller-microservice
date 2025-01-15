<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Sellers\Seller;
use App\Domain\Sellers\SellerRepository;
use App\Infrastructure\Eloquent\SellerEloquentModel;

class SellerEloquentRepository implements SellerRepository
{
    /**
     * Salva o vendedor no banco de dados.
     * Se o vendedor já existir, ele será atualizado. Caso contrário, será criado um novo vendedor.
     *
     * @param Seller $seller
     * @return Seller
     */
    public function save(Seller $seller): Seller
    {
        $eloquentSeller = SellerEloquentModel::find($seller->getId()) ?? new SellerEloquentModel();
        $eloquentSeller->name = $seller->getName();
        $eloquentSeller->email = $seller->getEmail();
        $eloquentSeller->save();

        // Atualiza o ID do vendedor de domínio com o ID gerado pelo Eloquent
        $seller->setId($eloquentSeller->id);

        // Retorna o objeto Seller atualizado
        return $seller;
    }

    public function findById(int $id): ?Seller
    {
        $eloquentSeller = SellerEloquentModel::find($id);

        if (!$eloquentSeller) {
            return null;
        }

        return new Seller(
            $eloquentSeller->id,
            $eloquentSeller->name,
            $eloquentSeller->email
        );
    }

    public function findAll(): array
    {
        $eloquentSellers = SellerEloquentModel::all();

        return $eloquentSellers->map(function ($eloquentSeller) {
            return new Seller(
                $eloquentSeller->id,
                $eloquentSeller->name,
                $eloquentSeller->email
            );
        })->toArray();
    }

    public function getAllSellersWithTotalCommission(): array
    {
        // Pega os vendedores com a soma das comissões associadas a eles
        $eloquentSellers = SellerEloquentModel::withSum('sales as total_commission', 'sale_commission')->get();

        // Mapeia os vendedores Eloquent para o modelo de domínio Seller
        return $eloquentSellers->map(function ($eloquentSeller) {
            // Cria o Seller com a comissão total
            $seller = new Seller(
                $eloquentSeller->id,
                $eloquentSeller->name,
                $eloquentSeller->email
            );

            $seller->setTotalCommission($eloquentSeller->total_commission ?? 0);

            return $seller;
        })->toArray();
    }

    public function getLastInsertedId(): int
    {
        return SellerEloquentModel::latest('id')->first()->id;
    }
}
