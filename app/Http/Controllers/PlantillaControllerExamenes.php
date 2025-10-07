<?php

namespace App\Http\Controllers;

use App\Models\Plantilla_Examenes;
use Illuminate\Http\Request;

class PlantillaControllerExamenes extends Controller
{
    public function index()
    {
        $examenes = Plantilla_Examenes::all();
        return response()->json($examenes);
    }

    public function store(Request $request)
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

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_examenes', 'public');
        }

        $examen = Plantilla_Examenes::create([
            'profesional' => $request->profesional,
            'tipoExamen' => $request->tipoExamen,
            'ojo' => $request->ojo,
            'archivo' => $archivoPath,
            'observaciones' => $request->observaciones,
            'codigoCiex' => $request->codigoCiex,
            'diagnostico' => $request->diagnostico,
            'ojoDiag' => $request->ojoDiag
        ]);

        return response()->json(['message' => 'Examen creado correctamente', 'data' => $examen], 201);
    }

    

    public function update(Request $request, $id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);

        $request->validate([
            'profesional' => 'sometimes|required|string|max:255',
            'tipoExamen' => 'sometimes|required|string|max:255',
            'ojo' => 'sometimes|required|string|in:Ojo Derecho,Ojo Izquierdo',
            'archivo' => 'nullable|mimes:pdf|max:2048',
            'observaciones' => 'nullable|string',
            'codigoCiex' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string',
            'ojoDiag' => 'nullable|string|in:Ojo Derecho,Ojo Izquierdo'
        ]);

        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_examenes', 'public');
            $examen->archivo = $archivoPath;
        }

        $examen->update($request->except('archivo'));

        return response()->json(['message' => 'Examen actualizado correctamente', 'data' => $examen]);
    }

    public function destroy($id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);
        $examen->delete();
        return response()->json(['message' => 'Examen eliminado correctamente']);
    }
}
