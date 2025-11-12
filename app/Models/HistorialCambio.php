<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';

    protected $fillable = [
        'bitacora_id',
        'registro_afectado',
        'datos_anteriores',
        'datos_nuevos',
        'fecha_cambio',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    public function bitacora()
    {
        return $this->belongsTo(BitacoraAuditoria::class, 'bitacora_id');
    }
}
