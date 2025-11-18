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
        if (!is_array($observacion)) {
            $observacion = $observacion ? ['detalle' => $observacion] : [];
        }

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
        if (!$datosAnteriores || !$datosNuevos) {
            return null;
        }

        $cambiosAntes = [];
        $cambiosDespues = [];

        foreach ($datosNuevos as $key => $nuevoValor) {

            $valorAnterior = $datosAnteriores[$key] ?? null;

            if ($valorAnterior != $nuevoValor) {
                $cambiosAntes[$key] = $valorAnterior;
                $cambiosDespues[$key] = $nuevoValor;
            }
        }

        if (empty($cambiosAntes) && empty($cambiosDespues)) {
            return null;
        }

        $cambio = new HistorialCambio();
        $cambio->bitacora_id = $bitacoraId;
        $cambio->registro_afectado = $registroId;
        $cambio->datos_anteriores = $cambiosAntes;
        $cambio->datos_nuevos = $cambiosDespues;
        $cambio->fecha_cambio = now();
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
