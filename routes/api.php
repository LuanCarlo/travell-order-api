<?php

use Illuminate\Support\Facades\Route;



Route::get('/ping', function () {
    return response()->json(['message' => 'API ativa! 🚀']);
});

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);