<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueoAgenda;
use App\Models\Doctores;
use App\Models\CalendarioDisponibilidad;
use App\Models\DoctorParcialidad;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BloqueoRequest;

class CitasBloqueadoController extends Controller
{
    public function store(BloqueoRequest $request)
    {
        $doctorProfile = Doctores::where('user_id', $request->doctor_id)->firstOrFail();

        $bloqueo = BloqueoAgenda::create([
            'doctor_id' => $doctorProfile->id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'motivo' => $request->motivo,
            'creado_por' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Bloqueo de agenda creado correctamente.');
    }

    public function destroy($id)
    {
        $bloqueo = BloqueoAgenda::findOrFail($id);
        
        $usuario = Auth::user();
        if ($usuario->id !== (int)$bloqueo->creado_por && !in_array($usuario->role, ['admin', 'admisiones'])) {
            abort(403);
        }

        $fecha = $bloqueo->fecha;
        $doctor_table_id = $bloqueo->doctor_id;

        $bloqueo->delete();

        $otrosBloqueos = BloqueoAgenda::where('doctor_id', $doctor_table_id)
            ->where('fecha', $fecha)
            ->exists();

        $tieneParcialidades = DoctorParcialidad::where('doctor_id', $doctor_table_id)
            ->where('fecha', $fecha)
            ->exists();

        if (!$otrosBloqueos && !$tieneParcialidades && $doctor_table_id) {
            CalendarioDisponibilidad::updateOrCreate(
                ['doctor_id' => $doctor_table_id, 'fecha' => $fecha],
                ['estado' => 'Disponible']
            );
        }

        return redirect()->back()->with('success', 'Bloqueo eliminado correctamente.');
    }
}