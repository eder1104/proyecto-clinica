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
        $registros = BitacoraAuditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        return view('citas.bitacora', compact('registros'));
    }
}
