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
        'diagnostico',
        'conducta',
        'created_by',     
        'updated_by',      
    ];

    protected $casts = [
        'antecedentes' => 'array',
        'signos_vitales' => 'array',
    ];

    // Relaciones
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

    public function getNombreCreadorAttribute()
    {
        return $this->creador ? $this->creador->nombres . ' ' . $this->creador->apellidos : null;
    }

    public function getNombreActualizadorAttribute()
    {
        return $this->actualizador ? $this->actualizador->nombres . ' ' . $this->actualizador->apellidos : null;
    }
}
