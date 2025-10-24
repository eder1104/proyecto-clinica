<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user') ?? $this->route('id');
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'nombres' => [
                'required',
                'filled',
                'string',
                'regex:/^(?!\s*$)[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u',
                'min:3',
                'max:100',
            ],
            'apellidos' => [
                'required',
                'filled',
                'string',
                'regex:/^(?!\s*$)[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u',
                'min:2',
                'max:100',
            ],
            'email' => [
                'required',
                'filled',
                'email',
                'max:255',
                $isUpdate
                    ? 'unique:users,email,' . $id
                    : 'unique:users,email',
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required'   => 'El campo nombres es obligatorio.',
            'nombres.regex'      => 'El nombre solo puede contener letras y espacios.',
            'apellidos.required' => 'El campo apellidos es obligatorio.',
            'apellidos.regex'    => 'El apellido solo puede contener letras y espacios.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Debe ser un correo electrónico válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria al crear un usuario.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max'       => 'La contraseña no puede exceder los 64 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex'     => 'Debe incluir mayúsculas, minúsculas, números y símbolos.',
        ];
    }
}
