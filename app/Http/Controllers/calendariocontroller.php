<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;
use App\Services\AgendaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    protected $agenda;

    public function __construct(AgendaService $agenda)
    {
        $this->agenda = $agenda;
    }

    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
        }

        $date = Carbon::now();
        $firstOfMonth = $date->copy()->firstOfMonth();
        $lastOfMonth = $date->copy()->lastOfMonth();

        $dias = [];
        for ($d = $firstOfMonth->copy(); $d->lte($lastOfMonth); $d->addDay()) {
            $fecha = $d->format('Y-m-d');
            $dias[] = [
                'fecha' => $fecha,
                'estado' => $this->agenda->estadoDelDia($fecha)
            ];
        }

        return view('citas.calendario', compact('dias'));
    }

    public function citasPorDia($fecha)
    {
        $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');

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

    public function crearBloqueo(Request $request)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'motivo' => 'nullable|string|max:255'
        ]);

        \App\Models\BloqueoAgenda::create([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'] . ':00',
            'hora_fin' => $data['hora_fin'] . ':00',
            'motivo' => $data['motivo'] ?? null,
            'creado_por' => Auth::id()
        ]);

        return back()->with('success', 'Bloqueo creado correctamente.');
    }
}
