<?php

namespace App\Application\Sellers;

use App\Application\Sellers\Services\SellerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Create a new seller.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createSeller(Request $request): JsonResponse
    {
        try {
            // Validando dados de entrada
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:sellers,email',
            ]);

            $name = $request->input('name');
            $email = $request->input('email');

            // Chamada ao serviço para criar o vendedor
            $seller = $this->sellerService->createSeller($name, $email);

            return response()->json(new SellerResource($seller), 201);
        } catch (Exception $e) {
            // Tratando exceções e retornando um erro adequado
            return response()->json([
                'error' => 'Unable to create seller',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all sellers.
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
