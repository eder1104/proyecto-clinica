<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AgendaController extends Controller
{
    public function disponibilidad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medico_id' => 'required|exists:users,id',
            'sede_id'   => 'required', 
            'fecha'     => 'required|date_format:Y-m-d|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Parámetros inválidos',
                'errores' => $validator->errors()
            ], 400);
        }

        $medicoId = (int) $request->medico_id;
        $fecha = $request->fecha;

        $agendaService = new AgendaService(20);
        $slots = $agendaService->generarSlotsDelDia($medicoId, $fecha);

        $disponibles = [];

        foreach ($slots as $slot) {
            if ($slot['estado'] === 'libre') {
                $disponibles[] = [
                    'hora_inicio' => $slot['start'],
                    'hora_fin'    => $slot['end']
                ];
            }
        }

        return response()->json([
            'medico_id' => $medicoId,
            'sede_id' => $request->sede_id,
            'fecha' => $fecha,
            'intervalos_disponibles' => $disponibles
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id'   => 'required|exists:users,id',
            'sede_id'     => 'required',
            'fecha'       => 'required|date_format:Y-m-d|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i:s',
            'hora_fin'    => 'required|date_format:H:i:s|after:hora_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error de validación',
                'errores' => $validator->errors()
            ], 400);
        }

        $solapamiento = Cita::where('doctor_id', $request->medico_id)
            ->whereDate('fecha', $request->fecha)
            ->whereIn('estado', ['programada', 'confirmada', 'modificada'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                    ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                    });
            })
            ->exists();

        if ($solapamiento) {
            return response()->json([
                'mensaje' => 'El horario seleccionado no está disponible (solapamiento detectado).'
            ], 409);
        }

        try {
            $cita = Cita::create([
                'paciente_id' => $request->paciente_id,
                'doctor_id'   => $request->medico_id,
                'fecha'       => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin'    => $request->hora_fin,
                'estado'      => 'programada',
                'created_by'  => 'API REST',
                'tipo_cita_id' => 1
            ]);

            return response()->json([
                'mensaje' => 'Cita agendada exitosamente',
                'cita' => $cita
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error interno', 'detalle' => $e->getMessage()], 500);
        }
    }

    public function cancelar(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'motivo_cancelacion' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()], 400);
        }

        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['mensaje' => 'Cita no encontrada'], 404);
        }

        if ($cita->estado == 'cancelada') {
            return response()->json(['mensaje' => 'La cita ya estaba cancelada'], 400);
        }

        $cita->update([
            'estado' => 'cancelada',
            'cancel_reason' => $request->motivo_cancelacion,
            'cancelled_by' => 'API REST'
        ]);

        return response()->json([
            'mensaje' => 'Cita cancelada correctamente',
            'cita' => $cita
        ]);
    }

    public function index(Request $request)
    {
        $query = Cita::query();

        if ($request->has('medico_id')) {
            $query->where('doctor_id', $request->medico_id);
        }

        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $citas = $query->orderBy('fecha', 'desc')->paginate(15);

        return response()->json($citas);
    }
}
