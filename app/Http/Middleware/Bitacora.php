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
        $datosOriginales = [];
        $metodo = strtolower($request->method());

        if (in_array($metodo, ['put', 'patch', 'delete'])) {
            foreach ($request->route()->parameters() as $key => $parametro) {
                if ($parametro instanceof Model) {
                    $datosOriginales[$key] = $parametro->toArray();
                }
            }
        }

        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            
            $this->procesarBitacora($request, $datosOriginales, strtoupper($metodo));
        }

        return $response;
    }

    protected function procesarBitacora(Request $request, $datosOriginales, $accion)
    {
        $user = Auth::user();
        $modulo = explode('/', $request->path())[0] ?? 'general';
        
        $registroId = null;
        $modeloPrincipal = null;

        foreach ($request->route()->parameters() as $key => $parametro) {
            if ($parametro instanceof Model) {
                $modeloPrincipal = $parametro;
                $registroId = $parametro->getKey();
                break; 
            } elseif (is_numeric($parametro)) {
                $registroId = $parametro;
            }
        }

        $bitacora = new BitacoraAuditoria();
        $bitacora->usuario_id = $user ? $user->id : null;
        $bitacora->modulo = $modulo;
        $bitacora->accion = $accion;
        $bitacora->registro_afectado = $registroId;
        
        $bitacora->observacion = [
            'input' => $request->except(['_token', 'password', 'password_confirmation']),
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ];
        
        $bitacora->fecha_hora = Carbon::now();
        $bitacora->save();

        if (!empty($datosOriginales)) {
            foreach ($datosOriginales as $key => $antes) {
                $modelo = $request->route($key);

                if ($modelo instanceof Model) {
                    $despues = $accion === 'DELETE' ? [] : $modelo->fresh()->toArray();
                    
                    $this->registrarCambios($bitacora->id, $modelo->getKey(), $antes, $despues);
                }
            }
        }
    }

    protected function registrarCambios($bitacoraId, $registroId, $datosAnteriores, $datosNuevos)
    {
        $cambiosAntes = [];
        $cambiosDespues = [];

        $ignorar = ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];

        if (empty($datosNuevos)) {
            $cambiosAntes = $datosAnteriores;
        } else {
            foreach ($datosNuevos as $key => $nuevoValor) {
                $valorAnterior = $datosAnteriores[$key] ?? null;

                $v1 = is_scalar($valorAnterior) ? (string)$valorAnterior : json_encode($valorAnterior);
                $v2 = is_scalar($nuevoValor) ? (string)$nuevoValor : json_encode($nuevoValor);

                if ($v1 !== $v2 && !in_array($key, $ignorar)) {
                    $cambiosAntes[$key] = $valorAnterior;
                    $cambiosDespues[$key] = $nuevoValor;
                }
            }
        }

        if (!empty($cambiosAntes) || !empty($cambiosDespues)) {
            $historial = new HistorialCambio();
            $historial->bitacora_id = $bitacoraId;
            $historial->registro_afectado = $registroId;
            $historial->datos_anteriores = json_encode($cambiosAntes);
            $historial->datos_nuevos = json_encode($cambiosDespues);
            $historial->fecha_cambio = Carbon::now();
            $historial->save();
        }
    }
}