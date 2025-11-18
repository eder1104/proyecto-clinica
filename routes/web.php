<?php

use App\Http\Controllers\{
    PlantillaControllerOptometria,
    PlantillaControllerExamenes,
    ProfileController,
    UserController,
    CitaController,
    PacienteController,
    HistoriaClinicaController,
    Auth\RegisteredUserController,
    PreExamenController,
    CalendarioController,
    ConsentimientoController,
    BitacoraAuditoriaController,
    PlantillaControllerRetina,
    CitasParcialController,
    CalendarioEspecialistaController,
    DoctorAgendaController,
    CatalogoController,
    ReporteAgendaController,
};
use App\Http\Middleware\Bitacora;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', Bitacora::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('citas/{cita}/examen', [PreExamenController::class, 'examen'])->name('citas.examen');

Route::middleware(['auth', 'checkrole:doctor,callcenter,admisiones', Bitacora::class])->group(function () {
    Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
    Route::get('pacientes/crear', [PacienteController::class, 'create'])->name('pacientes.create');
    Route::post('pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/buscar', [PacienteController::class, 'buscar'])->name('pacientes.buscar');
    Route::get('/pacientes/buscar/lista', [PacienteController::class, 'Paciente_buscar'])->name('pacientes.buscar.lista');
    Route::get('pacientes/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show');
    Route::get('pacientes/{paciente}/edit', [PacienteController::class, 'edit'])->name('pacientes.edit');
    Route::put('pacientes/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');
    Route::delete('pacientes/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
    Route::put('/pacientes/{id}/actualizar', [PacienteController::class, 'actualizarApi'])->name('pacientes.actualizarApi');

    Route::get('citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('citas/create', [CitaController::class, 'create'])->name('citas.create');
    Route::post('citas', [CitaController::class, 'store'])->name('citas.store');
    Route::get('citas/{cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');
    Route::put('citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('citas/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');
    Route::get('citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
    Route::patch('citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
    Route::get('citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
    Route::post('citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
    Route::get('citas/cancelar/{id}', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('citas/{cita}/atencion_update', [CitaController::class, 'atencion_update'])->name('citas.atencion_update');

    Route::prefix('citas/{cita}')->group(function () {
        Route::get('preexamen/{cita_id}/create', [PreExamenController::class, 'create'])->name('preexamen.create');
        Route::post('preexamen/{cita_id}', [PreExamenController::class, 'store'])->name('preexamen.store');
        Route::get('preexamen/{id}/examen', [PreExamenController::class, 'examen'])->name('citas.examen');

        Route::post('examenes/store', [PlantillaControllerExamenes::class, 'store'])->name('examenes.store');
        Route::get('examenes/edit', [PlantillaControllerExamenes::class, 'edit'])->name('examenes.edit');
        Route::put('examenes/update', [PlantillaControllerExamenes::class, 'update'])->name('examenes.update');
        Route::get('examenes/show', [PlantillaControllerExamenes::class, 'show'])->name('examenes.show');
        Route::delete('examenes/destroy', [PlantillaControllerExamenes::class, 'destroy'])->name('examenes.destroy');

        Route::get('plantilla/optometria', [PlantillaControllerOptometria::class, 'index'])->name('plantillas.optometria');
        Route::get('optometria/edit', [PlantillaControllerOptometria::class, 'edit'])->name('optometria.edit');
        Route::post('optometria/store', [PlantillaControllerOptometria::class, 'store'])->name('optometria.store');
        Route::put('optometria/update', [PlantillaControllerOptometria::class, 'update'])->name('optometria.update');
        Route::get('optometria/show', [PlantillaControllerOptometria::class, 'show'])->name('optometria.show');
        Route::delete('optometria/destroy', [PlantillaControllerOptometria::class, 'destroy'])->name('optometria.destroy');
    });

    Route::get('/pacientes/{id}/historia/pdf', [CitaController::class, 'descargarHistoriaPdf'])->name('pacientes.historia.pdf');
    Route::get('/historias/historia', [HistoriaClinicaController::class, 'index'])->name('historias.index');
    Route::resource('historias', HistoriaClinicaController::class)->except(['index']);
    Route::get('/historias/cita/{paciente}', [HistoriaClinicaController::class, 'cita'])->name('historias.cita');

    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/citas/{fecha}', [CalendarioController::class, 'citasPorDia'])->name('calendario.citasPorDia');
});

Route::middleware(['auth', 'checkrole:admin', Bitacora::class])->group(function () {
    Route::get('/administracion', fn() => view('administracion'))->name('administracion');
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::get('/users/buscar/lista', [UserController::class, 'Usuario_buscar'])->name('users.buscar.lista');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update.role');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/consentimientos', [ConsentimientoController::class, 'create'])
        ->name('consentimientos.create');

    Route::post('/consentimientos', [ConsentimientoController::class, 'store'])
        ->name('consentimientos.store');
});


Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

Route::middleware(['auth', 'checkrole:admin,admisiones', Bitacora::class])->group(function () {
    Route::get('/bitacora', [BitacoraAuditoriaController::class, 'index'])->name('citas.bitacora');
    Route::get('/agenda-doctores', [DoctorAgendaController::class, 'index'])->name('citas.DoctorAgenda');
    Route::get('/calendario-especialista', [CalendarioEspecialistaController::class, 'index'])->name('citas.CalendarioEspecialista');
    Route::get('/calendario-especialista/{doctorId}/{mes}', [CalendarioEspecialistaController::class, 'obtenerCalendario'])->name('calendario.obtener');
    Route::post('/calendario-especialista/update', [CalendarioEspecialistaController::class, 'actualizarEstado'])->name('calendario.update');
    Route::get('/vista-parcial/{doctorId}/{fecha}', [CitasParcialController::class, 'index'])->name('citas.parcial');
    Route::post('/parcialidades', [CitasParcialController::class, 'store'])->name('citas.parcial.store');
    Route::put('/parcialidades/{doctorParcialidad}', [CitasParcialController::class, 'update'])->name('citas.parcial.update');
    Route::delete('/parcialidades/{doctorParcialidad}', [CitasParcialController::class, 'destroy'])->name('citas.parcial.destroy');
});



Route::get('/citas/{cita}/retina', [PlantillaControllerRetina::class, 'index'])->name('retina.index');
Route::post('/citas/{cita}/retina', [PlantillaControllerRetina::class, 'store'])->name('retina.store');
Route::middleware([])->get('/catalogos/buscar', [CatalogoController::class, 'buscar'])->name('catalogos.buscar');
Route::get('/preexamen/create/{cita_id}', [PreExamenController::class, 'create'])->name('preexamen.create');
Route::get('/citas/reporte', [ReporteAgendaController::class, 'index'])->name('citas.reporte');
Route::get('/catalogos', function () {
    return view('citas.catalogos');
})->name('catalogos.index');


require __DIR__ . '/auth.php';
