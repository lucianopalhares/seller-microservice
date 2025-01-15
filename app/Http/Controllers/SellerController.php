<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SellerController extends Controller
{
    /**
     * Criar vendedor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:sellers,email',
        ]);

        $seller = Seller::create($request->all());

        return response()->json($seller, 201);
    }

    /**
     * Listar vendedores
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json(Seller::all());
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
