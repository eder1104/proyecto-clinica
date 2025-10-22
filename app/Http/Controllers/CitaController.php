<?php

namespace App\Http\Controllers;

use App\http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');

        $query = Cita::with(['paciente', 'createdBy'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'asc');

        if (!empty($estado)) {
            $query->where('estado', $estado);
        }

        if (!empty($fecha)) {
            $query->whereDate('fecha', $fecha);
        }

        $citas = $query->get();

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
            'fecha'        => 'required|date',
            'hora_inicio'  => 'required|date_format:H:i',
            'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id'   => 'required|exists:pacientes,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['estado'] = 'programada';

        Cita::create($validated);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::all();
        $admisiones = User::all();

        return view('citas.edit', compact('cita', 'pacientes', 'admisiones'));
    }

    public function update(request $request, Cita $cita)
    {
        $validated = $request->validate([
            'fecha'        => 'required|date',
            'hora_inicio'  => 'required|date_format:H:i',
            'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id'   => 'required|exists:pacientes,id',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['estado'] = 'modificada';

        $cita->update($validated);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita, Request $request)
    {
        $motivo = $request->input('delete_reason');

        if (!$motivo) {
            return redirect()->route('citas.index')->with('error', 'Debe ingresar una razón para cancelar la cita.');
        }

        $cita->update([
            'estado'        => 'cancelada',
            'cancelled_by'  => Auth::id(),
            'cancel_reason' => $motivo,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function cancelar(Cita $cita)
    {
        $cita->update([
            'estado'       => 'cancelada',
            'cancelled_by' => Auth::id(),
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function finalizar(Cita $cita)
    {
        $cita->update([
            'estado'     => 'asistida',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita finalizada correctamente.');
    }
}
