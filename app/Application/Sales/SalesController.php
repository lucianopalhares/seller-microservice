<?php

namespace App\Application\Sales;

use App\Application\Sales\Services\SaleService;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use App\Application\BaseController;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use Illuminate\Http\JsonResponse;

/**
 * Controlador responsável por gerenciar vendas.
 */
class SalesController extends BaseController
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

            $sale = $this->saleService->createSale($sellerId, $saleValue);

            return $this->responseJson(StatusCodeEnum::CREATED, new SaleResource($sale));
        } catch (CustomException $e) {
            return $this->responseJsonError($e);
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

            $sales = $this->saleService->getSalesBySeller($sellerId);

            if (count($sales) === 0) {
                return $this->responseJson(StatusCodeEnum::NO_CONTENT);
            }

            return $this->responseJson(StatusCodeEnum::OK, SaleResource::collection($sales));
        } catch (CustomException $e) {
            return $this->responseJsonError($e);
        }
    }
}
