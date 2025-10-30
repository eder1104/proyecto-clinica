<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';
    protected $fillable = ['fecha', 'hora_inicio', 'hora_fin', 'motivo', 'creado_por'];
    public $timestamps = true;
}
