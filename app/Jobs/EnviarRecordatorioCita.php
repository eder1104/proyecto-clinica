<?php

namespace App\Jobs;

use App\Models\RecordatorioCita;
use App\Models\BitacoraAuditoria;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarRecordatorioCita implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recordatorio;

    public function __construct(RecordatorioCita $recordatorio)
    {
        $this->recordatorio = $recordatorio;
    }

    public function handle(): void
    {
        $cita = $this->recordatorio->cita; 
        
        if (!$cita) {
            Log::error("Error en Job: El recordatorio {$this->recordatorio->id} no tiene cita asociada.");
            return;
        }

        $paciente = $cita->paciente;

        Log::info("📧 [SIMULACIÓN] Recordatorio enviado al paciente {$paciente->nombres} {$paciente->apellidos} para la cita del {$cita->fecha_programada} a las {$cita->hora_cita}");

        $this->recordatorio->update([
            'estado' => 'enviado',
            'fecha_enviado' => now(),
        ]);

        BitacoraAuditoria::create([
            'usuario_id' => null, 
            'accion' => 'RECORDATORIO_ENVIADO',
            'detalles' => "Se procesó el recordatorio ID: {$this->recordatorio->id} para la Cita ID: {$cita->id}",
            'ip_address' => '127.0.0.1', 
        ]);
    }
}