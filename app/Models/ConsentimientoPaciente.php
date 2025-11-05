<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentimientoPaciente extends Model
{
    use HasFactory;

    protected $table = 'consentimientos_paciente';

    protected $fillable = [
        'paciente_id',
        'plantilla_id',
        'nombre_firmante',
        'fecha_firma',
        'imagen_firma',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(ConsentimientoPaciente::class, 'plantilla_id');
    }
}
