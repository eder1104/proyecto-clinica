<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;
use App\Models\User;

class PlantillaControllerOptometria extends Controller
{
    public function index()
    {
        $id = null;
        $plantilla = null;
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.optometria', compact('id', 'plantilla', 'citas'));
    }

    public function edit(Cita $cita)
    {
        $cita->load(['paciente', 'TipoCita']);
        $users = User::all();
        $plantilla = Plantilla_Optometria::firstOrCreate(
            ['cita_id' => $cita->id],
            [
                'paciente_id' => $cita->paciente_id,
            ]
        );

        return view('plantillas.optometria', compact('plantilla', 'cita', 'users'));
    }

    public function store(Request $request, Cita $cita)
    {
        if (in_array($request->estado, ['finalizada', 'cancelada'])) {
            return redirect()->route('citas.index')
                ->with('error', 'No se puede crear una cita con estado ' . $request->estado . '.');
        }

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

        $data = $request->except(['_token', '_method', 'id']);
        $data['paciente_id'] = $cita->paciente_id;
        $data['cita_id'] = $cita->id;
        $data['consulta_completa'] = $request->has('consulta_completa') ? 1 : 0;

        Plantilla_Optometria::updateOrCreate(
            ['cita_id' => $cita->id],
            $data
        );

        dd(config('app.debug'));

        $cita->update([
            'estado' => 'finalizada'
        ]);

        return redirect()->route('historias.historia', ['paciente' => $cita->paciente_id])
            ->with('success', 'Cita y plantilla guardadas correctamente.');
    }


    public function atender(Cita $cita)
    {
        $cita->load(['paciente']);
        $historia = $cita->paciente->historiaClinica ?? null;

        return view('optometria.historia', compact('cita', 'historia'));
    }

    public function show($id)
    {
        $plantilla = Plantilla_Optometria::with(['cita.paciente'])->findOrFail($id);
        return view('plantillas.optometria_show', compact('plantilla'));
    }


    public function update(Request $request, Cita $cita)
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

        $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->firstOrFail();

        $data = $request->all();
        $data['consulta_completa'] = $request->has('consulta_completa') ? 1 : 0;

        $plantilla->update($data);

        return redirect()->route('historias.cita', ['paciente' => $cita->paciente_id])
            ->with('success', 'Plantilla de optometría actualizada correctamente.');
    }


    public function destroy($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $plantilla->delete();
        return redirect()->back()->with('success', 'Plantilla eliminada correctamente.');
    }
}
