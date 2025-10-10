<?php

use App\Http\Controllers\PlantillaControllerOptometria;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\PlantillaControllerExamenes;

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
    Route::resource('citas', CitaController::class)->except(['show']);

    Route::get('/citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
    Route::patch('/citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
    Route::get('/citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
    Route::post('/citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/ModalPaciente', [CitaController::class, 'ModalPaciente'])->name('citas.ModalPaciente');
    Route::get('/citas/{cita}/examen', [CitaController::class, 'examen'])->name('citas.examen');
    Route::post('/citas/{cita}/guardar-examen', [CitaController::class, 'guardarExamen'])->name('citas.guardarExamen');

    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
    Route::get('/pacientes/create', [PacienteController::class, 'create'])->name('pacientes.create');
    Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/{paciente}/edit', [PacienteController::class, 'edit'])->name('pacientes.edit');
    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');
    Route::get('/pacientes/{id}/historia/pdf', [CitaController::class, 'descargarHistoriaPdf'])->name('pacientes.historia.pdf');

    Route::get('/plantillas/optometria', [PlantillaControllerOptometria::class, 'index'])->name('plantillas.optometria');
    Route::get('/optometria/{cita}/crear', [PlantillaControllerOptometria::class, 'create'])->name('optometria.create');
    Route::get('/optometria/{plantilla}/editar', [PlantillaControllerOptometria::class, 'edit'])->name('plantilla.edit');
    Route::post('/optometria/{cita}', [PlantillaControllerOptometria::class, 'store'])->name('plantilla.store');
    Route::put('/optometria/{plantilla_optometria}', [PlantillaControllerOptometria::class, 'update'])->name('plantilla.update');


    Route::get('/plantillas/examenes', [PlantillaControllerExamenes::class, 'index'])->name('plantillas.examenes');
    Route::get('/plantillas/{cita}', [PlantillaControllerExamenes::class, 'store'])->name('plantillas.examenes');



    Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->name('register.store');
});

require __DIR__ . '/auth.php';
