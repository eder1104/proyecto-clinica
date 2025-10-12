<?php

namespace App\Http\Controllers;

use App\Models\Plantilla_Examenes;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlantillaControllerExamenes extends Controller
{
    public function index()
    {
        $examenes = Plantilla_Examenes::all();
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.examenes', compact('examenes', 'citas'));
    }

    public function store(Request $request, User $user)
    {
        $citaId = $request->input('cita');
        $citaRegistro = Cita::findOrFail($citaId);

        $request->validate([
            'cita' => 'required|exists:citas,id',
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

        $data = $request->all();
        $data['cita_id'] = $citaRegistro->id;
        $data['archivo'] = $archivoPath;
       
        Plantilla_Examenes::updateOrCreate(
            ['cita_id' => $data['cita_id']],
            $data
        );

        $citaRegistro->update([
            'estado' => 'finalizada'
        ]);

        return redirect()->route('citas.index')->with('success', 'Examen guardado y cita finalizada correctamente.');
    }



    public function destroy($id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);
        $examen->delete();
        return response()->json(['message' => 'Examen eliminado correctamente']);
    }
}
