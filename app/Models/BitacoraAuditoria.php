<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraAuditoria extends Model
{
    use HasFactory;

    protected $table = 'bitacora_auditoria';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'modulo',
        'accion',
        'registro_afectado',
        'fecha_hora',
        'observacion',
    ];

    protected $casts = [
        'observacion' => 'array',
        'fecha_hora' => 'datetime',
    ];
}
