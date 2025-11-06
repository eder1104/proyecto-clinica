<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BitacoraAuditoria extends Model
{
    use HasFactory;

    protected $table = 'bitacora_auditoria';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'modulo',
        'accion',
        'registro_afectado',
        'fecha_hora',
        'observacion',
    ];

    protected $casts = [
        'observacion' => 'array',
        'fecha_hora' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getModuloDescriptivoAttribute(): string
    {
        return match (Str::lower($this->modulo)) {
            'agenda' => 'Agenda Oftalmológica',
            'calendario-especialista' => 'Agenda Oftalmológica',
            'parcialidad' => 'Agenda (Horas Parciales)',
            'parcialidades' => 'Agenda (Horas Parciales)',
            'consentimiento' => 'Consentimientos',
            'historia_clinica' => 'Historia Clínica',
            'pacientes' => 'Pacientes',
            'usuarios' => 'Gestión de Usuarios',
            default => ucwords(str_replace('_', ' ', $this->modulo)),
        };
    }

    public function getObservacionDescriptivaAttribute(): string
    {
        $data = $this->observacion;
        if (!is_array($data) || empty($data)) {
            return 'Detalles no disponibles';
        }

        try {
            $descripcion = [];
            switch (Str::lower($this->modulo)) {
                case 'agenda':
                case 'calendario-especialista':
                    if (Str::lower($this->accion) === 'editar' || Str::lower($this->accion) === 'crear' || Str::lower($this->accion) === 'post') {
                        $descripcion[] = "Nuevo Estado: <strong>{$data['estado']}</strong>";
                        $descripcion[] = "Fecha: <strong>{$data['fecha']}</strong>";
                    }
                    break;

                case 'parcialidad':
                case 'parcialidades':
                    if (Str::lower($this->accion) === 'crear' || Str::lower($this->accion) === 'post') {
                        $descripcion[] = "Rango: <strong>{$data['hora_inicio']} - {$data['hora_fin']}</strong>";
                        $descripcion[] = "Fecha: <strong>{$data['fecha']}</strong>";
                    }
                    break;

                default:
                    foreach ($data as $key => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            if (str_contains($key, 'password')) continue;
                            $descripcion[] = "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . Str::limit($value, 50);
                        }
                    }
                    break;
            }

            if (empty($descripcion)) {
                return 'Detalles no disponibles';
            }

            return implode('<br>', $descripcion);
        } catch (\Exception $e) {
            return 'Datos no procesables';
        }
    }

    public function getAccionDescriptivaAttribute(): string
    {
        return match (Str::lower($this->accion)) {
            'crear' => 'Creó',
            'post' => 'Creó (POST)',
            'editar' => 'Editó',
            'put' => 'Editó (PUT)',
            'patch' => 'Editó (PATCH)',
            'eliminar' => 'Eliminó',
            'delete' => 'Eliminó (DELETE)',
            default => ucfirst($this->accion),
        };
    }
}
