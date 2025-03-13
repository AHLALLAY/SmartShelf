<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\RayonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/product/create', [ProductController::class, 'createProduct']);
Route::post('/pruduct/update/{id}', [ProductController::class, 'updateProduct']);
Route::post('/pruduct/delete/{id}', [ProductController::class, 'deleteProduct']);
Route::get('/product/display/available', [ProductController::class, 'displayProductAvailable']);
Route::post('/product/sale/{id}', [ProductController::class, 'saleProduct']);
Route::get('/product/display/populare', [ProductController::class, 'displayProductPopulare']);
Route::get('/product/display/promotion', [ProductController::class, 'displayProductPromo']);
Route::get('/products/search', [ProductController::class, 'search']);


Route::post('/rayon/create', [RayonController::class, 'makeRayon']);
Route::post('/rayon/update/{id}', [RayonController::class, 'updateRayon']);
Route::post('/rayon/delete/{id}', [RayonController::class, 'deleteRayon']);