<?php

namespace App\Observers;

use App\Models\RecordatorioCita;
use App\Models\BitacoraAuditoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RecordatorioObserver
{
    public function updated(RecordatorioCita $recordatorio): void
    {
        if ($recordatorio->wasChanged('estado') && $recordatorio->estado === 'enviado') {
            $usuarioId = Auth::id() ?? User::value('id') ?? 1;

            BitacoraAuditoria::create([
                'usuario_id' => $usuarioId,
                'accion' => 'EJECUCION_JOB_RECORDATORIO',
                'modulo' => 'RECORDATORIOS',
                'detalles' => json_encode([
                    'recordatorio_id' => $recordatorio->id,
                    'cita_id' => $recordatorio->cita_id,
                    'resultado' => 'Enviado correctamente'
                ]),
            ]);
        }
    }
}