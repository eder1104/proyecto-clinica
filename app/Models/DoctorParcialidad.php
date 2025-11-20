<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorParcialidad extends Model
{
    use HasFactory;

    protected $table = 'plantillas_horario';

    protected $fillable = [
        'doctor_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctores::class);
    }
}
