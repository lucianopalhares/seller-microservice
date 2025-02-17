<?php

namespace App\Application\Sales;

use App\Application\Sales\Services\SaleService;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

/**
 * Controlador responsável por gerenciar vendas.
 */
class SalesController extends Controller
{
    /**
     * Serviço de vendas.
     *
     * @var SaleService
     */
    private SaleService $saleService;

    /**
     * Construtor do controlador.
     *
     * @param SaleService $saleService
     */
    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Criar uma nova venda.
     *
     * @param SaleRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function createSale(SaleRequest $request): JsonResponse
    {
        try {
            $sellerId = $request->input('seller_id');
            $saleValue = $request->input('sale_value');

            if (!$sellerId || !$saleValue) {
                throw new CustomException(StatusCodeEnum::BAD_REQUEST);
            }

            $created = $this->saleService->createSale($sellerId, $saleValue);

            if ($created === false)
                throw new CustomException(StatusCodeEnum::BAD_REQUEST);

            if ($this->saleService->errorExists())
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, $this->saleService->getError());

            $collection = $this->saleService->getSale();

            return ResponseService::responseJson(StatusCodeEnum::CREATED, new SaleResource($collection));
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }

    /**
     * Obter todas as vendas de um vendedor específico.
     *
     * @param int $sellerId
     * @return JsonResponse
     * @throws CustomException
     */
    public function getSalesBySeller(int $sellerId): JsonResponse
    {
        try {
            if (!$sellerId) {
                throw new CustomException(StatusCodeEnum::BAD_REQUEST);
            }

            $response = $this->saleService->fetchSalesBySeller($sellerId);

            if ($response === false)
                return ResponseService::responseJson(StatusCodeEnum::NO_CONTENT);

            if ($this->saleService->errorExists())
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, $this->saleService->getError());

            $sales = $this->saleService->getSales();

            if (count($sales) === 0) {
                return ResponseService::responseJson(StatusCodeEnum::NO_CONTENT);
            }

            return ResponseService::responseJson(StatusCodeEnum::OK, SaleResource::collection($sales));
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }

    /**
     * Obter todas as vendas do dia.
     *
     * @param int $sellerId
     * @return JsonResponse
     * @throws CustomException
     */
    public function getSalesOfTheDay(): JsonResponse
    {
        try {
            $this->saleService->getSalesOfTheDay();

            if ($this->saleService->errorExists())
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, $this->saleService->getError());

            $sales = $this->saleService->getSales();

            if (count($sales) === 0) {
                return ResponseService::responseJson(StatusCodeEnum::NO_CONTENT);
            }

            return ResponseService::responseJson(StatusCodeEnum::OK, SaleResource::collection($sales));
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }

    /**
     * Obter todas as vendas do elasticsearch.
     *
     * @param int $sellerId
     * @return JsonResponse
     * @throws CustomException
     */
    public function fetchAllSalesFromElastic(): JsonResponse
    {
        try {
            $this->saleService->fetchAllSalesFromElastic();

            if ($this->saleService->errorExists())
                throw new CustomException(StatusCodeEnum::BAD_REQUEST, $this->saleService->getError());

            $sales = $this->saleService->getSales();

            if (count($sales) === 0) {
                return ResponseService::responseJson(StatusCodeEnum::NO_CONTENT);
            }

            return ResponseService::responseJson(StatusCodeEnum::OK, $sales);
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }
}
