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
        if (!$request->has('doctorId')) abort(404, 'Doctor no especificado.');
        $doctor = User::find($request->doctorId);
        if (!$doctor || $doctor->role !== 'doctor') abort(404, 'Doctor no encontrado.');
        $doctorProfile = Doctores::where('user_id', $doctor->id)->first();
        $doctor->numero_documento = $doctorProfile ? $doctorProfile->documento : 'N/A';
        return view('citas.CalendarioEspecialista', compact('doctor'));
    }

    public function obtenerCalendario($doctorId, $mes)
    {
        $doctorProfile = Doctores::where('user_id', $doctorId)->first();
        if (!$doctorProfile) return response()->json([]);
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

        $bloqueosGuardados = BloqueoAgenda::where('doctor_id', $doctor_table_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->pluck('id', 'fecha')
            ->toArray();

        $diasMes = (int)date('t', strtotime($fechaInicio));
        $dias = [];

        for ($d = 1; $d <= $diasMes; $d++) {
            $fecha = sprintf('%s-%02d', $mes, $d);
            
            if (isset($parcialidadesGuardadas[$fecha])) {
                $dias[$fecha] = 'Parcial';
            } elseif (isset($bloqueosGuardados[$fecha])) {
                $dias[$fecha] = 'Bloqueado';
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
            'estado' => 'required|string|in:Disponible,Bloqueado,Parcial'
        ]);

        $doctorProfile = Doctores::where('user_id', $validated['doctor_id'])->first();
        if (!$doctorProfile) return response()->json(['success' => false], 404);
        
        $usuarioId = Auth::id();

        if ($validated['estado'] === 'Bloqueado') {
            return redirect()->route('citas.bloqueado', [
                'doctorId' => $validated['doctor_id'],
                'fecha' => $validated['fecha']
            ]);
        }

        if ($validated['estado'] === 'Parcial') {
            
            $observacion = "Cambio de disponibilidad manual a Parcial para la fecha {$validated['fecha']}.";
            
            CalendarioDisponibilidad::updateOrCreate(
                ['doctor_id' => $doctorProfile->id, 'fecha' => $validated['fecha']],
                ['estado' => 'Disponible']
            );

            $parcialidad = DoctorParcialidad::updateOrCreate(
                ['doctor_id' => $doctorProfile->id, 'fecha' => $validated['fecha']],
                []
            );
            
            BloqueoAgenda::where('fecha', $validated['fecha'])
                ->where('doctor_id', $doctorProfile->id)
                ->delete();

            $datosBitacora = array_merge($validated, ['observacion' => $observacion]);
            $bitacoraId = BitacoraAuditoriaController::registrar($usuarioId, 'agenda', 'creacion/edicion parcial', $parcialidad->getKey(), $datosBitacora);

            return response()->json(['success' => true]);
        }

        $disponibilidad = CalendarioDisponibilidad::firstOrNew([
            'doctor_id' => $doctorProfile->id,
            'fecha' => $validated['fecha']
        ]);

        $estadoAnterior = $disponibilidad->exists ? $disponibilidad->estado : 'No registrado';
        $observacion = "Cambio de estado de '{$estadoAnterior}' a '{$validated['estado']}' para la fecha {$validated['fecha']}.";

        $disponibilidad->estado = $validated['estado'];
        $disponibilidad->save();

        DoctorParcialidad::where('doctor_id', $doctorProfile->id)
            ->where('fecha', $validated['fecha'])
            ->delete();

        BloqueoAgenda::where('fecha', $validated['fecha'])
            ->where('doctor_id', $doctorProfile->id)
            ->delete();

        $datosBitacora = array_merge($validated, ['observacion' => $observacion]);
        $bitacoraId = BitacoraAuditoriaController::registrar(Auth::id(), 'agenda', 'editar estado', $disponibilidad->getKey(), $datosBitacora);
        
        BitacoraAuditoriaController::registrarCambio($bitacoraId, $disponibilidad->getKey(), ['estado' => $estadoAnterior], ['estado' => $validated['estado']]);

        return response()->json(['success' => true]);
    }

    public function vistaBloqueo($doctorId, $fecha)
    {
        $doctorProfile = Doctores::where('user_id', $doctorId)->firstOrFail();
        $doctorUser = $doctorProfile->user;

        $bloqueos_guardados = BloqueoAgenda::where('doctor_id', $doctorProfile->id)
            ->where('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        return view('citas.bloqueado', [
            'doctor' => $doctorUser,
            'doctorId' => $doctorId,
            'dia' => $fecha,
            'fecha' => $fecha,
            'bloqueos_guardados' => $bloqueos_guardados
        ]);
    }

    public function storeBloqueo(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => 'required|integer|exists:users,id',
            'fecha' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'motivo' => 'nullable|string'
        ]);

        $doctorProfile = Doctores::where('user_id', $data['doctor_id'])->firstOrFail();
        $usuarioId = Auth::id();
        $motivo = $data['motivo'] ?? 'No especificado';
        $observacion = "Bloqueo de agenda creado por {$doctorProfile->user->nombres} {$doctorProfile->user->apellidos} para la fecha {$data['fecha']} de {$data['hora_inicio']} a {$data['hora_fin']}. Motivo: {$motivo}.";

        CalendarioDisponibilidad::updateOrCreate(
            ['doctor_id' => $doctorProfile->id, 'fecha' => $data['fecha']],
            ['estado' => 'Bloqueado']
        );

        DoctorParcialidad::where('doctor_id', $doctorProfile->id)
            ->where('fecha', $data['fecha'])
            ->delete();

        $bloqueo = BloqueoAgenda::create([
            'doctor_id' => $doctorProfile->id,
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'creado_por' => $data['doctor_id'], 
            'motivo' => $motivo
        ]);

        $datosBitacora = array_merge($data, ['observacion' => $observacion]);
        $bitacoraId = BitacoraAuditoriaController::registrar($usuarioId, 'agenda', 'bloqueo creado', $bloqueo->getKey(), $datosBitacora);

        return back()->with('success', 'Bloqueo registrado');
    }
}