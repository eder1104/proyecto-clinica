<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\CalendarioDisponibilidad;
use App\Models\DoctorParcialidad;
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

        $bloqueos = BloqueoAgenda::whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->get(['doctor_id', 'fecha', 'hora_inicio', 'hora_fin', 'motivo']);

        $parcialidades = DoctorParcialidad::with('doctor.user')
            ->whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->get(['doctor_id', 'fecha', 'hora_inicio', 'hora_fin']);

        $mapaEstados = [];

        foreach ($bloqueos as $b) {
            $fecha = Carbon::parse($b->fecha)->format('Y-m-d');

            $doctorNombre = null;
            $doctorModel = null;
            try {
                $doctorModel = $b->doctor;
            } catch (\Throwable $e) {
                $doctorModel = null;
            }
            if ($doctorModel && isset($doctorModel->nombres)) {
                $doctorNombre = trim(($doctorModel->nombres ?? '') . ' ' . ($doctorModel->apellidos ?? ''));
            } else {
                $doct = Doctores::with('user:id,nombres,apellidos')->find($b->doctor_id);
                if ($doct && $doct->user) {
                    $doctorNombre = trim(($doct->user->nombres ?? '') . ' ' . ($doct->user->apellidos ?? ''));
                }
            }
            $doctorNombre = $doctorNombre ? 'Dr. ' . $doctorNombre : 'Doctor no especificado';

            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => 'disponible',
                    'doctores' => [],
                    'prioridad' => 0,
                    'bloqueos' => [],
                    'parcialidades' => []
                ];
            }

            $mapaEstados[$fecha]['bloqueos'][] = [
                'doctor_id' => $b->doctor_id,
                'doctor_nombre' => $doctorNombre,
                'hora_inicio' => $b->hora_inicio,
                'hora_fin' => $b->hora_fin,
                'motivo' => $b->motivo,
            ];

            if ($mapaEstados[$fecha]['prioridad'] < 2) {
                $mapaEstados[$fecha]['estado'] = 'bloqueado';
                $mapaEstados[$fecha]['prioridad'] = 2;
            }

            if (!in_array($doctorNombre, $mapaEstados[$fecha]['doctores'])) {
                $mapaEstados[$fecha]['doctores'][] = $doctorNombre;
            }
        }

        foreach ($parcialidades as $p) {
            $fecha = Carbon::parse($p->fecha)->format('Y-m-d');

            $doctorNombre = null;
            $doctorModel = null;
            try {
                $doctorModel = $p->doctor;
            } catch (\Throwable $e) {
                $doctorModel = null;
            }
            if ($doctorModel && isset($doctorModel->user)) {
                $doctorNombre = trim(($doctorModel->user->nombres ?? '') . ' ' . ($doctorModel->user->apellidos ?? ''));
            } else {
                $userDoc = \App\Models\User::find($p->doctor_id);
                if ($userDoc) {
                    $doctorNombre = trim(($userDoc->nombres ?? '') . ' ' . ($userDoc->apellidos ?? ''));
                }
            }
            $doctorNombre = $doctorNombre ? 'Dr. ' . $doctorNombre : 'Doctor no especificado';

            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => 'disponible',
                    'doctores' => [],
                    'prioridad' => 0,
                    'bloqueos' => [],
                    'parcialidades' => []
                ];
            }

            $mapaEstados[$fecha]['parcialidades'][] = [
                'doctor_id' => $p->doctor_id,
                'doctor_nombre' => $doctorNombre,
                'hora_inicio' => $p->hora_inicio,
                'hora_fin' => $p->hora_fin,
            ];

            if ($mapaEstados[$fecha]['prioridad'] < 1) {
                $mapaEstados[$fecha]['estado'] = 'parcial';
                $mapaEstados[$fecha]['prioridad'] = 1;
            }

            if (!in_array($doctorNombre, $mapaEstados[$fecha]['doctores'])) {
                $mapaEstados[$fecha]['doctores'][] = $doctorNombre;
            }
        }

        $citasRegistradas = Cita::whereBetween('fecha', [$firstOfMonth->format('Y-m-d'), $lastOfMonth->format('Y-m-d')])
            ->whereIn('estado', ['activa', 'pendiente', 'confirmada'])
            ->get(['fecha']);

        foreach ($citasRegistradas as $cita) {
            $fecha = Carbon::parse($cita->fecha)->format('Y-m-d');
            if (!isset($mapaEstados[$fecha])) {
                $mapaEstados[$fecha] = [
                    'estado' => 'cita',
                    'doctores' => ['Citas registradas'],
                    'prioridad' => 0,
                    'bloqueos' => [],
                    'parcialidades' => []
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
            
            $cantidadCitas = Cita::whereDate('fecha', $fecha)
                ->whereIn('estado', ['activa', 'pendiente', 'confirmada'])
                ->count();

            $estadoBase = $this->agenda->estadoDelDia(5, $fecha);
            if ($estadoBase === 'parcial' || $estadoBase === 'bloqueado') {
                $estadoFinal = $estadoBase;
            }

            $bloqueosDia = [];
            $parcialidadesDia = [];

            if (isset($mapaEstados[$fecha])) {
                $estadoFinal = $mapaEstados[$fecha]['estado'];
                $doctores = $mapaEstados[$fecha]['doctores'];
                $doctorNombre = implode(', ', $doctores);

                $bloqueosDia = $mapaEstados[$fecha]['bloqueos'] ?? [];
                $parcialidadesDia = $mapaEstados[$fecha]['parcialidades'] ?? [];
            }

            $dias[] = [
                'fecha' => $fecha,
                'estado' => $estadoFinal,
                'doctor' => $doctorNombre,
                'citas_activas' => $cantidadCitas,
                'bloqueos' => $bloqueosDia,
                'parcialidades' => $parcialidadesDia
            ];
        }

        return $dias;
    }

    public function estadoDia($fecha)
    {
        $fecha = Carbon::parse($fecha)->format('Y-m-d');

        $doctores = doctores::with('user:id,nombres,apellidos')
            ->select('id', 'user_id', 'especialidad')
            ->get();

        $bloqueos = BloqueoAgenda::whereDate('fecha', $fecha)
            ->get(['doctor_id', 'hora_inicio', 'hora_fin', 'motivo']);

        $parcialidades = DoctorParcialidad::whereDate('fecha', $fecha)
            ->get(['doctor_id', 'hora_inicio', 'hora_fin']);

        $respuesta = [];

        foreach ($doctores as $d) {
            $respuesta[] = [
                'doctor_id' => $d->id,
                'nombre' => $d->user->nombres . ' ' . $d->user->apellidos,
                'especialidad' => $d->especialidad ?? 'Sin especialidad',
                'bloqueos' => $bloqueos->where('doctor_id', $d->id)->values(),
                'parcialidades' => $parcialidades->where('doctor_id', $d->id)->values()
            ];
        }

        return response()->json($respuesta);
    }

    public function citasPorDia($fecha)
    {
        $fechaFormateada = Carbon::parse($fecha)->format('Y-m-d');

        $citas = Cita::with(['paciente:id,nombres,apellidos', 'doctor.user:id,nombres,apellidos'])
            ->whereDate('fecha', $fechaFormateada)
            ->whereIn('estado', ['activa', 'pendiente', 'confirmada'])
            ->get();

        $result = $citas->map(function ($cita) {
            
            $pacienteNombre = 'Sin paciente';
            if($cita->paciente) {
                $pacienteNombre = $cita->paciente->nombres . ' ' . $cita->paciente->apellidos;
            }

            $doctorNombre = 'Sin doctor';
            if($cita->doctor && $cita->doctor->user) {
                $doctorNombre = $cita->doctor->user->nombres . ' ' . $cita->doctor->user->apellidos;
            }

            $procedimiento = 'General';
            if($cita->tipoCita) {
                $procedimiento = $cita->tipoCita->nombre;
            } elseif($cita->tipo_cita_nombre) {
                $procedimiento = $cita->tipo_cita_nombre;
            }

            return [
                'id' => $cita->id,
                'fecha' => $cita->fecha,
                'hora' => $cita->hora_inicio,
                'hora_fin' => $cita->hora_fin,
                'estado' => $cita->estado,
                'paciente_nombre' => $pacienteNombre,
                'doctor_nombre' => $doctorNombre,
                'procedimiento' => $procedimiento,
                'cancel_reason' => $cita->cancel_reason,
            ];
        });

        return response()->json($result);
    }
}