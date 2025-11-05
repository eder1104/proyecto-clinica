<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retina extends Model
{
    use HasFactory;

    protected $fillable = [
        'cita_id',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'imagen_ojo_izquierdo',
        'imagen_ojo_derecho',
        'imagen_editada_izq',
        'imagen_editada_der'
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
