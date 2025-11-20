<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Alergia;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'nombres',
        'apellidos',
        'documento',
        'telefono',
        'tipo_documento',
        'direccion',
        'email',
        'fecha_nacimiento',
        'sexo',
        'created_by',
        'updated_by',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function alergias()
    {
        return $this->belongsToMany(
            Alergia::class,
            'alergia_paciente',
            'paciente_id',
            'alergia_id'
        )
            ->withPivot('cita_id')
            ->withTimestamps();
    }
}
