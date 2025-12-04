<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BitacoraAuditoria;
use App\Models\HistorialCambio;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bitacora
{
    public function handle(Request $request, Closure $next)
    {
        $metodo = strtolower($request->method());
        $metodosAuditables = ['post', 'put', 'patch', 'delete'];

        $response = $next($request);

        if (in_array($metodo, $metodosAuditables) && $response->getStatusCode() < 400) {
            $this->registrarAuditoria($request, strtoupper($metodo));
        }

        return $response;
    }

    protected function registrarAuditoria(Request $request, $metodoOriginal)
    {
        $user = Auth::user();
        $modulo = explode('/', $request->path())[0] ?? 'general';
        
        $acciones = [
            'POST' => 'Crear',
            'PUT' => 'Actualizar',
            'PATCH' => 'Actualizar',
            'DELETE' => 'Eliminar'
        ];
        $accion = $acciones[$metodoOriginal] ?? ucfirst(strtolower($metodoOriginal));

        $registroId = null;
        $modeloEncontrado = null;
        $datosNuevos = [];

        foreach ($request->route()->parameters() as $parametro) {
            if ($parametro instanceof Model) {
                $modeloEncontrado = $parametro;
                $registroId = $parametro->getKey();
                break;
            } elseif (is_numeric($parametro)) {
                $registroId = $parametro;
            }
        }

        if ($modeloEncontrado) {
            if ($metodoOriginal === 'DELETE') {
                $datosNuevos = $modeloEncontrado->toArray();
            } else {
                $modeloRefrescado = $modeloEncontrado->fresh();
                $datosNuevos = $modeloRefrescado ? $modeloRefrescado->toArray() : $request->all();
            }
        } else {
            $datosNuevos = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        }

        $bitacora = new BitacoraAuditoria();
        $bitacora->usuario_id = $user ? $user->id : null;
        $bitacora->modulo = $modulo;
        $bitacora->accion = $accion;
        $bitacora->registro_afectado = $registroId;
        $bitacora->observacion = json_encode($datosNuevos, JSON_UNESCAPED_UNICODE);
        $bitacora->fecha_hora = Carbon::now();
        $bitacora->save();

        if (!empty($datosNuevos)) {
            $this->guardarHistorial($bitacora->id, $registroId, $datosNuevos, $accion);
        }
    }

    protected function guardarHistorial($bitacoraId, $registroId, $datos, $accion)
    {
        $historial = new HistorialCambio();
        $historial->bitacora_id = $bitacoraId;
        $historial->registro_afectado = $registroId;

        if ($accion === 'Eliminar') {
            $historial->datos_anteriores = json_encode($datos, JSON_UNESCAPED_UNICODE);
            $historial->datos_nuevos = null;
        } else {
            $historial->datos_anteriores = null; 
            $historial->datos_nuevos = json_encode($datos, JSON_UNESCAPED_UNICODE);
        }

        $historial->fecha_cambio = Carbon::now();
        $historial->save();
    }
}