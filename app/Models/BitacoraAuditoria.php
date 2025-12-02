<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            'bloqueo-especialista' => 'Bloqueo de Agenda',
            'consentimiento' => 'Consentimientos',
            'consentimientos' => 'Consentimientos',
            'historia_clinica' => 'Historia Clínica',
            'historias' => 'Historia Clínica',
            'optometria' => 'Optometría',
            'pacientes' => 'Pacientes',
            'usuarios' => 'Usuarios',
            'citas' => 'Citas Médicas',
            'login' => 'Autenticación',
            'register' => 'Registro de Usuarios',
            default => ucwords(str_replace(['_', '-'], ' ', $this->modulo)),
        };
    }

    public function getObservacionDescriptivaAttribute(): string
    {
        $data = $this->observacion;

        if (!is_array($data) || empty($data)) {
            return is_string($data) ? $data : 'Detalles no disponibles';
        }

        if (isset($data['input'])) {
            $input = $data['input'];
            if (empty($input)) {
                if (isset($data['parametros_url']) && !empty($data['parametros_url'])) {
                    return "Registro afectado ID: " . implode(', ', $data['parametros_url']);
                }
                if (isset($data['url'])) {
                    $urlLimpia = str_replace(url('/'), '', $data['url']);
                    return "Consultó la ruta: " . ($urlLimpia ?: '/');
                }
                return 'Sin datos adicionales';
            }
            $data = $input;
        }

        try {
            $descripcion = [];

            switch (Str::lower($this->modulo)) {
                case 'agenda':
                case 'calendario-especialista':
                case 'bloqueo-especialista':
                    if (isset($data['estado'])) $descripcion[] = "Estado: <strong>{$data['estado']}</strong>";
                    if (isset($data['fecha'])) $descripcion[] = "Fecha: <strong>{$data['fecha']}</strong>";
                    if (isset($data['hora_inicio'])) $descripcion[] = "Horario: <strong>{$data['hora_inicio']} - {$data['hora_fin']}</strong>";
                    if (isset($data['motivo'])) $descripcion[] = "Motivo: {$data['motivo']}";
                    break;

                case 'parcialidad':
                case 'parcialidades':
                    if (isset($data['hora_inicio'])) $descripcion[] = "Rango: <strong>{$data['hora_inicio']} - {$data['hora_fin']}</strong>";
                    if (isset($data['fecha'])) $descripcion[] = "Fecha: <strong>{$data['fecha']}</strong>";
                    break;
                
                case 'citas':
                    if (isset($data['fecha'])) $descripcion[] = "Fecha Cita: <strong>{$data['fecha']}</strong>";
                    if (isset($data['paciente_id'])) $descripcion[] = "ID Paciente: {$data['paciente_id']}";
                    if (isset($data['estado'])) $descripcion[] = "Estado: <strong>{$data['estado']}</strong>";
                    if (isset($data['tipo_cita_id'])) $descripcion[] = "Tipo: " . ($data['tipo_cita_id'] == 1 ? 'Optometría' : 'Examen');
                    break;

                case 'usuarios':
                case 'register':
                    if (isset($data['email'])) $descripcion[] = "Email: {$data['email']}";
                    if (isset($data['role'])) $descripcion[] = "Rol: <strong>{$data['role']}</strong>";
                    if (isset($data['nombres'])) $descripcion[] = "Nombre: {$data['nombres']} {$data['apellidos']}";
                    break;
                
                case 'pacientes':
                    if (isset($data['documento'])) $descripcion[] = "Doc: {$data['documento']}";
                    if (isset($data['nombres'])) $descripcion[] = "Paciente: {$data['nombres']} {$data['apellidos']}";
                    if (isset($data['telefono'])) $descripcion[] = "Tel: {$data['telefono']}";
                    break;

                default:
                    foreach ($data as $key => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            if (Str::contains($key, ['password', 'token', '_method', 'q', '_token'])) continue;
                            
                            $label = ucwords(str_replace(['_', '-'], ' ', $key));
                            $valor = Str::limit($value, 50);
                            
                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
                                $valor = Carbon::parse($valor)->format('d/m/Y');
                            }

                            $descripcion[] = "<strong>{$label}:</strong> {$valor}";
                        }
                    }
                    break;
            }

            if (empty($descripcion)) {
                return 'Datos guardados: ' . Str::limit(json_encode($data), 100);
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
            'post' => 'Registró',
            'store' => 'Registró',
            'editar' => 'Editó',
            'put' => 'Actualizó',
            'patch' => 'Actualizó',
            'eliminar' => 'Eliminó',
            'delete' => 'Eliminó',
            'get' => 'Consultó',
            'index' => 'Listó',
            'show' => 'Visualizó',
            default => ucfirst($this->accion),
        };
    }

    public function historialCambios()
    {
        return $this->hasMany(HistorialCambio::class, 'bitacora_id');
    }
}