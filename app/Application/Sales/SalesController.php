<?php
namespace App\Application\Sales;

use App\Application\Sales\Services\SaleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;

class SalesController extends Controller
{
    private SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function createSale(Request $request)
    {
        $sellerId = $request->input('seller_id');
        $saleValue = $request->input('sale_value');

        $sale = $this->saleService->createSale($sellerId, $saleValue);

        return response()->json(new SaleResource($sale), 201);
    }

    public function getSalesBySeller($sellerId)
    {
        $sales = $this->saleService->getSalesBySeller($sellerId);

        return response()->json(SaleResource::collection($sales));
    }
}
