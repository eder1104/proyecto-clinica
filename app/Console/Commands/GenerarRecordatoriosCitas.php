<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecordatorioCita;
use App\Models\Cita;
use App\Jobs\EnviarRecordatorioCita;
use App\Models\BitacoraAuditoria;
use Carbon\Carbon;

class GenerarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:generar-recordatorios';
    protected $description = 'Genera recordatorios para citas de mañana';

    public function handle()
    {
        $fechaManana = Carbon::tomorrow()->toDateString();

        $this->info("📅 Buscando citas para mañana: " . $fechaManana);

        $citas = Cita::whereDate('fecha', $fechaManana)
            ->whereIn('estado', ['programada', 'Programada']) 
            ->get();

        $this->info("🔎 Citas encontradas: " . $citas->count());

        foreach ($citas as $cita) {
            $existe = RecordatorioCita::where('cita_id', $cita->id)->exists();

            if (!$existe) {
                $recordatorio = RecordatorioCita::create([
                    'cita_id' => $cita->id,
                    'fecha_programada' => now(),
                    'estado' => 'pendiente'
                ]);

                try {
                    EnviarRecordatorioCita::dispatch($recordatorio);
                    
                    $this->info("Enviado -> Cita ID: " . $cita->id);

                    BitacoraAuditoria::create([
                        'usuario_id' => null,
                        'accion' => 'SCHEDULER_ENCOLADO',
                        'modulo' => 'RECORDATORIOS',
                        'detalles' => json_encode(['cita_id' => $cita->id]),
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'System Scheduler',
                    ]);
                } catch (\Exception $e) {
                    $this->error("❌ Error: " . $e->getMessage());
                }
            } else {
                $this->warn(" Ya existe: Cita ID " . $cita->id);
            }
        }
    }
}