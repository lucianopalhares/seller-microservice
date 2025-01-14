<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    /**
     * Criar venda
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'seller_id' => 'required|exists:sellers,id',
                'sale_value' => 'required|numeric|min:0',
            ]);

            $data = $request->all();

            $data['sale_commission'] = $request->sale_value * 0.085;

            $sale = Sale::create($data);

            return response()->json($sale, 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    /**
     * Pega vendas de um vendedor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function salesBySeller($id): JsonResponse
    {
        $sales = Sale::where('seller_id', $id)->get();
        return response()->json($sales);
    }
}
