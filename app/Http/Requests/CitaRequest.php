<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cita;
use Carbon\Carbon;

class CitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['sometimes', 'required', 'date', 'after_or_equal:' . now()->toDateString()],
            'hora_inicio' => ['sometimes', 'required', 'date_format:H:i'],
            'motivo_consulta' => ['sometimes', 'required', 'string', 'min:3', 'max:255', 'not_regex:/^\s*$/'],
            'paciente_id' => ['sometimes', 'required', 'integer', 'exists:pacientes,id'],
            
            'tipo_cita_id' => ['sometimes', 'required', 'integer', 'exists:tipos_citas,id'],
            
            'tipo_examen' => [
                'required_if:tipo_cita_id,2', 
                'nullable', 
                'string', 
                'in:inyeccion_intravitrea,fotocoagulacion_laser,cirugia_retina'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha de la cita es obligatoria.',
            'fecha.date' => 'Debe ser una fecha válida.',
            'fecha.after_or_equal' => 'No puedes crear citas en fechas pasadas.',
            'hora_inicio.required' => 'Debe especificar la hora de inicio.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:mm.',
            'hora_fin.required' => 'Debe especificar la hora de finalización.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:mm.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'motivo_consulta.required' => 'El motivo de consulta es obligatorio.',
            'motivo_consulta.min' => 'El motivo debe tener al menos 3 caracteres.',
            'motivo_consulta.not_regex' => 'El motivo no puede estar vacío o solo tener espacios.',
            'paciente_id.required' => 'Debe seleccionar un paciente.',
            'paciente_id.exists' => 'El paciente seleccionado no existe.',
            'tipo_cita_id.required' => 'Debe seleccionar un tipo de cita.',
            'tipo_cita_id.exists' => 'El tipo de cita seleccionado no es válido.',
            'tipo_examen.required_if' => 'El tipo de examen es obligatorio para la opción de Exámenes.',
            'tipo_examen.in' => 'El tipo de examen seleccionado no es válido.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fecha = $this->input('fecha');
            $horaInicio = $this->input('hora_inicio');
            $horaFin = $this->input('hora_fin');

            $citaId = $this->route('cita') ? $this->route('cita')->id : null;

            if (!$fecha || !$horaInicio || !$horaFin) return;

            $fechaHoraInicio = Carbon::parse("$fecha $horaInicio");
            $ahora = Carbon::now();

            if ($fechaHoraInicio->isToday() && $fechaHoraInicio->lt($ahora)) {
                $validator->errors()->add('hora_inicio', 'No puedes crear citas en horas pasadas.');
                return;
            }

            $citas = Cita::whereDate('fecha', $fecha)
                ->when($citaId, fn($q) => $q->where('id', '!=', $citaId))
                ->get(['hora_inicio', 'hora_fin']);

            foreach ($citas as $citaExistente) {
                if (($horaInicio < $citaExistente->hora_fin) && ($horaFin > $citaExistente->hora_inicio)) {
                    $validator->errors()->add('hora_inicio', 'Ya existe una cita programada en este horario.');
                    break;
                }
            }
        });
    }
}