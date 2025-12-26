<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecordatorioCita;
use App\Jobs\EnviarRecordatorioCita;
use App\Models\BitacoraAuditoria;
use Carbon\Carbon;

class GenerarRecordatoriosCitas extends Command
{
    protected $signature = 'recordatorios:procesar';

    protected $description = 'Procesa recordatorios vencidos';

    public function handle()
    {
        $recordatorios = RecordatorioCita::where('estado', 'pendiente')
            ->where('fecha_programada', '<=', Carbon::now())
            ->get();

        foreach ($recordatorios as $recordatorio) {
            try {
                EnviarRecordatorioCita::dispatch($recordatorio);

                BitacoraAuditoria::create([
                    'usuario_id' => null,
                    'accion' => 'SCHEDULER_ENCOLADO',
                    'modulo' => 'RECORDATORIOS',
                    'detalles' => json_encode([
                        'recordatorio_id' => $recordatorio->id,
                        'cita_id' => $recordatorio->cita_id,
                        'mensaje' => 'Enviado a cola (Redis)'
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'System Scheduler',
                ]);

            } catch (\Exception $e) {
            }
        }
    }
}