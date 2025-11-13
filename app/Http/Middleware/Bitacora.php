<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\BitacoraAuditoriaController;
use Illuminate\Support\Facades\Auth;

class Bitacora
{
    public function handle(Request $request, Closure $next)
    {


        $response = $next($request);

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            $modulo = explode('/', $request->path())[0] ?? 'desconocido';

            BitacoraAuditoriaController::registrar(
                Auth::id(),
                $modulo,
                strtoupper($request->method()),
                null,
                ['data' => $request->all()]
            );
        }

        return $response;
    }
}
