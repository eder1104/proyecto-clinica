<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cita_id',
        'numero_fuente',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'mensaje',
        'estado',
        'paciente_id',
        'admisiones_id',
        'motivo_consulta',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'tension_arterial',
        'temperatura',
        'saturacion',
        'peso',
        'examen_fisico',
        'diagnostico',
        'created_by',
        'updated_by',
        'cancelled_by',
        'cancel_reason',
        'pdf_path',
    ];

    protected $with = ['paciente', 'admisiones'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function admisiones()
    {
        return $this->belongsTo(User::class, 'admisiones_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function TipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'tipo_cita_id');
    }
}
