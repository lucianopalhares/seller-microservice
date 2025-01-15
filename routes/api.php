<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('sellers')->group(function () {
        Route::post('/', [SellerController::class, 'store']);
        Route::get('/', [SellerController::class, 'index']);
    });

    Route::prefix('sales')->group(function () {
        Route::post('/', [SaleController::class, 'store']);
        Route::get('{id}', [SaleController::class, 'salesBySeller']);
    });
});


