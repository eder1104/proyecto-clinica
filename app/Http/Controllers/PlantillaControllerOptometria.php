<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;

class PlantillaControllerOptometria extends Controller
{
    public function index()
    {
        $id = null;
        $plantilla = null;
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.optometria', compact('id', 'plantilla', 'citas'));
    }

    public function edit($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $cita = $plantilla->cita;
        return view('plantillas.optometria', compact('plantilla', 'cita', 'id'));
    }

     public function store(Request $request, $cita)
    {
        $request->validate([
            'optometra' => 'required|string|max:255',
            'consulta_completa' => 'nullable|boolean',
            'anamnesis' => 'nullable|string',
            'alternativa_deseada' => 'nullable|string|max:255',
            'dominancia_ocular' => 'nullable|string|max:50',
            'av_lejos_od' => 'nullable|string|max:20',
            'av_intermedia_od' => 'nullable|string|max:20',
            'av_cerca_od' => 'nullable|string|max:20',
            'av_lejos_oi' => 'nullable|string|max:20',
            'av_intermedia_oi' => 'nullable|string|max:20',
            'av_cerca_oi' => 'nullable|string|max:20',
            'observaciones_internas' => 'nullable|string',
            'observaciones_optometria' => 'nullable|string',
            'observaciones_formula' => 'nullable|string',
            'tipo_lente' => 'nullable|string|max:50',
            'especificaciones_lente' => 'nullable|string',
            'vigencia_formula' => 'nullable|string|max:50',
            'filtro' => 'nullable|string|max:50',
            'tiempo_formulacion' => 'nullable|string|max:50',
            'distancia_pupilar' => 'nullable|string|max:10',
            'cantidad' => 'nullable|integer',
            'diagnostico_principal' => 'nullable|string|max:255',
            'otros_diagnosticos' => 'nullable|string',
            'datos_adicionales' => 'nullable|string',
            'finalidad_consulta' => 'nullable|string|max:255',
            'causa_motivo_atencion' => 'nullable|string|max:255',
        ]);

        $citaRegistro = Cita::findOrFail($cita);

        $data = $request->all();
        $data['cita_id'] = $citaRegistro->id;
        $data['paciente_id'] = $citaRegistro->paciente_id;
        $data['consulta_completa'] = $request->has('consulta_completa') ? 1 : 0;

        Plantilla_Optometria::updateOrCreate(
            ['cita_id' => $data['cita_id']],
            $data
        );

        $citaRegistro->estado = 'finalizada';
        $citaRegistro->save();

        return redirect()->route('citas.index')->with('success', 'Cita y plantilla guardadas correctamente.');
    }
    
    public function update(Request $request, Plantilla_Optometria $plantilla)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'optometra' => 'required|string|max:255',
            'consulta_completa' => 'nullable|boolean',
            'anamnesis' => 'nullable|string',
            'alternativa_deseada' => 'nullable|string|max:255',
            'dominancia_ocular' => 'nullable|string|max:50',
            'av_lejos_od' => 'nullable|string|max:20',
            'av_intermedia_od' => 'nullable|string|max:20',
            'av_cerca_od' => 'nullable|string|max:20',
            'av_lejos_oi' => 'nullable|string|max:20',
            'av_intermedia_oi' => 'nullable|string|max:20',
            'av_cerca_oi' => 'nullable|string|max:20',
            'observaciones_internas' => 'nullable|string',
            'observaciones_optometria' => 'nullable|string',
            'tipo_lente' => 'nullable|string|max:50',
            'especificaciones_lente' => 'nullable|string',
            'vigencia_formula' => 'nullable|string|max:50',
            'filtro' => 'nullable|string|max:50',
            'tiempo_formulacion' => 'nullable|string|max:50',
            'distancia_pupilar' => 'nullable|string|max:10',
            'cantidad' => 'nullable|integer',
            'diagnostico_principal' => 'nullable|string|max:255',
            'otros_diagnosticos' => 'nullable|string',
            'datos_adicionales' => 'nullable|string',
            'finalidad_consulta' => 'nullable|string|max:255',
            'causa_motivo_atencion' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['consulta_completa'] = $request->has('consulta_completa') ? 1 : 0;

        $plantilla->update($data);

        $cita = $plantilla->cita;
        $cita->estado = 'finalizada';
        $cita->save();

        return redirect()->route('citas.index')
            ->with('success', 'Plantilla actualizada y cita finalizada correctamente.');
    }

    public function destroy($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $plantilla->delete();
        return redirect()->back()->with('success', 'Plantilla eliminada correctamente.');
    }
}
