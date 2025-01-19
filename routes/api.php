<?php

use Illuminate\Support\Facades\Route;
use App\Application\Sellers\SellersController;
use App\Application\Sales\SalesController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('sellers')->group(function () {
        Route::post('/', [SellersController::class, 'createSeller']);
        Route::get('/', [SellersController::class, 'getAllSellers']);
    });

    Route::prefix('sales')->group(function () {
        Route::post('/', [SalesController::class, 'createSale']);
        Route::get('{id}', [SalesController::class, 'getSalesBySeller']);
    });

    Route::get('/sales-all', [SalesController::class, 'getAllSales']);
    Route::get('/sales-elastic', [SalesController::class, 'fetchAllSalesFromElastic']);
});

