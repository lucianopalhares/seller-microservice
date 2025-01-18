<?php

namespace App\Application\Sales\Services;

use App\Domain\Sales\SaleRepository;
use App\Domain\Sales\Sale;
use App\Domain\Sellers\SellerRepository;
use Elastic\Elasticsearch\ClientBuilder;

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
     * @var object
     */
    private object $sales;

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
     * @param object $sales
     * @return void
     */
    public function setSales(object $sales): void
    {
        $this->sales = $sales;
    }

    /**
     * Obtém o conjunto de vendas.
     *
     * @return object
     */
    public function getSales(): object
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
            $save = $this->saleRepository->save($sale);

            $this->setSale($save);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtém as vendas de um vendedor específico.
     *
     * @param int $sellerId ID do vendedor.
     * @return array
     */
    public function getSalesBySeller(int $sellerId): array
    {
        return $this->saleRepository->findBySeller($sellerId);
    }

    /**
     * Obtém todas as vendas do Elasticsearch.
     *
     * @return bool
     * @throws \Exception
     */
    public function fetchSales(): bool
    {
        try {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

            $params = [
                'scroll' => '30s',
                'size'   => 50,
                'index' => 'sales',
                'body'   => [
                    'query' => [
                        'match_all' => new \stdClass()
                    ]
                ]
            ];

            $response = $client->search($params);

            $data = $response['hits']['hits'];

            $this->setSales((object) $data);

            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
