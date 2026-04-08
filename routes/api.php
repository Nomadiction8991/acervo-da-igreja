<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicIgrejaController;
use App\Http\Controllers\Api\V1\IgrejaApiController;
use Illuminate\Support\Facades\Route;

// Legacy public routes
Route::get('/igrejas', [PublicIgrejaController::class, 'index'])->name('api.igrejas.index');
Route::get('/igrejas/{igreja}', [PublicIgrejaController::class, 'show'])->name('api.igrejas.show');

// Auth routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// API V1 routes (protected)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('igrejas', IgrejaApiController::class);
});
