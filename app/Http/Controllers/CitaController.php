<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\PlantillaConsentimiento;
use App\Models\ConsentimientoPaciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AgendaService;
use App\Http\Controllers\CalendarioController;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\BitacoraAuditoriaController;
use App\Models\HistorialCambio;


class CitaController extends Controller
{
    public function index(Request $request, AgendaService $agendaService)
    {
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');

        $query = Cita::with(['paciente'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'asc');

        if (!empty($estado)) {
            $query->where('estado', $estado);
        }

        if (!empty($fecha)) {
            $query->whereDate('fecha', $fecha);
        }

        $citasCollection = $query->get();

        $page = $request->get('page', 1);
        $perPage = 7;

        $citas = new LengthAwarePaginator(
            $citasCollection->forPage($page, $perPage)->values(),
            $citasCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $now = Carbon::now();

        foreach ($citas as $cita) {
            $fechaCita = Carbon::parse($cita->fecha)->format('Y-m-d');
            $horaFin = Carbon::parse($fechaCita . ' ' . $cita->hora_fin);
            if (!in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) && $now->greaterThan($horaFin)) {
                $cita->estado = 'no_asistida';
                $cita->save();
            }
        }

        $pacientes = Paciente::select('id', 'nombres', 'apellidos')->orderBy('apellidos', 'asc')->get();
        $plantillas = PlantillaConsentimiento::select('id', 'titulo')->where('activo', true)->orderBy('titulo', 'asc')->get();
        $consentimientos = ConsentimientoPaciente::with(['paciente', 'plantilla'])->orderBy('created_at', 'desc')->get();

        $calendarioController = app(CalendarioController::class);
        $dias = $calendarioController->obtenerDatosCalendario();

        return view('citas.index', compact('citas', 'pacientes', 'plantillas', 'consentimientos', 'dias'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        $admisiones = User::where('role', 'admisiones')->get();
        $tipos_citas = [
            1 => 'Optometría',
            2 => 'Exámenes'
        ];

        return view('citas.create', compact('pacientes', 'admisiones', 'tipos_citas'));
    }

    public function store(CitaRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $validated['estado'] = 'programada';

        Cita::create([
            'fecha'           => $validated['fecha'],
            'hora_inicio'     => $validated['hora_inicio'],
            'hora_fin'        => $validated['hora_fin'],
            'paciente_id'     => $validated['paciente_id'],
            'tipo_cita_id'    => $validated['tipo_cita_id'],
            'tipo_examen'     => $request->input('tipo_examen'),
            'motivo_consulta' => $request->input('motivo_consulta'),
            'estado'          => $validated['estado'],
            'created_by'      => $validated['created_by'],
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $fechaCita = Carbon::parse($cita->fecha)->format('Y-m-d');
        $horaFin = Carbon::parse($fechaCita . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede editar una cita con este estado o que ya ha finalizado.');
        }

        $pacientes = Paciente::all();
        $admisiones = User::where('role', 'admisiones')->get();
        $tipos_citas = [
            1 => 'Optometría',
            2 => 'Exámenes'
        ];

        return view('citas.edit', compact('cita', 'pacientes', 'admisiones', 'tipos_citas'));
    }

    public function update(CitaRequest $request, Cita $cita)
    {
        $fechaCita = Carbon::parse($cita->fecha)->format('Y-m-d');
        $horaFin = Carbon::parse($fechaCita . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede actualizar esta cita. Está bloqueada.');
        }

        $data = $request->only(['fecha', 'hora_inicio', 'hora_fin', 'paciente_id', 'tipo_cita_id', 'motivo_consulta']);
        $data['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $data['estado'] = 'modificada';

        $antes = $cita->only([
            'fecha',
            'hora_inicio',
            'hora_fin',
            'paciente_id',
            'tipo_cita_id',
            'motivo_consulta',
            'updated_by',
            'estado'
        ]);

        $cita->update($data);

        $despues = $cita->only([
            'fecha',
            'hora_inicio',
            'hora_fin',
            'paciente_id',
            'tipo_cita_id',
            'motivo_consulta',
            'updated_by',
            'estado'
        ]);

        $labels = [
            'fecha' => 'Fecha',
            'hora_inicio' => 'Hora Inicio',
            'hora_fin' => 'Hora Fin',
            'paciente_id' => 'Paciente Id',
            'tipo_cita_id' => 'Tipo Cita Id',
            'motivo_consulta' => 'Motivo Consulta',
            'updated_by' => 'Updated By',
            'estado' => 'Estado'
        ];

        $observacion = '';

        foreach ($antes as $campo => $valorAntes) {
            $valorDespues = $despues[$campo];

            $valorAntes = (string)($valorAntes ?? '');
            $valorDespues = (string)($valorDespues ?? '');

            if ($valorAntes != $valorDespues) {
                $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
                $observacion .= $label . ': ' . $valorAntes . ' -> ' . $valorDespues . "\n";
            }
        }

        if ($observacion === '') {
            $observacion = 'Sin cambios';
        }

        $bitacoraId = BitacoraAuditoriaController::registrar(
            Auth::id(),
            'Citas',
            'Editò',
            $cita->id,
            trim($observacion)
        );

        HistorialCambio::create([
            'bitacora_id' => $bitacoraId,
            'registro_afectado' => $cita->id,
            'datos_anteriores' => $antes,
            'datos_nuevos' => $despues,
            'fecha_cambio' => now()
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita, Request $request)
    {
        $fechaCita = Carbon::parse($cita->fecha)->format('Y-m-d');
        $horaFin = Carbon::parse($fechaCita . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede cancelar esta cita. Está bloqueada.');
        }

        $motivo = $request->input('delete_reason');
        if (!$motivo) {
            return redirect()->route('citas.index')->with('error', 'Debe ingresar una razón para cancelar la cita.');
        }

        $cita->update([
            'estado' => 'cancelada',
            'cancelled_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
            'cancel_reason' => $motivo,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function cancelar($id)
    {
        session(['show_cancel_modal' => true, 'cita_id' => $id]);
        return redirect()->route('citas.index');
    }

    public function finalizar(Cita $cita)
    {
        $fechaCita = Carbon::parse($cita->fecha)->format('Y-m-d');
        $horaFin = Carbon::parse($fechaCita . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede finalizar esta cita. Está bloqueada.');
        }

        $cita->update(['estado' => 'finalizada']);
        return redirect()->route('citas.preexamen')->with('success', 'Cita finalizada correctamente.');
    }

    public function preExamen(Cita $cita)
    {
        $paciente = $cita->paciente;
        $consentimientos = ConsentimientoPaciente::where('cita_id', $cita->id)->get();
        return view('citas.preexamen', compact('cita', 'paciente', 'consentimientos'));
    }

    public function obtenerCitasPorFecha($fecha)
    {
        $citas = Cita::with('paciente')
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio', 'asc')
            ->get()
            ->map(function ($cita) {
                return [
                    'id' => $cita->id,
                    'paciente' => $cita->paciente ? $cita->paciente->nombres . ' ' . $cita->paciente->apellidos : 'Sin paciente',
                    'hora_inicio' => $cita->hora_inicio,
                    'hora_fin' => $cita->hora_fin,
                    'estado' => ucfirst($cita->estado),
                    'tipo_cita' => $cita->tipo_cita_nombre,
                    'motivo' => $cita->cancel_reason ?? '—',
                    'creado_por' => $cita->creado_por,
                ];
            });

        return response()->json($citas);
    }

    public function atencion(Cita $cita)
    {
        switch ($cita->tipo_cita_id) {
            case 1:
                return redirect()->route('plantillas.optometria', ['cita' => $cita->id]);
            case 2:
                return redirect()->route('examenes.index', ['cita' => $cita->id]);
            default:
                return redirect()->route('citas.index')->with('error', 'Tipo de cita no válido.');
        }
    }
}
