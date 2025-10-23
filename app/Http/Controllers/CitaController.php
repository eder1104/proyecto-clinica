<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use App\MOdels\HistoriaClinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
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

        $citas = $query->get();
        
        $now = Carbon::now();

        foreach ($citas as $cita) {
            $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);

            if (
                $cita->estado != 'cancelada' && 
                $cita->estado != 'finalizada' && 
                $cita->estado != 'no_asistida' && 
                $now->greaterThan($horaFin)
            ) {
                $cita->estado = 'no_asistida';
                $cita->save();
            }
        }
        
        return view('citas.index', compact('citas'));
    }

    public function create()
    {
        $users = User::where('role', 'admisiones')->get();
        $pacientes = Paciente::all();

        return view('citas.create', compact('users', 'pacientes'));
    }

    public function store(CitaRequest $request)
    {
        $validated = $request->validate([
            'fecha'          => 'required|date',
            'hora_inicio'    => 'required|date_format:H:i',
            'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id'    => 'required|exists:pacientes,id',
        ]);

        $validated['created_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $validated['estado'] = 'programada';

        $cita = Cita::create($validated);

        HistoriaClinica::updateOrCreate(
            ['paciente_id' => $cita->paciente_id],
            [
                'motivo_consulta' => 'Atención en cita médica',
                'diagnostico' => 'Pendiente por evaluación',
                'conducta' => 'Por definir',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente y historia clínica actualizada.');
    }


    public function edit(Cita $cita)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            session()->flash('error', 'No se puede editar una cita con estado ' . ucfirst($cita->estado) . ' o que ya ha finalizado.');
            return redirect()->route('citas.index');
        }

        $pacientes = Paciente::all();
        $admisiones = User::all();

        return view('citas.edit', compact('cita', 'pacientes', 'admisiones'));
    }

    public function update(Request $request, Cita $cita)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede actualizar esta cita. Está bloqueada.');
        }

        $validated = $request->validate([
            'fecha'          => 'required|date',
            'hora_inicio'    => 'required|date_format:H:i',
            'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id'    => 'required|exists:pacientes,id',
        ]);

        $validated['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $validated['estado'] = 'modificada';

        $cita->update($validated);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita, Request $request)
    {
        $horaFin = Carbon::parse($cita->fecha . ' ' . $cita->hora_fin);
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) || Carbon::now()->greaterThan($horaFin);

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
        $isBlocked = in_array($cita->estado, ['cancelada', 'finalizada', 'no_asistida']) || Carbon::now()->greaterThan($horaFin);

        if ($isBlocked) {
            return redirect()->route('citas.index')->with('error', 'No se puede finalizar esta cita. Está bloqueada.');
        }
        
        $cita->update([
            'estado'     => 'finalizada',
            'updated_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
        ]);

        $user = Auth::user();

        app(\App\Http\Controllers\HistoriaClinicaController::class)
            ->registrarDesdeCita($cita, $user);

        return redirect()->route('citas.index')
            ->with('success', 'Cita finalizada y historia clínica actualizada correctamente.');
    }
}