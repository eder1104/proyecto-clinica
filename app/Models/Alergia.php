<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    use HasFactory;

    protected $table = 'alergias';

    protected $fillable = ['nombre', 'tipo', 'estado'];

    public function pacientes()
    {
        return $this->belongsToMany(
            Paciente::class,
            'alergia_paciente',
            'alergia_id',
            'paciente_id'
        )
        ->withPivot('cita_id')
        ->withTimestamps();
    }
}
