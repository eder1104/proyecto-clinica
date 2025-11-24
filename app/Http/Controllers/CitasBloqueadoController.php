<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueoAgenda;
use App\Models\Doctores;
use App\Models\CalendarioDisponibilidad;
use App\Models\DoctorParcialidad;
use Illuminate\Support\Facades\Auth;

class CitasBloqueadoController extends Controller
{
    public function destroy($id)
    {
        $bloqueo = BloqueoAgenda::findOrFail($id);

        $usuario = Auth::user();
        if ($usuario->id !== (int)$bloqueo->creado_por && !in_array($usuario->role, ['admin', 'admisiones'])) {
            abort(403);
        }

        $fecha = $bloqueo->fecha;
        $doctorUserId = $bloqueo->creado_por;

        $doctorProfile = Doctores::where('user_id', $doctorUserId)->first();
        $doctor_table_id = $doctorProfile ? $doctorProfile->id : null;

        $bloqueo->delete();

        $otrosBloqueos = BloqueoAgenda::where('creado_por', $doctorUserId)
            ->where('fecha', $fecha)
            ->exists();

        $tieneParcialidades = $doctor_table_id
            ? DoctorParcialidad::where('doctor_id', $doctor_table_id)
                ->where('fecha', $fecha)
                ->exists()
            : false;

        if (!$otrosBloqueos && !$tieneParcialidades && $doctor_table_id) {
            CalendarioDisponibilidad::updateOrCreate(
                ['doctor_id' => $doctor_table_id, 'fecha' => $fecha],
                ['estado' => 'Disponible']
            );
        }

        return redirect()->route('citas.bloqueado', [
            'doctorId' => $doctorUserId,
            'fecha' => $fecha
        ])->with('success', 'Bloqueo eliminado');
    }
}
