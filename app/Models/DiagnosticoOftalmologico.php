<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticoOftalmologico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos_oftalmologicos';

    protected $fillable = ['nombre', 'codigo', 'estado'];

    public function historiasClinicas()
    {
        return $this->hasMany(HistoriaClinica::class, 'diagnostico_principal_id');
    }
}
