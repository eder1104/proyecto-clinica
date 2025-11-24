<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\CalendarioDisponibilidad;
use App\Models\BloqueoAgenda;
use App\Models\doctores;
use App\Services\AgendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $doctores = doctores::with('user:id,nombres,apellidos')
            ->select('id', 'user_id', 'especialidad')
            ->get()
            ->map(function ($doctor) {
                return [
                    'nombre' => $doctor->user->nombres . ' ' . $doctor->user->apellidos,
                    'especialidad' => $doctor->especialidad ?? 'Sin especialidad'
                ];
            });

        return view('citas.calendario', compact('dias', 'doctores'));
    }

    public function obtenerDatosCalendario()
    {
        $hoy = Carbon::now();
        $firstOfMonth = $hoy->copy()->firstOfMonth()->startOfDay();
        $lastOfMonth = $hoy->copy()->lastOfMonth()->endOfDay();

        $estadosDoctor = CalendarioDisponibilidad::with('doctor.user:id,nombres,apellidos')
            ->whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->whereIn('estado', ['bloqueado', 'parcial'])
            ->get();

        $mapaEstados = [];
        foreach ($estadosDoctor as $estado) {
            $fecha = Carbon::parse($estado->fecha)->format('Y-m-d');
            $prioridad = $estado->estado === 'bloqueado' ? 2 : 1;

            $nombreDoctor = ($estado->doctor && $estado->doctor->user)
                ? $estado->doctor->user->nombres . ' ' . $estado->doctor->user->apellidos
                : 'Doctor no especificado';

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

        $citasRegistradas = Cita::whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->where('estado', 'activa')
            ->get(['fecha']);

        foreach ($citasRegistradas as $cita) {
            $fecha = Carbon::parse($cita->fecha)->format('Y-m-d');
            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => 'cita',
                    'doctores' => ['Citas registradas'],
                    'prioridad' => 0
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
            $cantidadCitas = Cita::whereDate('fecha', $fecha)->where('estado', 'activa')->count();

            $estadoBase = $this->agenda->estadoDelDia(5, $fecha);
            if ($estadoBase === 'parcial' || $estadoBase === 'bloqueado') {
                $estadoFinal = $estadoBase;
            }

            if (isset($mapaEstados[$fecha])) {
                $estadoFinal = $mapaEstados[$fecha]['estado'];
                $doctores = $mapaEstados[$fecha]['doctores'];
                $doctorNombre = implode(', ', $doctores);
            }

            $dias[] = [
                'fecha' => $fecha,
                'estado' => $estadoFinal,
                'doctor' => $doctorNombre,
                'citas_activas' => $cantidadCitas
            ];
        }

        return $dias;
    }


    public function citasPorDia($fecha)
    {
        $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');

        $citas = Cita::with(['paciente:id,nombres,apellidos'])
            ->whereDate('fecha', $fechaFormateada)
            ->where('estado', 'activa')
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

        BloqueoAgenda::create([
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'] . ':00',
            'hora_fin' => $data['hora_fin'] . ':00',
            'motivo' => $data['motivo'] ?? null,
            'creado_por' => Auth::id()
        ]);

        return back()->with('success', 'Bloqueo creado correctamente.');
    }
}
