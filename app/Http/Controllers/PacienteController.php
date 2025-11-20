<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\PacientesRequest;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with(['creador', 'actualizador'])
            ->orderBy('apellidos', 'asc')
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(PacientesRequest $request, User $user)
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
            'created_by'       => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
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

        $datosAnteriores = $paciente->toArray();

        $validated['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
        $paciente->update($validated);

        $datosNuevos = $paciente->fresh()->toArray();

        $bitacoraId = BitacoraAuditoriaController::registrar(
            Auth::id(),
            'pacientes',
            'editar',
            $paciente->id
        );

        if (array_diff_assoc($datosNuevos, $datosAnteriores)) {
            BitacoraAuditoriaController::registrarCambio(
                $bitacoraId,
                $paciente->id,
                $datosAnteriores,
                $datosNuevos
            );
        }

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

    public function buscar(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Solicitud inválida'], 400);
        }

        $tipo = $request->query('tipo');
        $numero = $request->query('numero');

        if (!$numero) {
            return response()->json(['error' => 'Debe ingresar el número de documento'], 422);
        }

        $paciente = Paciente::where('tipo_documento', $tipo)
            ->where('documento', $numero)
            ->first();

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        return response()->json($paciente);
    }

    public function actualizarApi(Request $request, $id)
    {
        try {
            $paciente = Paciente::findOrFail($id);

            $validated = $request->validate([
                'tipo_documento'   => 'required|string|max:20',
                'documento'        => 'required|string|max:20|unique:pacientes,documento,' . $id,
                'nombres'          => 'required|string|max:255',
                'apellidos'        => 'required|string|max:255',
                'telefono'         => 'nullable|string|max:20',
                'direccion'        => 'nullable|string|max:255',
                'email'            => 'nullable|email|unique:pacientes,email,' . $id,
                'fecha_nacimiento' => 'nullable|date',
                'sexo'             => 'nullable|in:M,F',
            ]);

            $validated['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;
            $paciente->update($validated);

            return response()->json([
                'mensaje' => 'Paciente actualizado correctamente.',
                'paciente' => $paciente
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['mensaje' => 'Error interno al actualizar el paciente.'], 500);
        }
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

        $pacientes = $query->paginate(10)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }
}
