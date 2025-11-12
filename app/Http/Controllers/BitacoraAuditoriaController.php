<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAuditoria;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BitacoraAuditoriaController extends Controller
{
    public static function registrar($usuarioId, $modulo, $accion, $registroId = null, $observacion = null)
    {
        $bitacora = new BitacoraAuditoria();
        $bitacora->usuario_id = $usuarioId;
        $bitacora->modulo = $modulo;
        $bitacora->accion = $accion;
        $bitacora->registro_afectado = $registroId;
        $bitacora->observacion = $observacion;
        $bitacora->fecha_hora = Carbon::now();
        $bitacora->save();

        return $bitacora->id;
    }

    public static function registrarCambio($bitacoraId, $registroId, $datosAnteriores = null, $datosNuevos = null)
    {
        $cambio = new HistorialCambio();
        $cambio->bitacora_id = $bitacoraId;
        $cambio->registro_afectado = $registroId;
        $cambio->datos_anteriores = $datosAnteriores ? json_encode($datosAnteriores) : null;
        $cambio->datos_nuevos = $datosNuevos ? json_encode($datosNuevos) : null;
        $cambio->save();

        return $cambio->id;
    }

    public function index()
    {
        $bitacoras = BitacoraAuditoria::with(['usuario', 'historialCambios'])
            ->orderByDesc('fecha_hora')
            ->paginate(20);

        return view('citas.bitacora', compact('bitacoras'));
    }
}
