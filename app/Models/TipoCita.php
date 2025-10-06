<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCita extends Model
{
    use HasFactory;

    protected $table = 'tipos_citas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'tipo_cita_id');
    }
}
