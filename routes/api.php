<?php

use Illuminate\Support\Facades\Route;


Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('/orders')->group(function () {
        Route::get('/', [App\Http\Controllers\OrderController::class, 'index']);
        Route::post('/', [App\Http\Controllers\OrderController::class, 'store']);
        Route::get('/getOrder/{id}', [App\Http\Controllers\OrderController::class, 'show']);
        Route::put('/update/{id}', [App\Http\Controllers\OrderController::class, 'update']);

        Route::patch('/updateStatusOrder/{id}', [App\Http\Controllers\OrderController::class, 'updateStatusOrder']);

        Route::get('/orderStatus', [App\Http\Controllers\OrderStatusController::class, 'index']);

    });
});