<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\PacientesRequest;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with(['creador', 'actualizador'])->get();
        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        return view('pacientes.create', compact('pacientes'));
    }

    public function store(PacientesRequest $request)
    {
        $validated = $request->validate([
            'tipo_documento'   => 'required|string|max:20',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'documento'        => 'required|string|max:20|unique:pacientes',
            'telefono'         => 'required|string|max:10',
            'direccion'        => 'required|string|max:255',
            'email'            => 'nullable|email|unique:pacientes',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
        ]);

        Paciente::create([
            'tipo_documento'   => $validated['tipo_documento'],
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

    public function update(PacientesRequest $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'tipo_documento'   => 'required|string|max:20',
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
            'cancelled_by'   => Auth::id(),
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

    public function Paciente_buscar(Request $request)
    {
        $nombre = $request->input('nombre');
        $documento = $request->input('documento');

        $query = Paciente::query();

        if ($nombre) {
            $query->where(function ($q) use ($nombre) {
                $q->where('nombres', 'LIKE', "%{$nombre}%")
                    ->orWhere('apellidos', 'LIKE', "%{$nombre}%");
            });
        }

        if ($documento) {
            $query->where('documento', 'LIKE', "%{$documento}%");
        }

        $pacientes = $query->get();

        return view('pacientes.index', compact('pacientes'));
    }

    public function buscar(Request $request)
    {
        $tipo = $request->query('tipo');
        $numero = $request->query('numero');

        if (!$tipo || !$numero) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        $paciente = Paciente::where('tipo_documento', $tipo)
            ->where('documento', $numero)
            ->first();

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        return response()->json([
            'id' => $paciente->id,
            'tipo_documento' => $paciente->tipo_documento,
            'documento' => $paciente->documento,
            'nombres' => $paciente->nombres,
            'apellidos' => $paciente->apellidos,
            'telefono' => $paciente->telefono,
            'direccion' => $paciente->direccion,
            'email' => $paciente->email,
            'fecha_nacimiento' => $paciente->fecha_nacimiento ?? '',
            'sexo' => $paciente->sexo ?? '',
        ]);
    }

    public function actualizarApi(PacientesRequest $request, $id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            $paciente->update(array_merge($request->validated(), [
                'updated_by' => Auth::id(),
            ]));

            return response()->json([
                'success' => true,
                'mensaje' => 'Paciente actualizado correctamente',
                'paciente' => [
                    'id' => $paciente->id,
                    'tipo_documento' => $paciente->tipo_documento,
                    'documento' => $paciente->documento,
                    'nombres' => $paciente->nombres,
                    'apellidos' => $paciente->apellidos,
                    'telefono' => $paciente->telefono,
                    'direccion' => $paciente->direccion,
                    'email' => $paciente->email,
                    'fecha_nacimiento' => $paciente->fecha_nacimiento ?? '',
                    'sexo' => $paciente->sexo ?? '',
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error desconocido al actualizar el paciente.', 'error_interno' => $e->getMessage()], 500);
        }
    }
}