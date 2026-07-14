<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('customers', \App\Http\Controllers\Api\CustomerController::class)->only(['index', 'show', 'store']);
Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)->only(['index', 'show', 'store']);
Route::apiResource('orders', \App\Http\Controllers\Api\OrderController::class)->only(['index', 'show', 'store']);
