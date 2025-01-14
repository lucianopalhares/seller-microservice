<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('sellers', [SellerController::class, 'store'])->middleware('auth:sanctum');
Route::get('sellers', [SellerController::class, 'index'])->middleware('auth:sanctum');

Route::post('sales', [SaleController::class, 'store'])->middleware('auth:sanctum');
Route::get('sales/{id}', [SaleController::class, 'salesBySeller'])->middleware('auth:sanctum');
