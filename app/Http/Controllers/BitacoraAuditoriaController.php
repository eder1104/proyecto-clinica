<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;

class BitacoraAuditoriaController extends Controller
{
    public static function registrar($usuarioId, $modulo, $accion, $registroId = null, $observacion = null)
    {
        BitacoraAuditoria::create([
            'usuario_id' => $usuarioId,
            'modulo' => $modulo,
            'accion' => $accion,
            'registro_afectado' => $registroId,
            'observacion' => $observacion,
        ]);
    }

    public function index()
    {
        $registros = BitacoraAuditoria::orderBy('fecha_hora', 'desc')->get();
        return view('citas.bitacora', compact('registros'));
    }
}
