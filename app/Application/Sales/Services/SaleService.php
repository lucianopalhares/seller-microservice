<?php

namespace App\Application\Sales\Services;

use App\Domain\Sales\SaleRepository;
use App\Domain\Sales\Sale;
use App\Domain\Sellers\SellerRepository;
use Elastic\Elasticsearch\ClientBuilder;
use App\Services\ResponseService;
use App\Exceptions\CustomException;
use App\Enums\StatusCodeEnum;

class SaleService
{
    private SaleRepository $saleRepository;
    private SellerRepository $sellerRepository;

    public function __construct(SaleRepository $saleRepository, SellerRepository $sellerRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->sellerRepository = $sellerRepository;
    }

    public function createSale(int $sellerId, float $value): object
    {
        try {
            $seller = $this->sellerRepository->findById($sellerId);

            if (!$seller) {
                throw new CustomException(StatusCodeEnum::NOT_FOUND, "Vendendo não encontrado.");
            }

            if ($value <= 0) {
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, "O valor da venda deve ser maior que zero.");
            }

            $commission = round($value * 0.085, 2);

            $sale = new Sale(0, $seller, $value, $commission);
            $save = $this->saleRepository->save($sale);

            return ResponseService::response(StatusCodeEnum::OK, $save);

        } catch (CustomException $e) {
            return ResponseService::responseError($e);
        }
    }

    public function getSalesBySeller(int $sellerId): array
    {
        return $this->saleRepository->findBySeller($sellerId);
    }

    /**
     * Obter todas as vendas do Elasticsearch
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getAllSales()
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

            return ResponseService::response($data);
        } catch (CustomException $e) {
            return ResponseService::responseError((object) $e);
        }
    }
}
