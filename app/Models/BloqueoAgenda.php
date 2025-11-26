<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Doctores;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';
    protected $fillable = ['doctor_id', 'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'creado_por'];
    public $timestamps = true;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctores::class, 'doctor_id');
    }
}