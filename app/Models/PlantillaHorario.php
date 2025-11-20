<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaHorario extends Model
{
    protected $table = 'plantillas_horario';
    protected $fillable = ['hora_inicio', 'hora_fin', 'activo'];
    public $timestamps = true;
}
