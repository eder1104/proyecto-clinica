<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueoAgenda;
use App\Models\Doctores;
use App\Models\CalendarioDisponibilidad;
use App\Models\DoctorParcialidad;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BitacoraAuditoriaController;
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

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'agenda',
            'crear bloqueo',
            $bloqueo->id,
            $bloqueo->toArray()
        );

        return redirect()->back()->with('success', 'Bloqueo de agenda creado correctamente.');
    }

    public function destroy(Request $request, $id)
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

        $datosEliminados = [
            'id' => $bloqueo->id,
            'fecha' => $bloqueo->fecha,
            'hora_inicio' => $bloqueo->hora_inicio,
            'hora_fin' => $bloqueo->hora_fin,
            'motivo' => $bloqueo->motivo ?? 'No especificado',
            'creado_por' => $bloqueo->creado_por,
            'doctor_id' => $bloqueo->doctor_id
        ];

        $observacion = "Eliminación de bloqueo de agenda para la fecha {$bloqueo->fecha} de {$bloqueo->hora_inicio} a {$bloqueo->hora_fin}. Motivo: " . ($bloqueo->motivo ?? 'No especificado');
        
        $datosBitacora = array_merge($datosEliminados, ['observacion' => $observacion]);

        $idEliminado = $bloqueo->id;
        $bloqueo->delete();

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'agenda',
            'eliminar bloqueo',
            $idEliminado,
            $datosBitacora
        );

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