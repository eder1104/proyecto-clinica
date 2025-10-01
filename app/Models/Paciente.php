<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'nombres',
        'apellidos',
        'documento',
        'telefono',
        'estado',
        'profesion',
        'ciudad',
        'direccion',
        'email',
        'fecha_nacimiento',
        'sexo',
        'created_by',
        'updated_by',
    ];

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    } 
}
