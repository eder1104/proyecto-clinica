<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\BloqueoAgenda;
use App\Models\User;
use Carbon\Carbon;

class ReporteAgendaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->toDateString());
        $horaInicio = '06:00:00';
        $horaFin = '20:00:00';

        $citas = Cita::where('fecha', $fecha)
            ->whereTime('hora_inicio', '>=', $horaInicio)
            ->whereTime('hora_fin', '<=', $horaFin)
            ->get();

        $programadas = $citas->where('estado', 'programada')->count();
        $canceladas = $citas->where('estado', 'cancelada')->count();
        $atendidas = $citas->where('estado', 'atendida')->count();

        $bloqueos = BloqueoAgenda::whereDate('fecha', $fecha)->get();

        $doctores = User::role('doctor')->get();

        $doctoresBloqueadosIds = $bloqueos->pluck('creado_por')->toArray();

        $doctoresDisponibles = $doctores->filter(function ($d) use ($doctoresBloqueadosIds) {
            return !in_array($d->id, $doctoresBloqueadosIds);
        });

        $totalHorarios = $doctoresDisponibles->count();

        $ocupados = $citas->count();
        $bloqueados = $bloqueos->count();

        $bloqueosConDoctor = $bloqueos->map(function ($bloqueo) {
            $doctor = User::find($bloqueo->creado_por);
            return [
                'nombre_doctor' => $doctor ? $doctor->nombres . ' ' . $doctor->apellidos : 'Desconocido',
                'fecha' => $bloqueo->fecha,
                'motivo' => $bloqueo->motivo ?? null,
            ];
        });

        return view('citas.AgendaDia', [
            'fecha' => $fecha,
            'totalHorarios' => $totalHorarios,
            'ocupados' => $ocupados,
            'bloqueados' => $bloqueados,
            'programadas' => $programadas,
            'canceladas' => $canceladas,
            'atendidas' => $atendidas,
            'citas' => $citas,
            'bloqueos' => $bloqueosConDoctor,
        ]);
    }
}
