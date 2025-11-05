<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctores;
use Carbon\Carbon;

class CalendarioEspecialistaController extends Controller
{
    public function index()
    {
        return view('citas.CalendarioEspecialista');
    }

    public function buscarDoctor($numero)
    {
        $doctor = Doctores::with('user')->where('documento', $numero)->first();

        if ($doctor) {
            return response()->json([
                'id' => $doctor->id,
                'nombre' => $doctor->user->nombres . ' ' . $doctor->user->apellidos
            ]);
        }

        return response()->json(['error' => 'Doctor no encontrado'], 404);
    }

    public function obtenerCalendario($doctorId, $mes)
    {
        $dias = [];
        $fechaInicio = new \DateTime("$mes-01");
        $diasMes = (int)$fechaInicio->format('t');

        for ($d = 1; $d <= $diasMes; $d++) {
            $fecha = sprintf('%s-%02d', $mes, $d);
            $diaSemana = (new \DateTime($fecha))->format('w');

            if ($diaSemana == 0) {
                $dias[$fecha] = 'Bloqueado';
            } elseif ($diaSemana == 3) {
                $dias[$fecha] = 'Parcial';
            } else {
                $dias[$fecha] = 'Disponible';
            }
        }

        return response()->json($dias);
    }

    public function actualizarEstado(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|integer',
            'fecha' => 'required|date',
            'estado' => 'required|string'
        ]);

        return response()->json(['success' => true, 'data' => $validated]);
    }
}
