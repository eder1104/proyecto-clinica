<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentimientoPaciente extends Model
{
    use HasFactory;

    protected $table = 'consentimientos_paciente';

    protected $fillable = [
        'cita_id',
        'paciente_id',
        'nombre_paciente',
        'doctor_id',
        'nombre_doctor',
        'plantilla_id',
        'nombre_firmante',
        'firma',
        'fecha_firma',
        'activo',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaConsentimiento::class, 'plantilla_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
