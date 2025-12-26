<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AgendaController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/disponibilidad', [AgendaController::class, 'disponibilidad']);
    Route::get('/citas', [AgendaController::class, 'index']);
    Route::post('/citas', [AgendaController::class, 'store']);
    Route::post('/citas/{id}/cancelar', [AgendaController::class, 'cancelar']);
    Route::post('/logout', [AuthController::class, 'logout']);
});