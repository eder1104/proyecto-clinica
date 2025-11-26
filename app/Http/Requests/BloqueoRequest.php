<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Doctores;
use App\Models\BloqueoAgenda;

class BloqueoRequest extends FormRequest
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

            'motivo' => [
                'nullable',
                'string',
                'max:255',
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
                    $currentBloqueoId = $this->route('id');

                    $existing = BloqueoAgenda::where('doctor_id', $doctor_table_id)
                        ->where('fecha', $fecha)
                        ->where(function ($query) use ($hora_inicio, $hora_fin) {
                            $query->where('hora_inicio', '<', $hora_fin)
                                  ->where('hora_fin', '>', $hora_inicio);
                        });
                        
                    if ($currentBloqueoId) {
                         $existing->where('id', '!=', $currentBloqueoId);
                    }

                    if ($existing->exists()) {
                        $fail('El rango seleccionado (' . $hora_inicio . ' - ' . $hora_fin . ') choca con un bloqueo existente.');
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
            'fecha.date_format' => 'La fecha no tiene un formato válido.',
            'fecha.after_or_equal' => 'No se puede bloquear una fecha pasada.',
            
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'Formato inválido para hora inicio (use HH:MM).',
            'hora_inicio.after_or_equal' => 'La hora inicio debe ser desde las 08:00.',
            'hora_inicio.before_or_equal' => 'La hora inicio debe ser hasta las 18:00.',

            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'Formato inválido para hora fin (use HH:MM).',
            'hora_fin.after' => 'La hora fin debe ser mayor a la hora de inicio.',
            'hora_fin.after_or_equal' => 'La hora fin debe ser desde las 08:00.',
            'hora_fin.before_or_equal' => 'La hora fin debe ser hasta las 18:00.',
        ];
    }
}