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
    public function index()
    {
        return view('welcome');
    }
}
