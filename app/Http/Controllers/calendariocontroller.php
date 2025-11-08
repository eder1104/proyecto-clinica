<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;
use App\Services\AgendaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CalendarioDisponibilidad;

class CalendarioController extends Controller
{
    protected $agenda;

    public function __construct(AgendaService $agenda)
    {
        $this->agenda = $agenda;
    }

    public function index(Request $request)
    {
        $dias = $this->obtenerDatosCalendario();
        return view('citas.calendario', compact('dias'));
    }

    public function obtenerDatosCalendario()
    {
        $date = Carbon::now();
        $firstOfMonth = $date->copy()->firstOfMonth();
        $lastOfMonth = $date->copy()->lastOfMonth();

        $estadosDoctor = CalendarioDisponibilidad::with('doctor.user:id,nombres')
            ->whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->whereIn('estado', ['bloqueado', 'parcial'])
            ->get();

        $mapaEstados = [];
        foreach ($estadosDoctor as $estado) {
            $fecha = $estado->fecha;
            $prioridad = $estado->estado === 'bloqueado' ? 2 : 1;
            $nombreDoctor = ($estado->doctor && $estado->doctor->user) ? explode(' ', $estado->doctor->user->nombres)[0] : '??';
            $nombreCompleto = 'Dr. ' . $nombreDoctor;

            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => $estado->estado,
                    'doctores' => [$nombreCompleto],
                    'prioridad' => $prioridad
                ];
            } else {
                if ($prioridad > $mapaEstados[$fecha]['prioridad']) {
                    $mapaEstados[$fecha]['estado'] = $estado->estado;
                    $mapaEstados[$fecha]['doctores'] = [$nombreCompleto];
                    $mapaEstados[$fecha]['prioridad'] = $prioridad;
                } elseif ($prioridad === $mapaEstados[$fecha]['prioridad']) {
                    if (!in_array($nombreCompleto, $mapaEstados[$fecha]['doctores'])) {
                        $mapaEstados[$fecha]['doctores'][] = $nombreCompleto;
                    }
                }
            }
        }

        $citasRegistradas = Cita::whereBetween('fecha', [$firstOfMonth, $lastOfMonth])
            ->get(['fecha']);

        foreach ($citasRegistradas as $cita) {
            $fecha = Carbon::parse($cita->fecha)->format('Y-m-d');
            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => 'cita',
                    'doctores' => ['Citas registradas']
                ];
            } else {
                if (!in_array('Citas registradas', $mapaEstados[$fecha]['doctores'])) {
                    $mapaEstados[$fecha]['doctores'][] = 'Citas registradas';
                }
            }
        }

        $dias = [];
        for ($d = $firstOfMonth->copy(); $d->lte($lastOfMonth); $d->addDay()) {
            $fecha = $d->format('Y-m-d');
            $estadoFinal = 'disponible';
            $doctorNombre = null;

            $estadoBase = $this->agenda->estadoDelDia($fecha);
            if ($estadoBase === 'parcial' || $estadoBase === 'bloqueado') {
                $estadoFinal = $estadoBase;
            }

            if (isset($mapaEstados[$fecha])) {
                $estadoFinal = $mapaEstados[$fecha]['estado'];
                $doctores = $mapaEstados[$fecha]['doctores'];
                if (count($doctores) > 1) {
                    $ultimoDoctor = array_pop($doctores);
                    $doctorNombre = implode(', ', $doctores) . ' y ' . $ultimoDoctor;
                } else {
                    $doctorNombre = $doctores[0];
                }
            }

            $dias[] = [
                'fecha' => $fecha,
                'estado' => $estadoFinal,
                'doctor' => $doctorNombre
            ];
        }
        return $dias;
    }

    public function citasPorDia($fecha)
    {
        $citas = \App\Models\Cita::whereDate('fecha', $fecha)->get();
        dd($citas);
        $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');

        $citas = Cita::with(['paciente:id,nombres,apellidos'])
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
            ]);

        $result = $citas->map(function ($cita) {
            $citaData = $cita->toArray();
            $citaData['tipo_cita'] = ['nombre' => $cita->tipo_cita_nombre];
            unset($citaData['tipo_cita_id']);
            return $citaData;
        });

        return response()->json($result);
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
