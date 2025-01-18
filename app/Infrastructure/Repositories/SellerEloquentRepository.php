<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Sellers\Seller;
use App\Domain\Sellers\SellerRepository;
use App\Infrastructure\Eloquent\SellerEloquentModel;
use Illuminate\Support\Facades\DB;

/**
 * Repositório de Vendedores Eloquent
 *
 * Implementação do repositório de vendedores usando Eloquent ORM para persistir e recuperar dados dos vendedores.
 */
class SellerEloquentRepository implements SellerRepository
{
    /**
     * Salva ou atualiza um vendedor no banco de dados.
     *
     * Se o vendedor já existir, ele será atualizado. Caso contrário, será criado um novo vendedor.
     *
     * @param Seller $seller O vendedor a ser salvo ou atualizado.
     * @return Seller O vendedor com os dados atualizados, incluindo o ID.
     */
    public function save(Seller $seller): Seller
    {
        try {
            DB::beginTransaction();

            $eloquentSeller = SellerEloquentModel::find($seller->getId()) ?? new SellerEloquentModel();
            $eloquentSeller->name = $seller->getName();
            $eloquentSeller->email = $seller->getEmail();
            $eloquentSeller->save();

            $seller->setId($eloquentSeller->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $seller;
    }

    /**
     * Encontra um vendedor pelo seu ID.
     *
     * Esse método recupera um vendedor específico a partir do banco de dados usando seu ID.
     *
     * @param int $id O ID do vendedor a ser encontrado.
     * @return Seller|null O vendedor encontrado ou null caso não seja encontrado.
     */
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

    /**
     * Encontra todos os vendedores.
     *
     * Esse método recupera todos os vendedores do banco de dados.
     *
     * @return array Uma lista de objetos Seller representando todos os vendedores.
     */
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

    /**
     * Recupera todos os vendedores com a soma das comissões associadas a cada um.
     *
     * Esse método recupera todos os vendedores juntamente com a soma das comissões das vendas associadas a eles.
     *
     * @return array Uma lista de objetos Seller, incluindo a comissão total.
     */
    public function fetchAllSellersWithCommission(): array
    {
        $eloquentSellers = SellerEloquentModel::withSum('sales as total_commission', 'sale_commission')->get();

        return $eloquentSellers->map(function ($eloquentSeller) {
            $seller = new Seller(
                $eloquentSeller->id,
                $eloquentSeller->name,
                $eloquentSeller->email
            );

            $seller->setTotalCommission($eloquentSeller->total_commission ?? 0);

            return $seller;
        })->toArray();
    }

    /**
     * Recupera o ID do último vendedor inserido.
     *
     * Esse método retorna o ID do vendedor mais recentemente inserido no banco de dados.
     *
     * @return int O ID do último vendedor inserido.
     */
    public function getLastInsertedId(): int
    {
        return SellerEloquentModel::latest('id')->first()->id;
    }
}
