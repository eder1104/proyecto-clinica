<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteAgenda extends Model
{
    use HasFactory;

    protected $table = 'reportes_agenda';

    protected $fillable = [
        'fecha',
        'total_horarios',
        'horarios_ocupados',
        'horarios_bloqueados',
        'citas_programadas',
        'citas_canceladas',
        'citas_atendidas',
    ];
}
