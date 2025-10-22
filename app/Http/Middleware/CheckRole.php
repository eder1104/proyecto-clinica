<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role;

        if (!in_array($userRole, $roles)) {
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No tienes permisos para acceder a esta sección.'
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
