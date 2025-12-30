<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AgendaController;

Route::prefix('v1')->group(function () {
    Route::get('/disponibilidad', [AgendaController::class, 'disponibilidad']);
    Route::get('/citas', [AgendaController::class, 'index']);
    Route::post('/citas', [AgendaController::class, 'store']);
    Route::delete('/citas/{id}/cancelar', [AgendaController::class, 'cancelar']);
});