<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cita;

class CitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => [
                'required',
                'date',
            ],
            'hora_inicio' => [
                'required',
                'date_format:H:i',
            ],
            'hora_fin' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
            ],
            'motivo_consulta' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'not_regex:/^\s*$/',
            ],
            'paciente_id' => [
                'required',
                'integer',
                'exists:pacientes,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha de la cita es obligatoria.',
            'fecha.date' => 'Debe ser una fecha válida.',
            'hora_inicio.required' => 'Debe especificar la hora de inicio.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:mm.',
            'hora_fin.required' => 'Debe especificar la hora de finalización.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:mm.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'motivo_consulta.required' => 'El motivo de consulta es obligatorio.',
            'motivo_consulta.min' => 'El motivo debe tener al menos 3 caracteres.',
            'motivo_consulta.not_regex' => 'El motivo no puede estar vacío o solo tener espacios.',
            'admisiones_id.required' => 'Debe seleccionar un usuario de admisiones.',
            'admisiones_id.exists' => 'El usuario de admisiones no existe.',
            'paciente_id.required' => 'Debe seleccionar un paciente.',
            'paciente_id.exists' => 'El paciente seleccionado no existe.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fecha      = $this->input('fecha');
            $horaInicio = $this->input('hora_inicio');
            $horaFin    = $this->input('hora_fin');
            $citaId     = $this->route('cita');

            if (!$fecha || !$horaInicio || !$horaFin) {
                return;
            }

            $citas = Cita::whereDate('fecha', $fecha)
                ->when($citaId, fn($q) => $q->where('id', '!=', $citaId))
                ->get(['hora_inicio', 'hora_fin']);

            foreach ($citas as $cita) {
                $inicioExistente = $cita->hora_inicio;
                $finExistente    = $cita->hora_fin;

                if (
                    ($horaInicio < $finExistente) &&
                    ($horaFin > $inicioExistente)
                ) {
                    $validator->errors()->add(
                        'hora_inicio',
                        'Ya existe una cita programada en este horario.'
                    );
                    break;
                }
            }
        });
    }
}
