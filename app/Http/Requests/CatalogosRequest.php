<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => 'required|exists:pacientes,id',
            'items_ids'   => 'nullable|array',
            'items_tipos' => 'nullable|array',
            'items_ids.*' => 'nullable|integer',
            'items_tipos.*' => 'nullable|string|in:alergia,procedimiento,diagnostico',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tipos = $this->input('items_tipos', []);

            if (!is_array($tipos)) {
                $tipos = [];
            }

            $diagnosticosCount = collect($tipos)->filter(fn($t) => $t === 'diagnostico')->count();

            if ($diagnosticosCount > 1) {
                $validator->errors()->add(
                    'items_tipos',
                    'Solo se permite un diagnóstico por consulta.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Debe enviar el paciente.',
            'historia_id.required' => 'Debe enviar la historia clínica.',
            'items_ids.array' => 'La lista de IDs no es válida.',
            'items_tipos.array' => 'La lista de tipos no es válida.',
            'items_tipos.*.in' => 'Uno de los tipos enviados no es válido.',
        ];
    }
}
