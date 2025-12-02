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
    CalendarioController,
    ConsentimientoController,
    BitacoraAuditoriaController,
    CitasParcialController,
    CitasBloqueadoController,
    CalendarioEspecialistaController,
    DoctorAgendaController,
    CatalogoController,
    ReporteAgendaController,
};
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\Bitacora;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::middleware(['auth', Bitacora::class])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/consentimientos', [ConsentimientoController::class, 'create'])->name('consentimientos.create');
    Route::post('/consentimientos', [ConsentimientoController::class, 'store'])->name('consentimientos.store');
    Route::get('/consentimientos/generar', [ConsentimientoController::class, 'generar'])->name('consentimientos.generar');

    Route::middleware(['checkrole:doctor,callcenter,admisiones'])->group(function () {

        Route::get('/pacientes/buscar', [PacienteController::class, 'buscar'])->name('pacientes.buscar');
        Route::get('/pacientes/buscar/lista', [PacienteController::class, 'Paciente_buscar'])->name('pacientes.buscar.lista');
        Route::put('/pacientes/{id}/actualizar', [PacienteController::class, 'actualizarApi'])->name('pacientes.actualizarApi');
        Route::resource('pacientes', PacienteController::class);

        Route::get('citas/cancelar/{id}', [CitaController::class, 'cancelar'])->name('citas.cancelar');
        Route::get('citas/{cita}/atencion', [CitaController::class, 'atencion'])->name('citas.atencion');
        Route::get('citas/{cita}/atencion_update', [CitaController::class, 'atencion_update'])->name('citas.atencion_update');
        Route::patch('citas/{cita}/motivo', [CitaController::class, 'updateMotivo'])->name('citas.updateMotivo');
        Route::get('citas/{cita}/pdf', [CitaController::class, 'pdf'])->name('citas.pdf');
        Route::post('citas/{cita}/finalizar', [CitaController::class, 'finalizar'])->name('citas.finalizar');
        Route::resource('citas', CitaController::class);

        Route::prefix('citas/{cita}')->group(function () {
            Route::post('examenes/store', [PlantillaControllerExamenes::class, 'store'])->name('examenes.store');
            Route::get('examenes/edit', [PlantillaControllerExamenes::class, 'edit'])->name('examenes.edit');
            Route::put('examenes/update', [PlantillaControllerExamenes::class, 'update'])->name('examenes.update');
            Route::get('examenes/index', [PlantillaControllerExamenes::class, 'index'])->name('examenes.index');
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
        Route::get('/historias/cita/{paciente}', [HistoriaClinicaController::class, 'cita'])->name('historias.cita');
        Route::resource('historias', HistoriaClinicaController::class)->except(['index']);

        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/calendario/citas/{fecha}', [CalendarioController::class, 'citasPorDia'])->name('calendario.citasPorDia');
        Route::get('/calendario/estado-dia/{fecha}', [CalendarioController::class, 'estadoDia'])->name('calendario.estadoDia');

        Route::get('/catalogos/buscar-diagnosticos', [CatalogoController::class, 'buscarDiagnosticos'])->name('catalogos.buscarDiagnosticos');
        Route::get('/catalogos/buscar-procedimientos', [CatalogoController::class, 'buscarProcedimientos'])->name('catalogos.buscarProcedimientos');
        Route::get('/catalogos/buscar-alergias', [CatalogoController::class, 'buscarAlergias'])->name('catalogos.buscarAlergias');
    });

    Route::middleware(['checkrole:admin'])->group(function () {
        Route::get('/administracion', fn() => view('administracion'))->name('administracion');

        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::get('/users/buscar/lista', [UserController::class, 'Usuario_buscar'])->name('users.buscar.lista');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update.role');
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware(['checkrole:admin,admisiones'])->group(function () {
        Route::get('/bitacora', [BitacoraAuditoriaController::class, 'index'])->name('citas.bitacora');

        Route::get('/agenda-doctores', [DoctorAgendaController::class, 'index'])->name('citas.DoctorAgenda');
        Route::get('/calendario-especialista', [CalendarioEspecialistaController::class, 'index'])->name('citas.CalendarioEspecialista');
        Route::get('/calendario-especialista/{doctorId}/{mes}', [CalendarioEspecialistaController::class, 'obtenerCalendario'])->name('calendario.obtener');
        Route::post('/calendario-especialista/update', [CalendarioEspecialistaController::class, 'actualizarEstado'])->name('calendario.update');

        Route::post('/bloqueo-especialista/store', [CitasBloqueadoController::class, 'store'])->name('citas.bloqueado.store');
        Route::get('/bloqueo-especialista/{doctorId}/{fecha}', [CalendarioEspecialistaController::class, 'vistaBloqueo'])->name('citas.bloqueado');
        Route::delete('/bloqueo-especialista/{doctorId}/{fecha}/{id}', [CitasBloqueadoController::class, 'destroy'])->name('citas.bloqueado.destroy');

        Route::get('/citas/reporte', [ReporteAgendaController::class, 'index'])->name('citas.reporte');

        Route::get('/vista-parcial/{doctorId}/{fecha}', [CitasParcialController::class, 'index'])->name('citas.parcial');
        Route::post('/parcialidades', [CitasParcialController::class, 'store'])->name('citas.parcial.store');
        Route::put('/parcialidades/{doctorParcialidad}', [CitasParcialController::class, 'update'])->name('citas.parcial.update');
        Route::delete('/parcialidades/{doctorParcialidad}', [CitasParcialController::class, 'destroy'])->name('citas.parcial.destroy');
    });
});

require __DIR__ . '/auth.php';
