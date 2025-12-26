<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\PacientesRequest;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function store(PacientesRequest $request)
    {
        $validated = $request->validated();

        $paciente = Paciente::create(array_merge(
            $validated,
            ['created_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos]
        ));
        
        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(PacientesRequest $request, Paciente $paciente)
    {
        $validated = $request->validated();
        $validated['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;

        $paciente->fill($validated);

        if ($paciente->isDirty()) {
            Paciente::withoutEvents(function () use ($paciente) {
                $paciente->save();
            });
        }

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        Paciente::withoutEvents(function () use ($paciente) {
            $paciente->update([
                'cancelled_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
            ]);
            $paciente->delete();
        });

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
        $tipo = trim($request->query('tipo'));
        $numero = trim($request->query('numero'));

        if (!$tipo || !$numero) {
            return response()->json(['error' => 'Faltan parámetros de búsqueda'], 400);
        }

        $mapaTipos = [
            'CC' => 5,
            'TI' => 4,
            'CE' => 6,
            'RC' => 3,
            'PA' => 7,
            'PE' => 463
        ];

        $paciente = Paciente::where('documento', $numero)
            ->where(function($q) use ($tipo, $mapaTipos) {
                $q->where('tipo_documento', $tipo);
                
                if (isset($mapaTipos[$tipo])) {
                    $q->orWhere('tipo_documento', $mapaTipos[$tipo]);
                }
            })
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
                'documento'        => [
                    'required', 
                    'string', 
                    'max:20', 
                    Rule::unique('pacientes', 'documento')->ignore($paciente->id)
                ],
                'nombres'          => 'required|string|max:255',
                'apellidos'        => 'required|string|max:255',
                'telefono'         => 'nullable|string|max:20',
                'direccion'        => 'nullable|string|max:255',
                'email'            => [
                    'nullable', 
                    'email', 
                    Rule::unique('pacientes', 'email')->ignore($paciente->id)
                ],
                'fecha_nacimiento' => 'nullable|date',
                'sexo'             => 'nullable|in:M,F',
            ]);

            $paciente->update($validated);

            return response()->json([
                'mensaje'  => 'Paciente actualizado correctamente.',
                'paciente' => $paciente
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['mensaje' => 'El paciente con ID ' . $id . ' no existe.'], 404);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors(), 'mensaje' => 'Error de validación'], 422);

        } catch (\Throwable $e) {
            return response()->json(['mensaje' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function Paciente_buscar(Request $request)
    {
        $query = Paciente::query();

        if ($request->nombre) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'LIKE', "%{$request->nombre}%")
                  ->orWhere('apellidos', 'LIKE', "%{$request->nombre}%");
            });
        }

        if ($request->documento) {
            $query->where('documento', 'LIKE', "%{$request->documento}%");
        }

        $pacientes = $query->paginate(10)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }
}