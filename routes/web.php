<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\PlantillaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/administracion', function () {
        return view('administracion');
    })->name('administracion');

    Route::get('/historias', [HistoriaClinicaController::class, 'index'])->name('historias.index');
    Route::get('/historia/buscar', [HistoriaClinicaController::class, 'buscar'])->name('historia.buscar');
    Route::resource('historias', HistoriaClinicaController::class)->except(['index']);
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/usuarios/{user}', [UserController::class, 'toggleStatus'])->name('users.toggle');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('/citas/create', [CitaController::class, 'create'])->name('citas.create');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/citas/{cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');
    Route::put('/citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('/citas/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');

    Route::get('/citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
    Route::patch('/citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
    Route::get('/citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
    Route::post('/citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/ModalPaciente', [CitaController::class, 'ModalPaciente'])->name('citas.ModalPaciente');
    Route::get('/citas/{cita}/examen', [CitaController::class, 'examen'])->name('citas.examen');
    Route::post('/citas/{cita}/guardar-examen', [CitaController::class, 'guardarExamen'])->name('citas.guardarExamen');

    Route::resource('citas', CitaController::class);

    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
    Route::get('/pacientes/create', [PacienteController::class, 'create'])->name('pacientes.create');
    Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/{paciente}/edit', [PacienteController::class, 'edit'])->name('pacientes.edit');
    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');
    Route::get('/pacientes/{id}/historia/pdf', [CitaController::class, 'descargarHistoriaPdf'])->name('pacientes.historia.pdf');

    Route::get('/optometria', [PlantillaController::class, 'index'])->name('optometria.index');
    Route::get('/optometria/{cita_id}', [PlantillaController::class, 'show'])->name('optometria.show');
    Route::post('/optometria', [PlantillaController::class, 'store'])->name('plantilla.store');
    Route::put('/optometria/{id}', [PlantillaController::class, 'update'])->name('plantilla.update');
});

require __DIR__ . '/auth.php';
