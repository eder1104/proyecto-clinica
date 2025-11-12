<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';
    protected $fillable = ['fecha', 'hora_inicio', 'hora_fin', 'motivo', 'creado_por'];
    public $timestamps = true;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
