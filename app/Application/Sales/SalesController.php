<?php
namespace App\Application\Sales;

use App\Application\Sales\Services\SaleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use Illuminate\Http\JsonResponse;
use Exception;

class SalesController extends Controller
{
    private SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Criar nova venda
     *
     * @param SaleRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createSale(SaleRequest $request)
    {
        try {
            $sellerId = $request->input('seller_id');
            $saleValue = $request->input('sale_value');

            $sale = $this->saleService->createSale($sellerId, $saleValue);

            return response()->json(new SaleResource($sale), 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to create seller',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pegar todas as vendas de um vendedor especifico.
     *
     * @param int $sellerId
     * @return JsonResponse
     */
    public function getSalesBySeller($sellerId): JsonResponse
    {
        try {
            $sales = $this->saleService->getSalesBySeller($sellerId);

            return response()->json(SaleResource::collection($sales));
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao pegar vendas',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
