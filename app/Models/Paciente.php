<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombres',
        'apellidos',
        'documento',
        'telefono',
        'direccion',
        'email',
        'fecha_nacimiento',
        'sexo',
        'created_by',
        'updated_by',
    ];

    protected $table = 'pacientes';


    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
