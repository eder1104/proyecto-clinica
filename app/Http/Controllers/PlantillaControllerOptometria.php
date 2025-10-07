<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;
use Illuminate\Support\Facades\Validator;

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
        $id = $plantilla->id;
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.optometria', compact('id', 'plantilla', 'citas'));
    }

    public function store(Request $request)
    {
        $consultaCompleta = $request->input('consulta_completa', 0) == 1;
        $rules = $this->rules($consultaCompleta);
        $messages = $this->messages();
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'id',
            'optometra',
            'consulta_completa',
            'anamnesis',
            'alternativa_deseada',
            'dominancia_ocular',
            'av_lejos_od',
            'av_intermedia_od',
            'av_cerca_od',
            'av_lejos_oi',
            'av_intermedia_oi',
            'av_cerca_oi',
            'observaciones_internas',
            'observaciones_optometria',
            'observaciones_formula',
            'especificaciones_lente',
            'tipo_lente',
            'vigencia_formula',
            'filtro',
            'tiempo_formulacion',
            'distancia_pupilar',
            'cantidad',
            'diagnostico_principal',
            'otros_diagnosticos',
            'datos_adicionales',
            'finalidad_consulta',
            'causa_motivo_atencion'
        ]);

        $data['consulta_completa'] = $consultaCompleta ? 1 : 0;

        Plantilla_Optometria::updateOrCreate(['id' => $request->input('id')], $data);

        return redirect()->route('citas.index')->with('success', 'Cita guardada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $consultaCompleta = $request->input('consulta_completa', 0) == 1;
        $rules = $this->rules($consultaCompleta);
        $messages = $this->messages();
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'id',
            'optometra',
            'consulta_completa',
            'anamnesis',
            'alternativa_deseada',
            'dominancia_ocular',
            'av_lejos_od',
            'av_intermedia_od',
            'av_cerca_od',
            'av_lejos_oi',
            'av_intermedia_oi',
            'av_cerca_oi',
            'observaciones_internas',
            'observaciones_optometria',
            'observaciones_formula',
            'especificaciones_lente',
            'tipo_lente',
            'vigencia_formula',
            'filtro',
            'tiempo_formulacion',
            'distancia_pupilar',
            'cantidad',
            'diagnostico_principal',
            'otros_diagnosticos',
            'datos_adicionales',
            'finalidad_consulta',
            'causa_motivo_atencion'
        ]);

        $data['consulta_completa'] = $consultaCompleta ? 1 : 0;

        $plantilla->update($data);

        return redirect()->back()->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $plantilla->delete();
        return redirect()->back()->with('success', 'Plantilla eliminada correctamente.');
    }

    private function rules(bool $consultaCompleta = false): array
    {
        $rules = [
            'consulta_completa' => 'nullable|boolean',
        ];

        if ($consultaCompleta) {
            $rules = array_merge($rules, [
                'optometra' => 'required|string|max:255',
                'anamnesis' => 'required|string',
                'alternativa_deseada' => 'required|string|max:255',
                'dominancia_ocular' => 'required|string|max:50',
                'av_lejos_od' => 'required|string|max:20',
                'av_intermedia_od' => 'required|string|max:20',
                'av_cerca_od' => 'required|string|max:20',
                'av_lejos_oi' => 'required|string|max:20',
                'av_intermedia_oi' => 'required|string|max:20',
                'av_cerca_oi' => 'required|string|max:20',
                'observaciones_internas' => 'required|string',
                'observaciones_optometria' => 'required|string',
                'observaciones_formula' => 'required|string',
                'especificaciones_lente' => 'required|string',
                'tipo_lente' => 'required|string|max:50',
                'vigencia_formula' => 'required|string|max:50',
                'filtro' => 'required|string|max:50',
                'tiempo_formulacion' => 'required|string|max:50',
                'distancia_pupilar' => 'required|string|max:10',
                'cantidad' => 'required|integer',
                'diagnostico_principal' => 'required|string|max:255',
                'otros_diagnosticos' => 'required|string',
                'datos_adicionales' => 'required|string',
                'finalidad_consulta' => 'required|string|max:255',
                'causa_motivo_atencion' => 'required|string|max:255',
            ]);
        }

        return $rules;
    }

    private function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no puede exceder :max caracteres.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'exists' => 'El :attribute seleccionado no es válido.',
        ];
    }
}
