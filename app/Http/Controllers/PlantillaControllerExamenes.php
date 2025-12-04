<?php

namespace App\Http\Controllers;

use App\Models\Plantilla_Examenes;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use App\Http\Controllers\BitacoraAuditoriaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        if ($cita->estado == 'finalizada' || $cita->estado == 'cancelada') {
            return redirect()->route('citas.index')->with('error', 'No se pueden agregar exámenes a una cita finalizada o cancelada.');
        }

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

        if ($plantilla) {
            $plantilla->update($data);
        } else {
            Plantilla_Examenes::create(array_merge(
                $data,
                ['paciente_id' => $cita->paciente_id]
            ));
        }

        $cita->update(['estado' => 'finalizada']);

        return redirect()->route('citas.index')->with('success', 'Examen guardado y cita finalizada correctamente.');
    }

    public function edit(Cita $cita)
    {
        if ($cita->estado == 'finalizada' || $cita->estado == 'cancelada') {
            return redirect()->back()->with('error', 'No se puede editar una cita que ya está finalizada o cancelada.');
        }

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
        if ($cita->estado == 'finalizada' || $cita->estado == 'cancelada') {
            return redirect()->route('citas.index')->with('error', 'No se puede actualizar una cita finalizada o cancelada.');
        }

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

        $archivoPath = $plantilla->archivo ?? null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_examenes', 'public');
        }

        $data = $request->all();
        $data['archivo'] = $archivoPath;

        if ($plantilla) {
            $plantilla->update($data);
        } else {
            Plantilla_Examenes::create(array_merge(
                $data,
                ['cita_id' => $cita->id, 'paciente_id' => $cita->paciente_id]
            ));
        }

        $cita->update(['estado' => 'finalizada']);

        return redirect()->route('historias.index', $cita->id)
            ->with('success', 'Examen actualizado y cita finalizada correctamente.');
    }

    public function destroy($id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);
        
        $cita = Cita::find($examen->cita_id);
        if ($cita && ($cita->estado == 'finalizada' || $cita->estado == 'cancelada')) {
            return response()->json(['message' => 'No se puede eliminar el examen de una cita finalizada'], 403);
        }

        $examen->delete();

        return response()->json(['message' => 'Examen eliminado correctamente']);
    }
}