<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla_Examenes extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $fillable = [
        'profesional',
        'tipoExamen',
        'ojo',
        'archivo',
        'observaciones',
        'codigoCiex',
        'diagnostico',
        'ojoDiag'
    ];
}
