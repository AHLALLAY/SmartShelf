<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/product/create', [ProductController::class, 'createProduct'])->name('new-product');
Route::get('/product/display/available', [ProductController::class, 'displayProductAvailable'])->name('available-product');
Route::post('/product/sale/{id}', [ProductController::class, 'saleProduct']);