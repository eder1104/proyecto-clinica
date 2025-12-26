<?php

namespace App\Observers;

use App\Models\Cita;
use App\Models\RecordatorioCita;
use App\Models\BitacoraAuditoria;
use App\Models\User;
use App\Jobs\NotificarDoctorCita;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CitaObserver
{
    public function created(Cita $cita): void
    {
        if ($cita->estado === 'programada') {
            
            NotificarDoctorCita::dispatch($cita);

            $fechaLimpia = $cita->fecha instanceof Carbon 
                ? $cita->fecha->format('Y-m-d') 
                : substr((string)$cita->fecha, 0, 10);

            $fechaCita = Carbon::parse($fechaLimpia . ' ' . $cita->hora_inicio);
            $fechaProgramada = $fechaCita->copy()->subHours(24);

            $recordatorio = RecordatorioCita::create([
                'cita_id' => $cita->id,
                'estado' => 'pendiente',
                'fecha_programada' => $fechaProgramada,
            ]);

            $usuarioId = Auth::id() ?? User::value('id') ?? 1;

            BitacoraAuditoria::create([
                'usuario_id' => $usuarioId,
                'accion' => 'GENERACION_RECORDATORIO',
                'modulo' => 'RECORDATORIOS',
                'detalles' => json_encode([
                    'cita_id' => $cita->id,
                    'recordatorio_id' => $recordatorio->id,
                    'aviso_doctor' => 'Enviado a cola',
                    'fecha_programada' => $fechaProgramada->toDateTimeString()
                ]),
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->header('User-Agent') ?? 'CLI',
            ]);
        }
    }
}