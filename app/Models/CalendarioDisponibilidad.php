<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioDisponibilidad extends Model
{
    use HasFactory;

    protected $table = 'calendario_disponibilidad';

    protected $fillable = [
        'doctor_id',
        'fecha',
        'estado',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctores::class, 'doctor_id');
    }
}
