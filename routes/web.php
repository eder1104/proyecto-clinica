<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BitacoraAuditoriaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\CalendarioEspecialistaController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CitasBloqueadoController;
use App\Http\Controllers\CitasParcialController;
use App\Http\Controllers\ConsentimientoController;
use App\Http\Controllers\DoctorAgendaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\LegacyPacienteController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PlantillaControllerExamenes;
use App\Http\Controllers\PlantillaControllerOptometria;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteAgendaController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Bitacora;
use App\Jobs\EnviarRecordatorioCita;
use App\Models\Cita;
use App\Models\RecordatorioCita;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use app\html\middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));


Route::middleware(['auth', Bitacora::class])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->middleware('verified')->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::middleware(['checkrole:admin'])->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::patch('/users/{user}/toggle', 'toggleStatus')->name('users.toggle');
            Route::get('/users/buscar/lista', 'Usuario_buscar')->name('users.buscar.lista');
            Route::patch('/users/{user}/role', 'updateRole')->name('users.update.role');
        });
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware(['checkrole:admin,admisiones'])->group(function () {
        Route::get('/bitacora', [BitacoraAuditoriaController::class, 'index'])->name('citas.bitacora');
        Route::get('/agenda-doctores', [DoctorAgendaController::class, 'index'])->name('citas.DoctorAgenda');
        
        Route::controller(CalendarioEspecialistaController::class)->group(function () {
            Route::get('/calendario-especialista', 'index')->name('citas.CalendarioEspecialista');
            Route::get('/calendario-especialista/{doctorId}/{mes}', 'obtenerCalendario')->name('calendario.obtener');
            Route::post('/calendario-especialista/update', 'actualizarEstado')->name('calendario.update');
            Route::get('/bloqueo-especialista/{doctorId}/{fecha}', 'vistaBloqueo')->name('citas.bloqueado');
        });

        Route::post('/bloqueo-especialista/store', [CitasBloqueadoController::class, 'store'])->name('citas.bloqueado.store');
        Route::delete('/bloqueo-especialista/{bloqueoAgenda}', [CitasBloqueadoController::class, 'destroy'])->name('citas.bloqueado.destroy');

        Route::get('/citas/reporte', [ReporteAgendaController::class, 'index'])->name('citas.reporte');

        Route::controller(CitasParcialController::class)->group(function () {
            Route::get('/vista-parcial/{doctorId}/{fecha}', 'index')->name('citas.parcial');
            Route::post('/parcialidades', 'store')->name('citas.parcial.store');
            Route::put('/parcialidades/{doctorParcialidad}', 'update')->name('citas.parcial.update');
            Route::delete('/parcialidades/{doctorParcialidad}', 'destroy')->name('citas.parcial.destroy');
        });
    });

    Route::middleware(['checkrole:doctor,admisiones,admin'])->group(function () {
        Route::controller(LegacyPacienteController::class)->group(function () {
            Route::get('/convenios/{id}/planes', 'getPlanes');
            Route::get('/legacy/pacientes', 'index_legacy')->name('legacy.index_legacy');
            Route::post('/legacy/citas/registrar', [LegacyPacienteController::class, 'storeCita'])->name('legacy.citas.store');
            Route::get('/legacy/buscar', 'buscar')->name('legacy.buscar');
            Route::post('/legacy/agendar', 'agendar')->name('legacy.agendar');
            Route::post('/legacy/pacientes/store', 'store')->name('legacy.pacientes.store');
            Route::put('/legacy/pacientes/{id}', 'update')->name('legacy.pacientes.update');
            Route::post('/legacy/pacientes/importar', 'importarCSV')->name('legacy.pacientes.importar');
        });
    });

    Route::middleware(['checkrole:doctor,callcenter,admisiones'])->group(function () {
        
        Route::controller(PacienteController::class)->group(function () {
            Route::get('/pacientes/buscar', 'buscar')->name('pacientes.buscar');
            Route::get('/pacientes/buscar/lista', 'Paciente_buscar')->name('pacientes.buscar.lista');
            Route::put('/pacientes/{id}/actualizar', 'actualizarApi')->name('pacientes.actualizarApi');
        });
        
        Route::resource('pacientes', PacienteController::class);

        Route::controller(CitaController::class)->group(function () {
            Route::get('citas/cancelar/{id}', 'cancelar')->name('citas.cancelar');
            Route::get('citas/{cita}/atencion', 'atencion')->name('citas.atencion');
            Route::get('citas/{cita}/atencion_update', 'atencion_update')->name('citas.atencion_update');
            Route::patch('citas/{cita}/motivo', 'updateMotivo')->name('citas.updateMotivo');
            Route::get('citas/{cita}/pdf', 'pdf')->name('citas.pdf');
            Route::post('citas/{cita}/finalizar', 'finalizar')->name('citas.finalizar');
            Route::get('/pacientes/{id}/historia/pdf', 'descargarHistoriaPdf')->name('pacientes.historia.pdf');
        });
        Route::resource('citas', CitaController::class);

        Route::controller(ConsentimientoController::class)->group(function () {
            Route::get('/consentimientos', 'create')->name('consentimientos.create');
            Route::post('/consentimientos', 'store')->name('consentimientos.store');
            Route::get('/consentimientos/generar', 'generar')->name('consentimientos.generar');
        });

        Route::prefix('citas/{cita}')->group(function () {
            Route::controller(PlantillaControllerExamenes::class)->group(function () {
                Route::post('examenes/store', 'store')->name('examenes.store');
                Route::get('examenes/edit', 'edit')->name('examenes.edit');
                Route::put('examenes/update', 'update')->name('examenes.update');
                Route::get('examenes/index', 'index')->name('examenes.index');
                Route::delete('examenes/destroy', 'destroy')->name('examenes.destroy');
            });

            Route::controller(PlantillaControllerOptometria::class)->group(function () {
                Route::get('plantilla/optometria', 'index')->name('plantillas.optometria');
                Route::get('optometria/edit', 'edit')->name('optometria.edit');
                Route::post('optometria/store', 'store')->name('optometria.store');
                Route::put('optometria/update', 'update')->name('optometria.update');
                Route::get('optometria/show', 'show')->name('optometria.show');
                Route::delete('optometria/destroy', 'destroy')->name('optometria.destroy');
            });
        });

        Route::controller(HistoriaClinicaController::class)->group(function () {
            Route::get('/historias/historia', 'index')->name('historias.index');
            Route::get('/historias/cita/{paciente}', 'cita')->name('historias.cita');
        });
        Route::resource('historias', HistoriaClinicaController::class)->except(['index']);

        Route::controller(CalendarioController::class)->group(function () {
            Route::get('/calendario', 'index')->name('calendario.index');
            Route::get('/calendario/citas/{fecha}', 'citasPorDia')->name('calendario.citasPorDia');
            Route::get('/calendario/estado-dia/{fecha}', 'estadoDia')->name('calendario.estadoDia');
        });

        Route::controller(CatalogoController::class)->group(function () {
            Route::get('/catalogos/buscar-diagnosticos', 'buscarDiagnosticos')->name('catalogos.buscarDiagnosticos');
            Route::get('/catalogos/buscar-procedimientos', 'buscarProcedimientos')->name('catalogos.buscarProcedimientos');
            Route::get('/catalogos/buscar-alergias', 'buscarAlergias')->name('catalogos.buscarAlergias');
        });
    });
});

require __DIR__ . '/auth.php';