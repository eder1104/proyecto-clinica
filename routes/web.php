<?php

use App\Http\Controllers\{
    PlantillaControllerOptometria,
    PlantillaControllerExamenes,
    ProfileController,
    UserController,
    CitaController,
    PacienteController,
    HistoriaClinicaController,
    PreExamenController
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

    Route::get('/administracion', fn() => view('administracion'))->name('administracion');

    Route::get('/historias/historia', [HistoriaClinicaController::class, 'index'])->name('historias.index');
    Route::resource('historias', HistoriaClinicaController::class)->except(['index']);
    Route::get('/historias/cita/{paciente}', [HistoriaClinicaController::class, 'cita'])->name('historias.cita');
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show']);
    Route::patch('/usuarios/{user}', [UserController::class, 'toggleStatus'])->name('users.toggle');
});

Route::middleware('auth')->group(function () {

    Route::resource('citas', CitaController::class)->except(['show']);
    Route::get('/citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
    Route::patch('/citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
    Route::get('/citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
    Route::post('/citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/{cita}/atencion_update', [CitaController::class, 'atencion_update'])->name('citas.atencion_update');

    Route::prefix('citas/{cita}')->group(function () {
        Route::get('preexamen/create', [PreExamenController::class, 'create'])->name('preexamen.create');
        Route::post('preexamen', [PreExamenController::class, 'store'])->name('preexamen.store');
        Route::get('preexamen/show', [PreExamenController::class, 'show'])->name('preexamen.show');
        Route::get('preexamen/examen', [PreExamenController::class, 'examen'])->name('preexamen.examen');

        Route::get('plantilla/optometria', [PlantillaControllerOptometria::class, 'index'])->name('plantillas.optometria');
        Route::get('optometria/edit', [PlantillaControllerOptometria::class, 'edit'])->name('optometria.edit');
        Route::post('optometria/store', [PlantillaControllerOptometria::class, 'store'])->name('optometria.store');
        Route::put('optometria/update', [PlantillaControllerOptometria::class, 'update'])->name('optometria.update');
        Route::get('optometria/show', [PlantillaControllerOptometria::class, 'show'])->name('optometria.show');
        Route::delete('optometria/destroy', [PlantillaControllerOptometria::class, 'destroy'])->name('optometria.destroy');

        Route::post('examenes/store', [PlantillaControllerExamenes::class, 'store'])->name('examenes.store');
        Route::get('examenes/edit', [PlantillaControllerExamenes::class, 'edit'])->name('examenes.edit');
        Route::put('examenes/update', [PlantillaControllerExamenes::class, 'update'])->name('examenes.update');
        Route::get('examenes/show', [PlantillaControllerExamenes::class, 'show'])->name('examenes.show');
        Route::delete('examenes/destroy', [PlantillaControllerExamenes::class, 'destroy'])->name('examenes.destroy');
    });

    Route::resource('pacientes', PacienteController::class)->except(['show']);
    Route::get('/pacientes/{id}/historia/pdf', [CitaController::class, 'descargarHistoriaPdf'])->name('pacientes.historia.pdf');

    Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->name('register.store');
});

require __DIR__ . '/auth.php';
