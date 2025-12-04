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
        $datosAntes = [];
        $metodo = strtolower($request->method());
        $metodosAuditables = ['post', 'put', 'patch', 'delete'];

        if (in_array($metodo, ['put', 'patch', 'delete'])) {
            foreach ($request->route()->parameters() as $key => $parametro) {
                if ($parametro instanceof Model) {
                    $datosAntes[$key] = $parametro->toArray();
                }
            }
        }

        $response = $next($request);

        if (in_array($metodo, $metodosAuditables) && $response->getStatusCode() < 400) {
            $this->registrarAuditoria($request, $datosAntes, strtoupper($metodo));
        }

        return $response;
    }

    protected function registrarAuditoria(Request $request, $datosAntes, $metodoOriginal)
    {
        $user = Auth::user();
        $modulo = explode('/', $request->path())[0] ?? 'general';
        
        $acciones = [
            'POST' => 'CREAR',
            'PUT' => 'ACTUALIZAR',
            'PATCH' => 'ACTUALIZAR',
            'DELETE' => 'ELIMINAR'
        ];
        $accion = $acciones[$metodoOriginal] ?? $metodoOriginal;

        $registroId = null;
        $modeloEncontrado = null;
        $cambios = [];

        foreach ($request->route()->parameters() as $key => $parametro) {
            if ($parametro instanceof Model) {
                $modeloEncontrado = $parametro;
                $registroId = $parametro->getKey();
                break;
            } elseif (is_numeric($parametro)) {
                $registroId = $parametro;
            }
        }

        if ($metodoOriginal === 'POST' && !$modeloEncontrado) {
             $cambios = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        } elseif ($modeloEncontrado) {
            $datosDespues = ($metodoOriginal === 'DELETE') ? [] : $modeloEncontrado->fresh()->toArray();
            
            $antes = $datosAntes[$modeloEncontrado->getTable()] ?? ($datosAntes[array_key_first($datosAntes)] ?? []);
            
            $cambios = $this->calcularDiferencias($antes, $datosDespues);
        }

        if (empty($cambios)) {
            $cambios = "Se ejecutó la acción pero no se detectaron cambios en los datos monitoreados.";
        }

        $bitacora = new BitacoraAuditoria();
        $bitacora->usuario_id = $user ? $user->id : null;
        $bitacora->modulo = $modulo;
        $bitacora->accion = $accion;
        $bitacora->registro_afectado = $registroId;
        $bitacora->observacion = json_encode($cambios, JSON_UNESCAPED_UNICODE);
        $bitacora->fecha_hora = Carbon::now();
        $bitacora->save();

        if (is_array($cambios) && !empty($cambios)) {
            $this->guardarHistorial($bitacora->id, $registroId, $cambios);
        }
    }

    protected function calcularDiferencias($antes, $despues)
    {
        $diferencias = [];
        $ignorar = ['updated_at', 'created_at', 'deleted_at', 'email_verified_at', 'remember_token'];

        if (empty($antes) && !empty($despues)) {
            return $despues; 
        }

        if (!empty($antes) && empty($despues)) {
            return ['registro_eliminado' => $antes];
        }

        foreach ($despues as $key => $valorNuevo) {
            $valorViejo = $antes[$key] ?? null;

            if (in_array($key, $ignorar)) continue;

            $v1 = is_bool($valorViejo) ? ($valorViejo ? '1' : '0') : (string)$valorViejo;
            $v2 = is_bool($valorNuevo) ? ($valorNuevo ? '1' : '0') : (string)$valorNuevo;

            if ($v1 !== $v2) {
                $diferencias[$key] = [
                    'antes' => $valorViejo,
                    'despues' => $valorNuevo
                ];
            }
        }

        return $diferencias;
    }

    protected function guardarHistorial($bitacoraId, $registroId, $cambios)
    {
        $historial = new HistorialCambio();
        $historial->bitacora_id = $bitacoraId;
        $historial->registro_afectado = $registroId;

        $dataAntes = [];
        $dataNuevos = [];

        foreach ($cambios as $campo => $valores) {
            if (is_array($valores) && isset($valores['antes'])) {
                $dataAntes[$campo] = $valores['antes'];
                $dataNuevos[$campo] = $valores['despues'];
            } else {
                $dataNuevos[$campo] = $valores;
            }
        }

        $historial->datos_anteriores = json_encode($dataAntes, JSON_UNESCAPED_UNICODE);
        $historial->datos_nuevos = json_encode($dataNuevos, JSON_UNESCAPED_UNICODE);
        $historial->fecha_cambio = Carbon::now();
        $historial->save();
    }
}