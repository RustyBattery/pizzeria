<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserAddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware(['auth:sanctum', 'token.refresh']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'token.access']);
});

Route::prefix('catalog')->group(function () {
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('product', [ProductController::class, 'index']);
    Route::get('product/{product}', [ProductController::class, 'get']);
});

Route::prefix('cart')->middleware(['auth:sanctum', 'token.access'])->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::prefix('product/{product}')->group(function () {
        Route::post('/', [CartController::class, 'addProduct']);
        Route::delete('/', [CartController::class, 'removeProduct']);
        Route::put('/', [CartController::class, 'changeCount']);
    });
});

Route::prefix('order')->middleware(['auth:sanctum', 'token.access'])->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{order}', [OrderController::class, 'get']);
    Route::post('/', [OrderController::class, 'create']);
    Route::delete('/{order}', [OrderController::class, 'cancel']);
});

Route::prefix('profile')->middleware(['auth:sanctum', 'token.access'])->group(function () {
    Route::prefix('address')->group(function () {
        Route::get('/', [UserAddressController::class, 'index']);
        Route::post('/', [UserAddressController::class, 'create']);
        Route::put('/{address}', [UserAddressController::class, 'update']);
        Route::delete('/{address}', [UserAddressController::class, 'delete']);
    });

});
