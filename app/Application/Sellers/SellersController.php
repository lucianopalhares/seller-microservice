<?php

namespace App\Application\Sellers;

use App\Application\Sellers\Services\SellerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRequest;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Http\Resources\SellerResource;

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
     * @throws Exception
     */
    public function createSeller(SellerRequest $request): JsonResponse
    {
        try {
            $name = $request->input('name');
            $email = $request->input('email');

            $seller = $this->sellerService->createSeller($name, $email);

            return response()->json(new SellerResource($seller), 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to create seller',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pegar todos vendedores
     *
     * @return JsonResponse
     */
    public function getAllSellers(): JsonResponse
    {
        try {
            $sellers = $this->sellerService->getAllSellersWithCommission();
            return response()->json(SellerResource::collection($sellers));
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch sellers',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
