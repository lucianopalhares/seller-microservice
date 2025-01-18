<?php

namespace App\Application\Sales\Services;

use App\Domain\Sales\SaleRepository;
use App\Domain\Sales\Sale;
use App\Domain\Sellers\SellerRepository;
use Elastic\Elasticsearch\ClientBuilder;

class SaleService
{
    private SaleRepository $saleRepository;
    private SellerRepository $sellerRepository;
    private Sale $sale;
    private Object $sales;
    private string $error;

    public function __construct(SaleRepository $saleRepository, SellerRepository $sellerRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->sellerRepository = $sellerRepository;
    }

    public function createSale(int $sellerId, float $value): bool
    {
        try {
            $seller = $this->sellerRepository->findById($sellerId);

            if (!$seller) {
                $this->setError("Vendendo não encontrado.");
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

    public function setSale(Sale $sale): void
    {
        $this->sale = $sale;
    }

    public function getSale(): Sale
    {
        return $this->sale;
    }

    public function setSales(object $sales): void
    {
        $this->sales = $sales;
    }

    public function getSales(): object
    {
        return $this->sales;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function errorExists(): bool
    {
        return empty($this->error) === false;
    }

    public function getSalesBySeller(int $sellerId): array
    {
        return $this->saleRepository->findBySeller($sellerId);
    }

    /**
     * Obter todas as vendas do Elasticsearch
     *
     * @return bool
     * @throws Exception
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
