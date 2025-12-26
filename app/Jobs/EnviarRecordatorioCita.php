<?php

namespace App\Jobs;

use App\Models\RecordatorioCita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecordatorioMailable;

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
        $this->recordatorio->load('cita.paciente');
        
        $paciente = $this->recordatorio->cita->paciente ?? null;

        if (!$this->recordatorio->cita || !$paciente) {
            return;
        }

        Mail::to($paciente->email)->send(new RecordatorioMailable($this->recordatorio));

        $this->recordatorio->update([
            'estado' => 'enviado',
            'fecha_enviado' => now(),
        ]);
    }
}