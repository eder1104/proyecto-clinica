<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctores;
use App\Models\CalendarioDisponibilidad;
use App\Models\DoctorParcialidad;
use App\Models\User;
use App\Http\Controllers\BitacoraAuditoriaController;
use Illuminate\Support\Facades\Auth;
use App\Models\BloqueoAgenda;
use Carbon\Carbon;

class CalendarioEspecialistaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->has('doctorId')) {
            abort(404, 'Doctor no especificado.');
        }

        $doctor = User::find($request->doctorId);

        if (!$doctor || $doctor->role !== 'doctor') {
            abort(404, 'Doctor no encontrado.');
        }

        $doctorProfile = Doctores::where('user_id', $doctor->id)->first();
        $doctor->numero_documento = $doctorProfile ? $doctorProfile->documento : 'N/A';

        return view('citas.CalendarioEspecialista', compact('doctor'));
    }

    public function obtenerCalendario($doctorId, $mes)
    {
        $user_id = $doctorId;
        $doctorProfile = Doctores::where('user_id', $user_id)->first();
        if (!$doctorProfile) {
            return response()->json([]);
        }
        $doctor_table_id = $doctorProfile->id;
        $fechaInicio = "$mes-01";
        $fechaFin = date('Y-m-t', strtotime($fechaInicio));
        $disponibilidadGuardada = CalendarioDisponibilidad::where('doctor_id', $doctor_table_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->pluck('estado', 'fecha')
            ->toArray();
        $parcialidadesGuardadas = DoctorParcialidad::where('doctor_id', $doctor_table_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->pluck('id', 'fecha')
            ->toArray();
        $diasMes = (int)date('t', strtotime($fechaInicio));
        $dias = [];
        for ($d = 1; $d <= $diasMes; $d++) {
            $fecha = sprintf('%s-%02d', $mes, $d);
            if (isset($parcialidadesGuardadas[$fecha])) {
                $dias[$fecha] = 'Parcial';
            } else {
                $dias[$fecha] = $disponibilidadGuardada[$fecha] ?? 'Disponible';
            }
        }
        return response()->json($dias);
    }

    public function actualizarEstado(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|integer|exists:users,id',
            'fecha' => 'required|date_format:Y-m-d',
            'estado' => 'required|string|in:Disponible,Bloqueado'
        ]);

        $user_id = $validated['doctor_id'];
        $doctorProfile = Doctores::where('user_id', $user_id)->first();

        if (!$doctorProfile) {
            return response()->json(['success' => false, 'message' => 'Perfil de doctor no encontrado para el usuario.'], 404);
        }

        $doctor_table_id = $doctorProfile->id;

        $disponibilidad = CalendarioDisponibilidad::firstOrNew(
            [
                'doctor_id' => $doctor_table_id,
                'fecha' => $validated['fecha'],
            ]
        );

        $estadoAnterior = $disponibilidad->exists ? $disponibilidad->estado : null;

        $disponibilidad->estado = $validated['estado'];
        $disponibilidad->save();

        if ($validated['estado'] !== 'Parcial') {
            DoctorParcialidad::where('doctor_id', $doctor_table_id)
                ->where('fecha', $validated['fecha'])
                ->delete();
        }

        if ($validated['estado'] === 'Bloqueado') {
            BloqueoAgenda::updateOrCreate(
                [
                    'fecha' => $validated['fecha'],
                    'creado_por' => $user_id,
                ],
                [
                    'hora_inicio' => '00:00:00',
                    'hora_fin' => '23:59:59',
                    'motivo' => 'Bloqueo completo del día por el doctor',
                ]
            );
        } else {
            BloqueoAgenda::where('fecha', $validated['fecha'])
                ->where('creado_por', $user_id)
                ->delete();
        }

        $bitacoraId = BitacoraAuditoriaController::registrar(
            Auth::id(),
            'agenda',
            'editar',
            $disponibilidad->getKey(),
            $validated
        );

        BitacoraAuditoriaController::registrarCambio(
            $bitacoraId,
            $disponibilidad->getKey(),
            ['estado' => $estadoAnterior],
            ['estado' => $validated['estado']]
        );

        return response()->json(['success' => true]);
    }
}
