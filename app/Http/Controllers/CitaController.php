<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with(['paciente', 'admisiones'])->get();
        $pacientes = User::role('paciente')->get();
        $admisiones = User::role('admisiones')->get();

        return view('citas.index', compact('citas', 'pacientes', 'admisiones'));
    }

    public function create()
    {
        $pacientes = User::role('paciente')->get();
        $admisiones = User::role('admisiones')->get();

        return view('citas.create', compact('pacientes', 'admisiones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id' => 'required|exists:users,id',
            'admisiones_id' => 'required|exists:users,id',
        ]);

        Cita::create(array_merge($validated, [
            'estado' => 'programada',
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('citas.index')->with('success', 'La cita fue creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $pacientes = User::role('paciente')->get();
        $admisiones = User::role('admisiones')->get();

        return view('citas.edit', compact('cita', 'pacientes', 'admisiones'));
    }

    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'paciente_id' => 'required|exists:users,id',
            'admisiones_id' => 'required|exists:users,id',
            'estado' => 'sometimes|in:programada,cancelada,finalizada',
        ]);

        $cita->update(array_merge($validated, [
            'updated_by' => Auth::id(),
        ]));

        return redirect()->route('citas.index')->with('success', 'La cita fue actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('success', 'La cita fue eliminada correctamente.');
    }
}
