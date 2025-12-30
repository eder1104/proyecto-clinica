<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BitacoraAuditoria;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        try {
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

            foreach ($request->route()->parameters() as $parametro) {
                if ($parametro instanceof Model) {
                    $registroId = $parametro->getKey();
                    break;
                } elseif (is_numeric($parametro)) {
                    $registroId = $parametro;
                }
            }

            $datos = $request->except(['_token', '_method', 'password', 'password_confirmation']);

            $bitacora = new BitacoraAuditoria();
            $bitacora->usuario_id = $user ? $user->id : null;
            $bitacora->modulo = $modulo;
            $bitacora->accion = $accion;
            $bitacora->registro_afectado = $registroId;
            $bitacora->observacion = json_encode($datos, JSON_UNESCAPED_UNICODE);
            $bitacora->fecha_hora = Carbon::now();
            $bitacora->save();

        } catch (\Exception $e) {
            Log::error('Error en Bitacora: ' . $e->getMessage());
        }
    }
}