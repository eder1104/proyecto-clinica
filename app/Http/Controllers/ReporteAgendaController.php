<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\BloqueoAgenda;
use App\Models\User;
use Carbon\Carbon;

class ReporteAgendaController extends Controller
{
    public function index()
    {
        $fecha = Carbon::today()->toDateString();

        $citas = Cita::with(['paciente'])
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio', 'asc')
            ->get();

        $citas->transform(function ($cita) {
            $cita->tipo_examen_nombre = $cita->tipo_examen == 1 ? 'Optometría' : 'Examen';
            return $cita;
        });

        $programadas = $citas->where('estado', 'programada')->count();
        $canceladas = $citas->filter(function ($cita) {
            return str_contains(strtolower($cita->estado), 'cancel');
        })->count();
        $atendidas   = $citas->where('estado', 'atendida')->count();

        $bloqueos = BloqueoAgenda::with('doctor')
            ->whereDate('fecha', $fecha)
            ->get();

        $doctores = User::role('doctor')->get();

        $bloqueadosIds = $bloqueos->pluck('creado_por')->filter()->unique()->values()->all();

        $totalDoctores = $doctores->count();
        $bloqueados = count($bloqueadosIds);
        $totalHorarios = $totalDoctores - $bloqueados;

        $ocupados = $citas->count();

        $bloqueosConDoctor = $bloqueos->map(function ($bloqueo) {
            return [
                'nombre_doctor' => $bloqueo->doctor ? "{$bloqueo->doctor->nombres} {$bloqueo->doctor->apellidos}" : 'Desconocido',
                'fecha' => $bloqueo->fecha,
                'motivo' => $bloqueo->motivo ?? 'No especificado',
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
