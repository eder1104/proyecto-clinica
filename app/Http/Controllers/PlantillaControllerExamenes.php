<?php

namespace App\Http\Controllers;

use App\Models\Plantilla_Examenes;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Http\Controllers\BitacoraAuditoriaController;
use Illuminate\Support\Facades\Auth;

class PlantillaControllerExamenes extends Controller
{
    public function index()
    {
        $examenes = Plantilla_Examenes::all();
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        $users = User::where('role', 'admisiones')->get();
        
        return view('plantillas.examenes', compact('examenes', 'citas', 'users'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'profesional' => 'required|string|max:255',
            'tipoExamen' => 'required|string|max:255',
            'ojo' => 'required|string|in:Ojo Derecho,Ojo Izquierdo',
            'archivo' => 'nullable|max:2048',
            'observaciones' => 'nullable|string',
            'codigoCiex' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string',
            'ojoDiag' => 'nullable|string|in:Ojo Derecho,Ojo Izquierdo'
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_examenes', 'public');
        }

        $data = $request->all();
        $data['cita_id'] = $cita->id;
        $data['archivo'] = $archivoPath;

        $plantilla = Plantilla_Examenes::where('cita_id', $cita->id)->first();
        $accion = '';

        if ($plantilla) {
            $plantilla->update($data);
            $accion = 'Actualizar';
        } else {
            $plantilla = Plantilla_Examenes::create(array_merge(
                $data,
                ['paciente_id' => $cita->paciente_id]
            ));
            $accion = 'Crear';
        }

        $cita->update(['estado' => 'finalizada']);

        $observacion = "{$accion} examen de tipo {$data['tipoExamen']} para la cita ID {$cita->id}.";
        $datosBitacora = array_merge($data, ['observacion' => $observacion]);

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'examenes',
            $accion,
            $plantilla->id,
            $datosBitacora
        );

        return redirect()->route('citas.index')->with('success', 'Examen guardado y cita finalizada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $cita->load(['paciente']);
        $users = User::all();

        if ($cita->tipo_cita_id != 2) {
            return redirect()->back()->with('error', 'Esta cita no corresponde a exámenes.');
        }

        $plantilla = Plantilla_Examenes::where('cita_id', $cita->id)->first();

        return view('historias.examenes_edit', compact('plantilla', 'cita', 'users'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'profesional' => 'required|string|max:255',
            'tipoExamen' => 'required|string|max:255',
            'ojo' => 'required|string|in:Ojo Derecho,Ojo Izquierdo',
            'archivo' => 'nullable|mimes:pdf|max:2048',
            'observaciones' => 'nullable|string',
            'codigoCiex' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string',
            'ojoDiag' => 'nullable|string|in:Ojo Derecho,Ojo Izquierdo'
        ]);

        $plantilla = Plantilla_Examenes::where('cita_id', $cita->id)->first();
        $datosAnteriores = $plantilla ? $plantilla->toArray() : [];

        $archivoPath = $plantilla->archivo ?? null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_examenes', 'public');
        }

        $data = $request->all();
        $data['archivo'] = $archivoPath;

        if ($plantilla) {
            $plantilla->update($data);
        } else {
            $plantilla = Plantilla_Examenes::create(array_merge(
                $data,
                ['cita_id' => $cita->id, 'paciente_id' => $cita->paciente_id]
            ));
        }

        $datosNuevos = $plantilla->fresh()->toArray();
        $observacion = "Edición de examen ID {$plantilla->id} (Cita {$cita->id}).";
        $datosBitacora = array_merge($data, ['observacion' => $observacion]);

        $bitacoraId = BitacoraAuditoriaController::registrar(
            Auth::id(),
            'examenes',
            'Editar',
            $plantilla->id,
            $datosBitacora
        );

        if ($datosAnteriores) {
            BitacoraAuditoriaController::registrarCambio(
                $bitacoraId,
                $plantilla->id,
                $datosAnteriores,
                $datosNuevos
            );
        }

        return redirect()->route('historias.index', $cita->id)
            ->with('success', 'Examen actualizado correctamente.');
    }

    public function destroy($id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);
        
        $datosEliminados = $examen->toArray();
        $observacion = "Eliminación de examen ID {$examen->id} del tipo {$examen->tipoExamen}.";
        $datosBitacora = array_merge($datosEliminados, ['observacion' => $observacion]);

        $idEliminado = $examen->id;
        $examen->delete();

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'examenes',
            'Eliminar',
            $idEliminado,
            $datosBitacora
        );

        return response()->json(['message' => 'Examen eliminado correctamente']);
    }
}