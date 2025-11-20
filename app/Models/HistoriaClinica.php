<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoriaClinica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'motivo_consulta',
        'antecedentes',
        'signos_vitales',
        'diagnostico_principal_id',
        'conducta',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'antecedentes' => 'array',
        'signos_vitales' => 'array',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function diagnostico()
    {
        return $this->belongsTo(DiagnosticoOftalmologico::class, 'diagnostico_principal_id');
    }

    public function procedimientos()
{
    return $this->belongsToMany(
        ProcedimientoOftalmologico::class,
        'historia_clinica_procedimiento',
        'historia_clinica_id',
        'procedimiento_id'
    )
    ->withPivot('cita_id')
    ->withTimestamps();
}

}
