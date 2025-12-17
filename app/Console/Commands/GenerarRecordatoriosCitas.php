<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use App\Models\RecordatorioCita;
use App\Jobs\EnviarRecordatorioCita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:generar-recordatorios';
    protected $description = 'Busca citas de mañana y genera sus recordatorios en la cola';

    public function handle()
    {
        $this->info('🔍 Buscando citas para mañana...');

        $manana = Carbon::tomorrow()->format('Y-m-d');

        $citas = Cita::where('fecha', $manana) 
                     ->where('estado', '!=', 'cancelada')
                     ->get();

        if ($citas->isEmpty()) {
            $this->info('No hay citas programadas para el ' . $manana);
            return;
        }

        $contador = 0;

        foreach ($citas as $cita) {
            $yaExiste = RecordatorioCita::where('cita_id', $cita->id)->exists();

            if (!$yaExiste) {
                $recordatorio = RecordatorioCita::create([
                    'cita_id' => $cita->id,
                    'fecha_programada' => now(),
                    'estado' => 'pendiente'
                ]);

                EnviarRecordatorioCita::dispatch($recordatorio);
                $contador++;
            }
        }

        $this->info("✅ Proceso terminado. Se generaron {$contador} recordatorios.");
        Log::info("SCHEDULER: Se generaron {$contador} recordatorios para el día {$manana}");
    }
}