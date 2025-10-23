<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'created_by',   
        'paciente_id',
        'updated_by',  
        'cancelled_by',
        'cancel_reason',
        'tipo_cita_id',
    ];

    protected $with = ['paciente', 'tipoCita'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function tipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'tipo_cita_id');
    }

    public function getCreadoPorAttribute()
    {
        return $this->created_by ?? 'No registrado';
    }

    public function getActualizadoPorAttribute()
    {
        return $this->updated_by ?? 'No registrado';
    }

    public function getCanceladoPorAttribute()
    {
        return $this->cancelled_by ?? 'No registrado';
    }
}
