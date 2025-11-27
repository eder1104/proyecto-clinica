<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\BloqueoAgenda;
use App\Models\DoctorParcialidad;
use App\Models\User;
use Carbon\Carbon;

class ReporteAgendaController extends Controller
{
    public function index()
    {
        $fecha = Carbon::today()->toDateString();

        $citas = Cita::with(['paciente'])
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        $bloqueosRaw = BloqueoAgenda::with(['doctor.user'])
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        $bloqueos = $bloqueosRaw->map(function ($b) {
            return [
                'doctor' => $b->doctor && $b->doctor->user
                    ? $b->doctor->user->nombres . ' ' . $b->doctor->user->apellidos
                    : 'Desconocido',
                'fecha' => $b->fecha,
                'hora_inicio' => $b->hora_inicio,
                'hora_fin' => $b->hora_fin,
                'motivo' => $b->motivo ?? 'No especificado',
            ];
        });

        $parcialesRaw = DoctorParcialidad::with(['doctor.user'])
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        $parciales = $parcialesRaw->map(function ($p) {
            return [
                'doctor' => $p->doctor && $p->doctor->user
                    ? $p->doctor->user->nombres . ' ' . $p->doctor->user->apellidos
                    : 'Desconocido',
                'fecha' => $p->fecha,
                'hora_inicio' => $p->hora_inicio,
                'hora_fin' => $p->hora_fin,
                'motivo' => 'Sin motivo',
            ];
        });

        $programadas = $citas->where('estado', 'programada')->count();
        $canceladas = $citas->filter(fn($c) => str_contains(strtolower($c->estado), 'cancel'))->count();
        $finalizada = $citas->where('estado', 'finalizada')->count();

        $numDoctores = User::role('doctor')->count();
        $bloqueados = $bloqueos->count();
        $ocupados = $citas->count();

        $horaInicio = 8; 
        $horaFin = 18;   
        $intervalo = 20; 

        $totalMinutosDia = ($horaFin - $horaInicio) * 60;
        $slotsPorDoctor = floor($totalMinutosDia / $intervalo);
        $capacidadTotal = $numDoctores * $slotsPorDoctor;

        $slotsBloqueados = 0;

        foreach ($bloqueosRaw as $b) {
            $inicio = Carbon::parse($b->hora_inicio);
            $fin = Carbon::parse($b->hora_fin);
            $minutos = $fin->diffInMinutes($inicio);
            $slotsBloqueados += ceil($minutos / $intervalo);
        }

        foreach ($parcialesRaw as $p) {
            $inicio = Carbon::parse($p->hora_inicio);
            $fin = Carbon::parse($p->hora_fin);
            $minutos = $fin->diffInMinutes($inicio);
            $slotsBloqueados += ceil($minutos / $intervalo);
        }

        $totalHorarios = max(0, $capacidadTotal - $slotsBloqueados);

        return view('citas.AgendaDia', [
            'fecha' => $fecha,
            'totalHorarios' => $totalHorarios,
            'ocupados' => $ocupados,
            'bloqueados' => $bloqueados,
            'programadas' => $programadas,
            'canceladas' => $canceladas,
            'finalizada' => $finalizada,
            'citas' => $citas,
            'bloqueos' => $bloqueos,
            'parciales' => $parciales,
            'numDoctores' => $numDoctores
        ]);
    }
}