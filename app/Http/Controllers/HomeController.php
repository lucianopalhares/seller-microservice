<?php

// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Application\Sales\Services\SaleService;

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
                $sales2[] = get_object_vars($sale);
            } catch (\Exception $e) {
                // Registrar erro específico ao publicar a mensagem
                echo 'erro2';
            }
        }

        echo 'foi';
    }
}
