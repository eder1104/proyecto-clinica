<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Http\Requests\PacientesRequest;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\BitacoraAuditoriaController;

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

        $observacion = "Creación de nuevo paciente: {$paciente->nombres} {$paciente->apellidos} (Documento: {$paciente->documento}).";

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'pacientes',
            'crear',
            $paciente->id,
            array_merge($validated, ['observacion' => $observacion])
        );

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(PacientesRequest $request, Paciente $paciente)
    {
        $validated = $request->validated();

        $datosAnteriores = $paciente->toArray();

        $validated['updated_by'] = Auth::user()->nombres . ' ' . Auth::user()->apellidos;

        $paciente->fill($validated);

        if ($paciente->isDirty()) {

            Paciente::withoutEvents(function () use ($paciente) {
                $paciente->save();
            });

            $datosNuevos = $paciente->toArray();

            $observacion = "Actualización de datos del paciente ID {$paciente->id}.";
            $datosBitacora = array_merge($validated, ['observacion' => $observacion]);

            $bitacoraId = BitacoraAuditoriaController::registrar(
                Auth::id(),
                'pacientes',
                'editar',
                $paciente->id,
                $datosBitacora
            );

            $diferencias = array_diff_assoc($datosNuevos, $datosAnteriores);

            if (!empty($diferencias)) {
                BitacoraAuditoriaController::registrarCambio(
                    $bitacoraId,
                    $paciente->id,
                    $datosAnteriores,
                    $datosNuevos
                );
            }
        }

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $datosEliminados = $paciente->toArray();
        $observacion = "Eliminación del paciente ID {$paciente->id}: {$paciente->nombres} {$paciente->apellidos}.";

        $idEliminado = $paciente->id;

        Paciente::withoutEvents(function () use ($paciente) {
            $paciente->update([
                'cancelled_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
            ]);
            $paciente->delete();
        });

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'pacientes',
            'eliminar',
            $idEliminado,
            array_merge($datosEliminados, ['observacion' => $observacion])
        );

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

        $paciente = Paciente::where('tipo_documento', $request->tipo)
            ->where('documento', $request->numero)
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

            $paciente->update($validated);

            return response()->json([
                'mensaje'  => 'Paciente actualizado correctamente.',
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
