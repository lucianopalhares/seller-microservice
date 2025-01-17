<?php

namespace App\Application\Sellers;

use App\Application\Sellers\Services\SellerService;
use App\Enums\StatusCodeEnum;
use App\Exceptions\CustomException;
use App\Application\BaseController;
use App\Http\Requests\SellerRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SellerResource;

class SellersController extends BaseController
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

            return $this->responseJson(StatusCodeEnum::CREATED, new SellerResource($seller));
        } catch (CustomException $e) {
            return $this->responseJsonError($e);
        }
    }

    /**
     * Obter todos vendedores com suas comissões
     *
     * @return JsonResponse
     */
    public function getAllSellers(): JsonResponse
    {
        try {
            $sellers = $this->sellerService->getAllSellersWithCommission();

            if (count($sellers) === 0) {
                return $this->responseJson(StatusCodeEnum::NO_CONTENT);
            }

            if (!auth('api')->check()) {
                return response()->json([
                    'message' => 'Usuário não autenticado no guard API.',
                ], 401);
            }

            return $this->responseJson(StatusCodeEnum::OK, SellerResource::collection($sellers));
        } catch (CustomException $e) {
            return $this->responseJsonError($e);
        }
    }
}
