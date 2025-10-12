<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with(['creador', 'actualizador'])->get();
        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'documento'        => 'required|string|max:50|unique:pacientes',
            'telefono'         => 'required|string|max:20',
            'direccion'        => 'required|string|max:255',
            'email'            => 'nullable|email|unique:pacientes',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
        ]);

        Paciente::create([
            'nombres'          => $validated['nombres'],
            'apellidos'        => $validated['apellidos'],
            'documento'        => $validated['documento'],
            'telefono'         => $validated['telefono'],
            'direccion'        => $validated['direccion'],
            'email'            => $validated['email'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
            'sexo'             => $validated['sexo'] ?? null,
            'created_by'       => Auth::id(),
        ]);

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'documento'        => 'required|string|max:50|unique:pacientes,documento,' . $paciente->id,
            'telefono'         => 'required|string|max:20',
            'direccion'        => 'required|string|max:255',
            'email'            => 'nullable|email|unique:pacientes,email,' . $paciente->id,
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
        ]);

        $validated['updated_by'] = Auth::id();

        $paciente->update($validated);

        return redirect()
            ->route('pacientes.index', $paciente)
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        $paciente->update([
            'cancelled_by'  => Auth::id(),
        ]);
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado correctamente.');
    }

    public function show($id)
    {
        $paciente = Paciente::findOrFail($id);

        $historias = Cita::where('paciente_id', $id)
            ->where('estado', 'finalizada')
            ->get();

        return view('pacientes.show', compact('paciente', 'historias'));
    }
}
