<?php

use App\Http\Controllers\AuthController;
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

Route::prefix('auth')->group(function (){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware(['auth:sanctum', 'token.refresh']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'token.access']);
    //ToDo убрать (тестовый роут)
    Route::middleware(['auth:sanctum', 'token.access'])->get('user', function (){
        return response(['user' => auth()->user()], 200);
    });
});
