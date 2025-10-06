<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla_Optometria extends Model
{
    use HasFactory;

    protected $table = 'citas_optometria';

    protected $fillable = [
        'id',
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
        'queratometria_cilindro_od',
        'queratometria_eje_od',
        'queratometria_kplano_od',
        'queratometria_cilindro_oi',
        'queratometria_eje_oi',
        'queratometria_kplano_oi',
        'objetivo_espera_od',
        'objetivo_cilindro_od',
        'objetivo_eje_od',
        'objetivo_lejos_od',
        'objetivo_espera_oi',
        'objetivo_cilindro_oi',
        'objetivo_eje_oi',
        'objetivo_lejos_oi',
        'subjetivo_esfera_od',
        'subjetivo_cilindro_od',
        'subjetivo_eje_od',
        'subjetivo_adicion_od',
        'subjetivo_lejos_od',
        'subjetivo_intermedia_od',
        'subjetivo_pin_hole_od',
        'subjetivo_cerca_od',
        'subjetivo_esfera_oi',
        'subjetivo_cilindro_oi',
        'subjetivo_eje_oi',
        'subjetivo_adicion_oi',
        'subjetivo_lejos_oi',
        'subjetivo_intermedia_oi',
        'subjetivo_pin_hole_oi',
        'subjetivo_cerca_oi',
        'observaciones_internas',
        'observaciones_optometria',
        'cicloplejia_esfera_od',
        'cicloplejia_cilindro_od',
        'cicloplejia_eje_od',
        'cicloplejia_lejos_od',
        'cicloplejia_esfera_oi',
        'cicloplejia_cilindro_oi',
        'cicloplejia_eje_oi',
        'cicloplejia_lejos_oi',
        'rx_final_esfera_od',
        'rx_final_cilindro_od',
        'rx_final_eje_od',
        'rx_final_adicion_od',
        'rx_final_esfera_oi',
        'rx_final_cilindro_oi',
        'rx_final_eje_oi',
        'rx_final_adicion_oi',
        'observaciones_formula',
        'especificaciones_lente',
        'tipo_lente',
        'vigencia_formula',
        'filtro',
        'tiempo_formulacion',
        'distancia_pupilar',
        'cantidad',
        'diagnostico_principal',
        'otros_diagnosticos',
        'datos_adicionales',
        'finalidad_consulta',
        'causa_motivo_atencion'
    ];

    public function cita()
    {
        return $this->belongsTo(\App\Models\Cita::class, 'id');
    }
}
