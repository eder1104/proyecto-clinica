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
        $citas = Cita::whereDate('fecha', $fecha)->get();

        return response()->json($citas);
    }
}
