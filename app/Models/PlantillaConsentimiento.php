<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaConsentimiento extends Model
{
    use HasFactory;

    protected $table = 'plantillas_consentimiento';

    protected $fillable = [
        'titulo',
        'tipo',
        'texto',
        'version',
        'activo',
    ];
}
