<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreExamen extends Model
{
    use HasFactory;

    protected $table = 'pre_examenes';

    protected $fillable = [
        'cita_id',
        'vision_lejana_od',
        'vision_lejana_oi',
        'vision_cercana_od',
        'vision_cercana_oi',
        'test_color',
        'test_profundidad',
        'motilidad_ocular',
        'observaciones',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
