<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\HistoriaClinicaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Panel administración
    Route::get('/administracion', function () {
        return view('administracion');
    })->name('administracion');

    // Historias clínicas
    Route::get('/historia-clinica', [HistoriaClinicaController::class, 'index'])->name('historia.index');
    Route::get('/historia/buscar', [HistoriaClinicaController::class, 'buscar'])->name('historia.buscar');
    Route::resource('historias', HistoriaClinicaController::class)->except(['index']);
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    // Usuarios (solo admin)
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    Route::patch('/usuarios/{user}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
});

Route::middleware(['auth'])->group(function () {
    // Citas sin create, edit, show
    Route::resource('citas', CitaController::class)->except(['create', 'edit', 'show']);
    Route::resource('pacientes', PacienteController::class);
});

require __DIR__ . '/auth.php';
