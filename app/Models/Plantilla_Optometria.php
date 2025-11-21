<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla_Optometria extends Model
{
    use HasFactory;

    protected $table = 'optometria';

    protected $fillable = [
        'cita_id',
        'optometra',
        'consulta_completa',
        'anamnesis',
        'alternativa_deseada',
        'dominancia_ocular',
        'av_lejos_od',
        'av_intermedia_od',
        'av_cerca_od',
        'av_lejos_oi',
        'av_intermedia_oi',
        'av_cerca_oi',
        'observaciones_internas',
        'observaciones_optometria',
        'observaciones_formula',
        'tipo_lente',
        'especificaciones_lente',
        'vigencia_formula',
        'filtro',
        'tiempo_formulacion',
        'distancia_pupilar',
        'cantidad',
        'medicamento_principal',
        'otros_medicamentos',
        'notas_medicamento',
        'finalidad_consulta',
        'causa_motivo_atencion',
    ];

    public function cita()
    {
        return $this->belongsTo(\App\Models\Cita::class, 'cita_id');
    }
}
