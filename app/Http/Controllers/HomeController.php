<?php

// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Application\Sales\Services\SaleService;
use App\Http\Resources\SaleResource;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial.
     *
     * @return \Illuminate\View\View
     */
    public function index(SaleService $saleService)
    {
        //return view('welcome');

        $saleService->fetchAllSales();

        $sales = $saleService->getSales();

        if (empty($sales)) {
            echo 'erro';
            return;
        }

        $sales2 = [];

        foreach ($sales as $sale) {
            try {
                $sale333 = new SaleResource($sale);
                $array = $sale333->toArray(request());
                $sales2[] = $array;

            } catch (\Exception $e) {
                // Registrar erro específico ao publicar a mensagem
                echo 'erro2';
            }
        }

        echo 'foi';
    }
}
