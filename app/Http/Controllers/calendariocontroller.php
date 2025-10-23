<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;

class CalendarioController extends Controller
{
    public function index()
    {
        $citas = Cita::whereIn('estado', ['programada', 'modificada'])
            ->pluck('fecha')
            ->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))
            ->toArray();

        return view('citas.calendario', compact('citas'));
    }

    public function citasPorDia($fecha)
    {
        $citas = Cita::with([
            'paciente:id,nombres,apellidos',
            'tipoCita:id,nombre',
        ])
            ->whereDate('fecha', $fecha)
            ->get([
                'id',
                'fecha',
                'hora_inicio',
                'hora_fin',
                'estado',
                'tipo_cita_id',
                'paciente_id',
                'cancel_reason',
            ]);

        return response()->json($citas);
    }
}
