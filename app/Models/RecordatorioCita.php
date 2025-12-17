<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordatorioCita extends Model
{
    use HasFactory;

    protected $table = 'recordatorios_citas';

    protected $fillable = [
        'cita_id',
        'fecha_programada',
        'fecha_enviado',
        'estado',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}