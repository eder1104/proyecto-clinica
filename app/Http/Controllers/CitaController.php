<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\AgendaService;
use Carbon\CarbonPeriod;

class CitaController extends Controller
{
    public function index(Request $request, AgendaService $agendaService)
    {
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');

        $query = Cita::with(['paciente'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'asc');

        if (!empty($estado)) $query->where('estado', $estado);
        if (!empty($fecha)) $query->whereDate('fecha', $fecha);

        $citas = $query->get();
        $now = Carbon::now();

        foreach ($citas as $cita) {
            $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
            if (!in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) && $now->greaterThan($horaFin)) {
                $cita->estado = 'no_asistida';
                $cita->save();
            }
        }

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $dias = [];
        foreach ($period as $day) {
            $dias[] = [
                'fecha' => $day->format('Y-m-d'),
                'estado' => $agendaService->estadoDelDia($day->format('Y-m-d'))
            ];
        }

        return view('citas.index', compact('dias', 'citas'));
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
            'fecha'        => $validated['fecha'],
            'hora_inicio'  => $validated['hora_inicio'],
            'hora_fin'     => $validated['hora_fin'],
            'paciente_id'  => $validated['paciente_id'],
            'tipo_cita_id' => $validated['tipo_cita_id'],
            'estado'       => $validated['estado'],
            'created_by'   => $validated['created_by'],
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
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
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')
                ->with('error', 'No se puede actualizar esta cita. Está bloqueada.');
        }

        $data = $request->only([
            'fecha',
            'hora_inicio',
            'hora_fin',
            'paciente_id',
            'tipo_cita_id',
            'motivo_consulta'
        ]);

        $data['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $data['estado'] = 'modificada';

        $cita->update($data);

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente.');
    }


    public function destroy(Cita $cita, Request $request)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede cancelar esta cita. Está bloqueada.');
        }

        $motivo = $request->input('delete_reason');

        if (!$motivo) {
            return redirect()->route('citas.index')
                ->with('error', 'Debe ingresar una razón para cancelar la cita.');
        }

        $cita->update([
            'estado'        => 'cancelada',
            'cancelled_by'  => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
            'cancel_reason' => $motivo,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita cancelada correctamente.');
    }

    public function cancelar($id)
    {
        session([
            'show_cancel_modal' => true,
            'cita_id' => $id,
        ]);

        return redirect()->route('citas.index');
    }

    public function finalizar(Cita $cita)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida', 'asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede finalizar esta cita. Está bloqueada.');
        }

        $cita->update([
            'estado'     => 'finalizada',
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita finalizada correctamente.');
    }
}
