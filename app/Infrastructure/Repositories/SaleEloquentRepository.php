<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Sales\Sale;
use App\Domain\Sellers\Seller;
use App\Domain\Sales\SaleRepository;
use App\Infrastructure\Eloquent\SaleEloquentModel;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

/**
 * Repositório de Vendas Eloquent
 *
 * Implementação do repositório de vendas usando Eloquent ORM para persistir e recuperar dados das vendas.
 */
class SaleEloquentRepository implements SaleRepository
{
    /**
     * Salva uma venda no banco de dados.
     *
     * Esse método cria uma nova venda no banco de dados e a persiste. Além disso, realiza a indexação da venda no Elasticsearch.
     * Se ocorrer algum erro, a transação é revertida.
     *
     * @param Sale $sale A venda a ser salva.
     * @return Sale A venda com os dados atualizados (incluindo o ID gerado).
     */
    public function save(Sale $sale): Sale
    {
        $SaleEloquentModel = new SaleEloquentModel();
        $SaleEloquentModel->seller_id = $sale->getSeller()->getId();
        $SaleEloquentModel->sale_value = $sale->getValue();
        $SaleEloquentModel->sale_commission = $sale->getCommission();
        $SaleEloquentModel->save();

        $sale->setId($SaleEloquentModel->id);
        $sale->setCommission($SaleEloquentModel->sale_commission);
        $sale->setSaleDate($SaleEloquentModel->created_at);

        $this->indexToElasticsearch($SaleEloquentModel);

        return $sale;
    }

    /**
     * Salva a venda no elasticsearch.
     *
     * @param object $data Dados da venda.
     * @return void
     */
    private function indexToElasticsearch(object $data): void
    {
        $elasticsearchService = new ElasticsearchService();
        $elasticsearchService->saveToSalesIndex($data);
    }

    /**
     * Encontra todas as vendas de um vendedor específico.
     *
     * Esse método recupera todas as vendas de um vendedor a partir do banco de dados,
     * mapeando os dados para objetos do domínio de vendas.
     *
     * @param int $sellerId O ID do vendedor.
     * @return array Uma lista de objetos Sale representando as vendas do vendedor.
     */
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

    /**
     * Encontra todas as vendas.
     *
     * Esse método recupera todas as vendas do banco de dados e as mapeia para objetos do domínio de vendas.
     *
     * @return array Uma lista de objetos Sale representando todas as vendas.
     */
    public function findAll(): array
    {
        return SaleEloquentModel::all()->map(function ($sale) {
            $eloquentSeller = $sale->seller;

            $seller = new Seller(
                $eloquentSeller->id,
                $eloquentSeller->name,
                $eloquentSeller->email
            );
            return new Sale($sale->id, $seller, $sale->sale_value, $sale->sale_commission, $sale->created_at);
        })->toArray();
    }
}
