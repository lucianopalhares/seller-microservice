<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SaleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('sellers', [SellerController::class, 'store']);
Route::get('sellers', [SellerController::class, 'index']);

Route::post('sales', [SaleController::class, 'store']);
Route::get('sales/{id}', [SaleController::class, 'salesBySeller']);
