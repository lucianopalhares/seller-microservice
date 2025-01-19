<?php

namespace App\Application\Sales\Services;

use App\Domain\Sales\SaleRepository;
use App\Domain\Sales\Sale;
use App\Domain\Sellers\SellerRepository;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Classe responsável pelos serviços relacionados a vendas.
 */
class SaleService
{
    /**
     * Repositório de vendas.
     *
     * @var SaleRepository
     */
    private SaleRepository $saleRepository;

    /**
     * Repositório de vendedores.
     *
     * @var SellerRepository
     */
    private SellerRepository $sellerRepository;

    /**
     * Venda atual.
     *
     * @var Sale
     */
    private Sale $sale;

    /**
     * Conjunto de vendas.
     *
     * @var array
     */
    private array $sales = [];

    /**
     * Mensagem de erro.
     *
     * @var string
     */
    private string $error;

    /**
     * Construtor da classe SaleService.
     *
     * @param SaleRepository $saleRepository
     * @param SellerRepository $sellerRepository
     */
    public function __construct(SaleRepository $saleRepository, SellerRepository $sellerRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Define a venda atual.
     *
     * @param Sale $sale
     * @return void
     */
    public function setSale(Sale $sale): void
    {
        $this->sale = $sale;
    }

    /**
     * Obtém a venda atual.
     *
     * @return Sale
     */
    public function getSale(): Sale
    {
        return $this->sale;
    }

    /**
     * Define o conjunto de vendas.
     *
     * @param array $sales
     * @return void
     */
    public function setSales(array $sales): void
    {
        $this->sales = $sales;
    }

    /**
     * Obtém o conjunto de vendas.
     *
     * @return array
     */
    public function getSales(): array
    {
        return $this->sales;
    }

    /**
     * Define a mensagem de erro.
     *
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * Obtém a mensagem de erro.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Verifica se existe uma mensagem de erro.
     *
     * @return bool
     */
    public function errorExists(): bool
    {
        return empty($this->error) === false;
    }

    /**
     * Cria uma nova venda.
     *
     * @param int $sellerId ID do vendedor.
     * @param float $value Valor da venda.
     * @return bool
     */
    public function createSale(int $sellerId, float $value): bool
    {
        try {
            $seller = $this->sellerRepository->findById($sellerId);

            if (!$seller) {
                $this->setError("Vendedor não encontrado.");
                return false;
            }

            if ($value <= 0) {
                $this->setError("O valor da venda deve ser maior que zero.");
                return false;
            }

            $commission = round($value * 0.085, 2);

            $sale = new Sale(0, $seller, $value, $commission);

            DB::beginTransaction();
            $save = $this->saleRepository->save($sale);
            DB::commit();

            $this->setSale($save);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('seller_microservice')->error($e->getMessage(), ['sellerId' => $sellerId, 'value' => $value]);
            return false;
        }
    }

    /**
     * Obtém as vendas de um vendedor específico.
     *
     * @param int $sellerId ID do vendedor.
     * @return array
     */
    public function fetchSalesBySeller(int $sellerId): bool
    {
        try {
            $sales = $this->saleRepository->findBySeller($sellerId);

            $this->setSales($sales);

            return true;
        } catch (\Exception $e) {
            Log::channel('seller_microservice')->error($e->getMessage(), ['sellerId' => $sellerId]);
            return false;
        }
    }

    /**
     * Obtém todas as vendas do Elasticsearch.
     *
     * @return bool
     */
    public function fetchAllSales(): bool
    {
        try {
            $sales = $this->saleRepository->findAll();

            $this->setSales($sales);

            return true;
        } catch (\Exception $e) {
            Log::channel('seller_microservice')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Obtém todas as vendas do Elasticsearch.
     *
     * @return void
     */
    public function fetchAllSalesFromElastic(): void
    {
        $elasticsearchService = new ElasticsearchService();
        $data = $elasticsearchService->fetchAllSales();

        $this->setSales($data);
    }
}
