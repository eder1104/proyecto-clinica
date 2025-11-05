<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            abort(403, 'Acceso denegado para administradores.');
        }

        $citas = Cita::whereNotNull('fecha')
            ->pluck('fecha')
            ->map(fn($f) => Carbon::parse($f)->format('Y-m-d'))
            ->unique()
            ->toArray();


        return view('citas.calendario', compact('citas'));
    }

    public function citasPorDia($fecha)
    {
        $fechaFormateada = \Carbon\Carbon::parse($fecha)->format('Y-m-d');

        $citas = Cita::with('paciente:id,nombres,apellidos')
            ->whereDate('fecha', $fechaFormateada)
            ->get([
                'id',
                'fecha',
                'hora_inicio',
                'hora_fin',
                'estado',
                'tipo_cita_id',
                'paciente_id',
                'cancel_reason',
            ])
            ->map(function ($cita) {
                switch ($cita->tipo_cita_id) {
                    case 1:
                        $cita->tipo_cita = ['nombre' => 'Optometría'];
                        break;
                    case 2:
                        $cita->tipo_cita = ['nombre' => 'Examen'];
                        break;
                    default:
                        $cita->tipo_cita = ['nombre' => 'Sin tipo'];
                        break;
                }
                return $cita;
            });

        return response()->json($citas);
    }
}
