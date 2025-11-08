<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedimientoOftalmologico extends Model
{
    use HasFactory;

    protected $table = 'procedimientos_oftalmologicos';

    protected $fillable = ['nombre', 'codigo', 'estado'];

    public function historiasClinicas()
    {
        return $this->belongsToMany(
            HistoriaClinica::class,
            'historia_clinica_procedimiento',
            'procedimiento_id',
            'historia_clinica_id'
        )->withTimestamps()->withPivot('notas');
    }
}
