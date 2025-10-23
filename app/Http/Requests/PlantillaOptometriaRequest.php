<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlantillaOptometriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Si no se marcó "consulta_completa", solo validamos el optómetra
        if (!$this->has('consulta_completa')) {
            return [
                'optometra' => 'required|integer|exists:doctores,id',
            ];
        }

        // Si está marcada, aplicamos todas las reglas completas
        return [
            'optometra' => 'required|integer|exists:doctores,id',

            'consulta_completa' => 'nullable|boolean',
            'anamnesis' => 'required|string',
            'alternativa_deseada' => 'required|string|max:255',
            'dominancia_ocular' => 'required|string|max:50',

            'av_lejos_od' => 'required|string|max:20',
            'av_intermedia_od' => 'required|string|max:20',
            'av_cerca_od' => 'required|string|max:20',
            'av_lejos_oi' => 'required|string|max:20',
            'av_intermedia_oi' => 'required|string|max:20',
            'av_cerca_oi' => 'required|string|max:20',

            'observaciones_optometria' => 'required|string',
            'tipo_lente' => 'required|string|max:50',
            'especificaciones_lente' => 'required|string',
            'vigencia_formula' => 'required|string|max:50',
            'filtro' => 'required|string|max:50',
            'tiempo_formulacion' => 'required|string|max:50',
            'distancia_pupilar' => 'required|string|max:10',

            'cantidad' => 'required|integer|min:1|max:9999',
            'diagnostico_principal' => 'required|string|max:255',
            'otros_diagnosticos' => 'required|string',
            'datos_adicionales' => 'required|string',

            'finalidad_consulta' => 'required|string|max:255',
            'causa_motivo_atencion' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'optometra.required' => 'Debe seleccionar el optómetra a cargo.',
            'optometra.exists'   => 'El optómetra seleccionado no existe.',

            'anamnesis.required' => 'Debe ingresar la anamnesis.',
            'alternativa_deseada.required' => 'Debe indicar la alternativa deseada.',
            'dominancia_ocular.required' => 'Debe especificar la dominancia ocular.',

            'av_lejos_od.required' => 'Debe llenar la agudeza visual de lejos OD.',
            'av_lejos_oi.required' => 'Debe llenar la agudeza visual de lejos OI.',

            'observaciones_optometria.required' => 'Debe ingresar observaciones.',
            'tipo_lente.required' => 'Debe indicar el tipo de lente.',
            'vigencia_formula.required' => 'Debe ingresar la vigencia de la fórmula.',

            'cantidad.required' => 'Debe ingresar la cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número.',
            'cantidad.min' => 'La cantidad no puede ser menor que 1.',
            'cantidad.max' => 'La cantidad no puede superar 9999.',

            'diagnostico_principal.required' => 'Debe ingresar el diagnóstico principal.',
            'finalidad_consulta.required' => 'Debe ingresar la finalidad de la consulta.',
            'causa_motivo_atencion.required' => 'Debe ingresar la causa o motivo de atención.',
        ];
    }
}
