<?php

namespace App\Http\Controllers;

use App\Models\Plantilla_Examenes;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Plantilla_Optometria;
use App\Models\User;

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

    public function edit(Cita $cita)
    {
        $cita->load(['paciente', 'TipoCita']);
        $users = User::all();

        if ($cita->tipo_cita_id != 2) {
            return redirect()->back()->with('error', 'Esta cita no corresponde a exámenes.');
        }

        $plantilla = Plantilla_Examenes::firstOrCreate(
            ['cita_id' => $cita->id],
            ['paciente_id' => $cita->paciente_id]
        );

        return view('plantillas.examenes', compact('plantilla', 'cita', 'users'));
    }

    public function atender(Cita $cita)
    {
        $cita->load(['paciente']);
        $historia = $cita->paciente->historiaClinica ?? null;

        return view('optometria.historia', compact('cita', 'historia'));
    }


    public function destroy($id)
    {
        $examen = Plantilla_Examenes::findOrFail($id);
        $examen->delete();
        return response()->json(['message' => 'Examen eliminado correctamente']);
    }
}
