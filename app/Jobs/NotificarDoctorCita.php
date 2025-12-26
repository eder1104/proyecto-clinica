<?php

namespace App\Jobs;

use App\Models\Cita;
use App\Mail\NotificacionDoctorMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificarDoctorCita implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cita;

    public function __construct(Cita $cita)
    {
        $this->cita = $cita;
    }

    public function handle(): void
    {
        $this->cita->load('doctor');

        if (!$this->cita->doctor || !$this->cita->doctor->email) {
            Log::warning("No se pudo notificar al doctor. Cita ID: {$this->cita->id} - Falta email o doctor.");
            return;
        }

        Mail::to($this->cita->doctor->email)->send(new NotificacionDoctorMailable($this->cita));
    }
}