<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PacientesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paciente = $this->route('paciente');
        $id = is_object($paciente) ? $paciente->id : $paciente;

        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'nombres' => [
                'required',
                'string',
                'regex:/^(?!\s*$)[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u',
                'min:3',
                'max:100',
            ],
            'apellidos' => [
                'required',
                'string',
                'regex:/^(?!\s*$)[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u',
                'min:2',
                'max:100',
            ],
            'documento' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:6',
                'max:15',
                $isUpdate
                    ? Rule::unique('pacientes', 'documento')->ignore($id)
                    : Rule::unique('pacientes', 'documento'),
            ],
            'telefono' => [
                'nullable',
                'regex:/^[0-9+\s\-()]{7,20}$/',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                $isUpdate
                    ? Rule::unique('pacientes', 'email')->ignore($id)
                    : Rule::unique('pacientes', 'email'),
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:255',
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'before:today',
            ],
            'sexo' => [
                'nullable',
                'in:M,F',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'El campo nombres es obligatorio.',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'nombres.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombres.max' => 'El nombre no puede exceder los 100 caracteres.',

            'apellidos.required' => 'El campo apellidos es obligatorio.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'apellidos.min' => 'El apellido debe tener al menos 2 caracteres.',
            'apellidos.max' => 'El apellido no puede exceder los 100 caracteres.',

            'documento.required' => 'El número de documento es obligatorio.',
            'documento.regex' => 'El documento solo puede contener números.',
            'documento.unique' => 'Este documento ya está registrado.',
            'documento.min' => 'El documento debe tener al menos 6 dígitos.',
            'documento.max' => 'El documento no puede superar los 15 dígitos.',

            'telefono.regex' => 'El teléfono tiene un formato no válido.',

            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',

            'direccion.max' => 'La dirección no puede exceder los 255 caracteres.',

            'fecha_nacimiento.date' => 'Debe ingresar una fecha válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',

            'sexo.in' => 'El valor del sexo debe ser M o F.',
        ];
    }
}
