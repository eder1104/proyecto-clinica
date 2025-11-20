<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Doctores;
use App\Models\DoctorParcialidad;

class ParcialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'doctor_id' => [
            'required',
            'integer',
            'exists:users,id',
        ],

        'fecha' => [
            'required',
            'date',
            'date_format:Y-m-d',
            'after_or_equal:today',
        ],

        'hora_inicio' => [
            'required',
            'date_format:H:i',
            'after_or_equal:08:00', 
            'before_or_equal:18:00',
        ],

        'hora_fin' => [
            'required',
            'date_format:H:i',
            'after:hora_inicio',
            'after_or_equal:08:00',
            'before_or_equal:18:00',

            function ($attribute, $value, $fail) {
                $doctor_user_id = $this->input('doctor_id');
                $fecha = $this->input('fecha');
                $hora_inicio = $this->input('hora_inicio');
                $hora_fin = $value;

                $doctorProfile = Doctores::where('user_id', $doctor_user_id)->first();
                if (!$doctorProfile) {
                    return;
                }

                $doctor_table_id = $doctorProfile->id;

                $existing = DoctorParcialidad::where('doctor_id', $doctor_table_id)
                    ->where('fecha', $fecha)
                    ->where(function ($query) use ($hora_inicio, $hora_fin) {
                        $query->where('hora_inicio', '<', $hora_fin)
                              ->where('hora_fin', '>', $hora_inicio);
                    })
                    ->exists();

                if ($existing) {
                    $fail('El rango (' . $hora_inicio . ' - ' . $hora_fin . ') se superpone con un rango existente.');
                }
            }
        ],
    ];
}


    public function messages(): array
    {
        return [
            'doctor_id.required' => 'El doctor es obligatorio.',
            'doctor_id.exists' => 'El doctor seleccionado no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no tiene un formato válido.',
            'fecha.after_or_equal' => 'No se puede definir una disponibilidad para una fecha pasada.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'El formato de la hora de inicio debe ser HH:MM (ej. 09:00).',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'El formato de la hora de fin debe ser HH:MM (ej. 17:00).',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
