<?php

use App\Http\Controllers\{
    PlantillaControllerOptometria,
    PlantillaControllerExamenes,
    ProfileController,
    UserController,
    CitaController,
    PacienteController,
    HistoriaClinicaController,
    PreExamenController,
    CalendarioController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));


Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pacientes/buscar', [PacienteController::class, 'buscar'])->name('pacientes.buscar');
    Route::put('/pacientes/{id}/actualizar', [PacienteController::class, 'actualizarApi'])->name('pacientes.actualizarApi');
});


Route::middleware(['auth', 'checkrole:doctor,callcenter,admisiones,admin'])->group(function () {
    // Pacientes
    Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
    Route::get('pacientes/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show');
    Route::get('/pacientes/buscar/lista', [PacienteController::class, 'Paciente_buscar'])->name('pacientes.buscar.lista');

    Route::get('pacientes/{paciente}/edit', [PacienteController::class, 'edit'])->name('pacientes.edit');
    Route::put('pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');

    Route::delete('pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    Route::get('/administracion', fn() => view('administracion'))->name('administracion');

    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::get('/users/buscar/lista', [UserController::class, 'Usuario_buscar'])->name('users.buscar.lista'); 
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update.role');

    Route::resource('pacientes', PacienteController::class)->only(['create', 'store']);
});

Route::middleware(['auth', 'checkrole:doctor,callcenter,admisiones'])->group(function () {
    Route::resource('citas', CitaController::class)->except(['show']);

    Route::get('/citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
    Route::patch('/citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
    Route::get('/citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
    Route::post('/citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
    Route::get('/citas/cancelar/{id}', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/{cita}/atencion_update', [CitaController::class, 'atencion_update'])->name('citas.atencion_update');

    Route::prefix('citas/{cita}')->group(function () {
        Route::get('preexamen/create', [PreExamenController::class, 'create'])->name('preexamen.create'); // Mostrar formulario
        Route::post('preexamen', [PreExamenController::class, 'store'])->name('preexamen.store'); // Guardar datos
        Route::get('preexamen/show', [PreExamenController::class, 'show'])->name('preexamen.show'); // Ver preexamen
        Route::get('preexamen/examen', [PreExamenController::class, 'examen'])->name('preexamen.examen'); // Examen específico

        Route::post('examenes/store', [PlantillaControllerExamenes::class, 'store'])->name('examenes.store');
        Route::get('examenes/edit', [PlantillaControllerExamenes::class, 'edit'])->name('examenes.edit');
        Route::put('examenes/update', [PlantillaControllerExamenes::class, 'update'])->name('examenes.update');
        Route::get('examenes/show', [PlantillaControllerExamenes::class, 'show'])->name('examenes.show');
        Route::delete('examenes/destroy', [PlantillaControllerExamenes::class, 'destroy'])->name('examenes.destroy');
    });

    Route::get('/pacientes/{id}/historia/pdf', [CitaController::class, 'descargarHistoriaPdf'])->name('pacientes.historia.pdf');
    Route::get('/historias/historia', [HistoriaClinicaController::class, 'index'])->name('historias.index');
    Route::resource('historias', HistoriaClinicaController::class)->except(['index']);
    Route::get('/historias/cita/{paciente}', [HistoriaClinicaController::class, 'cita'])->name('historias.cita');

    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/citas/{fecha}', [CalendarioController::class, 'citasPorDia'])->name('calendario.citasPorDia');
});


Route::middleware(['auth', 'checkrole:doctor'])->group(function () {
    Route::prefix('citas/{cita}')->group(function () {
        Route::get('plantilla/optometria', [PlantillaControllerOptometria::class, 'index'])->name('plantillas.optometria');
        Route::get('optometria/edit', [PlantillaControllerOptometria::class, 'edit'])->name('optometria.edit');
        Route::post('optometria/store', [PlantillaControllerOptometria::class, 'store'])->name('optometria.store');
        Route::put('optometria/update', [PlantillaControllerOptometria::class, 'update'])->name('optometria.update');
        Route::get('optometria/show', [PlantillaControllerOptometria::class, 'show'])->name('optometria.show');
        Route::delete('optometria/destroy', [PlantillaControllerOptometria::class, 'destroy'])->name('optometria.destroy');
    });
});


Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->name('register.store');


require __DIR__ . '/auth.php';
