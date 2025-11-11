<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

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

    protected $with = ['paciente'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function getTipoCitaNombreAttribute(): string
    {
        switch ($this->tipo_cita_id) {
            case 1:
                return 'Optometría';
            case 2:
                return 'Exámenes';
            case 3:
                return 'Retina';
            default:
                return 'Sin tipo';
        }
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