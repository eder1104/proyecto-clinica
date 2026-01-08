<?php

namespace App\Jobs;

use App\Models\RecordatorioCita;
use App\Models\BitacoraAuditoria;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecordatorioMailable;
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
        $this->recordatorio->refresh()->load('cita.paciente');
        
        $cita = $this->recordatorio->cita;
        $paciente = $cita->paciente ?? null;
        
        if (!$paciente || !$paciente->email) {
            Log::warning("No se pudo enviar recordatorio. Cita ID: " . ($cita->id ?? 'N/A') . " - El paciente no tiene email.");
            return;
        }

        Mail::to($paciente->email)->send(new RecordatorioMailable($this->recordatorio));

        $this->recordatorio->update([
            'estado' => 'enviado',
            'fecha_enviado' => now(),
        ]);

        BitacoraAuditoria::create([
            'usuario_id' => null,
            'accion' => 'RECORDATORIO_ENVIADO',
            'modulo' => 'RECORDATORIOS',
            'detalles' => json_encode([
                'recordatorio_id' => $this->recordatorio->id,
                'cita_id' => $cita->id,
                'enviado_a_paciente' => $paciente->email
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Queue Worker',
        ]);
    }
}