<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla_Examenes extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $fillable = [
        'cita_id',
        'profesional',
        'tipoExamen',
        'ojo',
        'archivo',
        'observaciones',
        'codigoCiex',
        'diagnostico',
        'ojoDiag',
    ];

    public function cita()
    {
        return $this->belongsTo(\App\Models\Cita::class, 'id');
    }
}
