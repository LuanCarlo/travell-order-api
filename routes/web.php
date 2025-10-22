<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Route::prefix('api/orders')->group(function () {
//     Route::get('/', [App\Http\Controllers\OrderController::class, 'index']);
//     Route::post('/', [App\Http\Controllers\OrderController::class, 'store']);
// });