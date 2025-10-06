<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;
use Illuminate\Support\Facades\Validator;

class PlantillaController extends Controller
{
    public function index()
    {
        $id = null;
        $plantilla = null;
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.optometria', compact('id', 'plantilla', 'citas'));
    }

    public function create($id = null)
    {
        $plantilla = null;
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.optometria', compact('id', 'plantilla', 'citas'));
    }

    public function show($id)
    {
        $cita = Cita::findOrFail($id);
        $plantilla = Plantilla_Optometria::where('id', $id)->first();

        return view('plantillas.optometria', compact('cita', 'plantilla'));
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
            'queratometria_cilindro_od',
            'queratometria_eje_od',
            'queratometria_kplano_od',
            'queratometria_cilindro_oi',
            'queratometria_eje_oi',
            'queratometria_kplano_oi',
            'objetivo_espera_od',
            'objetivo_cilindro_od',
            'objetivo_eje_od',
            'objetivo_lejos_od',
            'objetivo_espera_oi',
            'objetivo_cilindro_oi',
            'objetivo_eje_oi',
            'objetivo_lejos_oi',
            'subjetivo_esfera_od',
            'subjetivo_cilindro_od',
            'subjetivo_eje_od',
            'subjetivo_adicion_od',
            'subjetivo_lejos_od',
            'subjetivo_intermedia_od',
            'subjetivo_pin_hole_od',
            'subjetivo_cerca_od',
            'subjetivo_esfera_oi',
            'subjetivo_cilindro_oi',
            'subjetivo_eje_oi',
            'subjetivo_adicion_oi',
            'subjetivo_lejos_oi',
            'subjetivo_intermedia_oi',
            'subjetivo_pin_hole_oi',
            'subjetivo_cerca_oi',
            'observaciones_internas',
            'observaciones_optometria',
            'cicloplejia_esfera_od',
            'cicloplejia_cilindro_od',
            'cicloplejia_eje_od',
            'cicloplejia_lejos_od',
            'cicloplejia_esfera_oi',
            'cicloplejia_cilindro_oi',
            'cicloplejia_eje_oi',
            'cicloplejia_lejos_oi',
            'rx_final_esfera_od',
            'rx_final_cilindro_od',
            'rx_final_eje_od',
            'rx_final_adicion_od',
            'rx_final_esfera_oi',
            'rx_final_cilindro_oi',
            'rx_final_eje_oi',
            'rx_final_adicion_oi',
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

        return redirect()->route('citas.index')->with('success', 'Plantilla guardada correctamente.');
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
            'queratometria_cilindro_od',
            'queratometria_eje_od',
            'queratometria_kplano_od',
            'queratometria_cilindro_oi',
            'queratometria_eje_oi',
            'queratometria_kplano_oi',
            'objetivo_espera_od',
            'objetivo_cilindro_od',
            'objetivo_eje_od',
            'objetivo_lejos_od',
            'objetivo_espera_oi',
            'objetivo_cilindro_oi',
            'objetivo_eje_oi',
            'objetivo_lejos_oi',
            'subjetivo_esfera_od',
            'subjetivo_cilindro_od',
            'subjetivo_eje_od',
            'subjetivo_adicion_od',
            'subjetivo_lejos_od',
            'subjetivo_intermedia_od',
            'subjetivo_pin_hole_od',
            'subjetivo_cerca_od',
            'subjetivo_esfera_oi',
            'subjetivo_cilindro_oi',
            'subjetivo_eje_oi',
            'subjetivo_adicion_oi',
            'subjetivo_lejos_oi',
            'subjetivo_intermedia_oi',
            'subjetivo_pin_hole_oi',
            'subjetivo_cerca_oi',
            'observaciones_internas',
            'observaciones_optometria',
            'cicloplejia_esfera_od',
            'cicloplejia_cilindro_od',
            'cicloplejia_eje_od',
            'cicloplejia_lejos_od',
            'cicloplejia_esfera_oi',
            'cicloplejia_cilindro_oi',
            'cicloplejia_eje_oi',
            'cicloplejia_lejos_oi',
            'rx_final_esfera_od',
            'rx_final_cilindro_od',
            'rx_final_eje_od',
            'rx_final_adicion_od',
            'rx_final_esfera_oi',
            'rx_final_cilindro_oi',
            'rx_final_eje_oi',
            'rx_final_adicion_oi',
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
                'queratometria_cilindro_od' => 'required|string|max:20',
                'queratometria_eje_od' => 'required|string|max:20',
                'queratometria_kplano_od' => 'required|string|max:20',
                'queratometria_cilindro_oi' => 'required|string|max:20',
                'queratometria_eje_oi' => 'required|string|max:20',
                'queratometria_kplano_oi' => 'required|string|max:20',
                'objetivo_espera_od' => 'required|string|max:20',
                'objetivo_cilindro_od' => 'required|string|max:20',
                'objetivo_eje_od' => 'required|string|max:20',
                'objetivo_lejos_od' => 'required|string|max:20',
                'objetivo_espera_oi' => 'required|string|max:20',
                'objetivo_cilindro_oi' => 'required|string|max:20',
                'objetivo_eje_oi' => 'required|string|max:20',
                'objetivo_lejos_oi' => 'required|string|max:20',
                'subjetivo_esfera_od' => 'required|string|max:20',
                'subjetivo_cilindro_od' => 'required|string|max:20',
                'subjetivo_eje_od' => 'required|string|max:20',
                'subjetivo_adicion_od' => 'required|string|max:20',
                'subjetivo_lejos_od' => 'required|string|max:20',
                'subjetivo_intermedia_od' => 'required|string|max:20',
                'subjetivo_pin_hole_od' => 'required|string|max:20',
                'subjetivo_cerca_od' => 'required|string|max:20',
                'subjetivo_esfera_oi' => 'required|string|max:20',
                'subjetivo_cilindro_oi' => 'required|string|max:20',
                'subjetivo_eje_oi' => 'required|string|max:20',
                'subjetivo_adicion_oi' => 'required|string|max:20',
                'subjetivo_lejos_oi' => 'required|string|max:20',
                'subjetivo_intermedia_oi' => 'required|string|max:20',
                'subjetivo_pin_hole_oi' => 'required|string|max:20',
                'subjetivo_cerca_oi' => 'required|string|max:20',
                'observaciones_internas' => 'required|string',
                'observaciones_optometria' => 'required|string',
                'cicloplejia_esfera_od' => 'required|string|max:20',
                'cicloplejia_cilindro_od' => 'required|string|max:20',
                'cicloplejia_eje_od' => 'required|string|max:20',
                'cicloplejia_lejos_od' => 'required|string|max:20',
                'cicloplejia_esfera_oi' => 'required|string|max:20',
                'cicloplejia_cilindro_oi' => 'required|string|max:20',
                'cicloplejia_eje_oi' => 'required|string|max:20',
                'cicloplejia_lejos_oi' => 'required|string|max:20',
                'rx_final_esfera_od' => 'required|string|max:20',
                'rx_final_cilindro_od' => 'required|string|max:20',
                'rx_final_eje_od' => 'required|string|max:20',
                'rx_final_adicion_od' => 'required|string|max:20',
                'rx_final_esfera_oi' => 'required|string|max:20',
                'rx_final_cilindro_oi' => 'required|string|max:20',
                'rx_final_eje_oi' => 'required|string|max:20',
                'rx_final_adicion_oi' => 'required|string|max:20',
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
