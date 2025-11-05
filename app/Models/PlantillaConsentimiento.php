<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaConsentimiento extends Model
{
    use HasFactory;

    protected $table = 'plantillas_consentimiento';

    protected $fillable = [
        'version',
        'titulo',
        'texto_consentimiento',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function consentimientosPaciente()
    {
        return $this->hasMany(ConsentimientoPaciente::class, 'plantilla_id');
    }
}
