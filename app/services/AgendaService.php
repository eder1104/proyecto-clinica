<?php

namespace App\Services;

use App\Models\PlantillaHorario;
use App\Models\BloqueoAgenda;
use App\Models\Cita;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AgendaService
{
    protected int $slotMinutes;

    public function __construct($slotMinutes = 30)
    {
        $this->slotMinutes = $slotMinutes;
    }

    public function generarSlotsDelDia(int $doctorId, string $fecha): array
    {
        $plantilla = PlantillaHorario::where('doctor_id', $doctorId)
            ->where('activo', true)
            ->first();

        if (!$plantilla) {
            return [];
        }

        $inicio = Carbon::parse($fecha . ' ' . $plantilla->hora_inicio);
        $fin = Carbon::parse($fecha . ' ' . $plantilla->hora_fin);

        $period = CarbonPeriod::create($inicio, $this->slotMinutes . ' minutes', $fin);
        $slots = [];

        foreach ($period as $slotStart) {
            $slotEnd = $slotStart->copy()->addMinutes($this->slotMinutes);

            if ($slotEnd->gt($fin)) {
                break;
            }

            $slots[] = [
                'start' => $slotStart->format('H:i:s'),
                'end' => $slotEnd->format('H:i:s'),
                'estado' => 'libre'
            ];
        }

        $bloqueos = BloqueoAgenda::where('doctor_id', $doctorId)
            ->whereDate('fecha', $fecha)
            ->get();

        foreach ($bloqueos as $b) {
            foreach ($slots as &$s) {
                if ($this->timeRangesOverlap($s['start'], $s['end'], $b->hora_inicio, $b->hora_fin)) {
                    $s['estado'] = 'bloqueado';
                }
            }
            unset($s);
        }

        $citas = Cita::where('doctor_id', $doctorId)
            ->whereDate('fecha', $fecha)
            ->get();

        foreach ($citas as $c) {
            foreach ($slots as &$s) {
                if ($s['estado'] !== 'bloqueado') {
                    if ($this->timeRangesOverlap($s['start'], $s['end'], $c->hora_inicio, $c->hora_fin)) {
                        $s['estado'] = 'ocupado';
                    }
                }
            }
            unset($s);
        }

        return $slots;
    }

    protected function timeRangesOverlap($startA, $endA, $startB, $endB): bool
    {
        $aS = Carbon::parse($startA);
        $aE = Carbon::parse($endA);
        $bS = Carbon::parse($startB);
        $bE = Carbon::parse($endB);

        return $aS->lt($bE) && $bS->lt($aE);
    }

    public function estadoDelDia(int $doctorId, string $fecha): string
    {
        $slots = $this->generarSlotsDelDia($doctorId, $fecha);

        if (empty($slots)) {
            return 'disponible';
        }

        $total = count($slots);
        $ocupados = collect($slots)->filter(
            fn($s) =>
            $s['estado'] === 'ocupado' || $s['estado'] === 'bloqueado'
        )->count();

        if ($ocupados === 0) return 'disponible';
        if ($ocupados >= $total) return 'bloqueado';
        return 'parcial';
    }

    public function resumenDelDia(int $doctorId, string $fecha): array
    {
        $slots = $this->generarSlotsDelDia($doctorId, $fecha);

        return [
            'fecha' => $fecha,
            'estado' => $this->estadoDelDia($doctorId, $fecha),
            'slots' => $slots,
            'total_slots' => count($slots),
            'ocupados' => collect($slots)->filter(fn($s) => $s['estado'] === 'ocupado')->count(),
            'bloqueados' => collect($slots)->filter(fn($s) => $s['estado'] === 'bloqueado')->count(),
            'libres' => collect($slots)->filter(fn($s) => $s['estado'] === 'libre')->count(),
        ];
    }

    public function obtenerDoctoresBloqueadosHoy()
    {
        $fechaHoy = Carbon::today()->toDateString();
        return BloqueoAgenda::with('doctor')
            ->whereDate('fecha', $fechaHoy)
            ->get()
            ->pluck('doctor.nombres')
            ->filter()
            ->unique()
            ->values();
    }
}