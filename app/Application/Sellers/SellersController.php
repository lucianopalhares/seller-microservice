<?php

namespace App\Application\Sellers;

use App\Application\Sellers\Services\SellerService;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SellerResource;
use App\Services\ResponseService;

class SellersController extends Controller
{
    private SellerService $sellerService;

    /**
     * SellersController constructor.
     *
     * @param SellerService $sellerService
     */
    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    /**
     * Criar novo vendedor
     *
     * @param SellerRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function createSeller(SellerRequest $request): JsonResponse
    {
        try {
            $name = $request->input('name');
            $email = $request->input('email');

            $seller = $this->sellerService->createSeller($name, $email);

            return ResponseService::responseJson(StatusCodeEnum::CREATED, new SellerResource($seller));
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }

    /**
     * Obter todos vendedores com suas comissões
     *
     * @return JsonResponse
     */
    public function getAllSellers()
    {
        try {
            $sellers = $this->sellerService->getAllSellersWithCommission();

            if (count($sellers) === 0) {
                return ResponseService::responseJson(StatusCodeEnum::NO_CONTENT);
            }

            return ResponseService::responseJson(StatusCodeEnum::OK, SellerResource::collection($sellers));
        } catch (CustomException $e) {
            return ResponseService::responseJsonError($e);
        }
    }


}