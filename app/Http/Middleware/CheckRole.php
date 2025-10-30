<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        $userRole = $user->role;
        $userStatus = $user->status;

        $hasRequiredRole = in_array($userRole, $roles);
        $isAccountActive = $userStatus === 'activo';

        if (!$isAccountActive) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('login')->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        $rolesCitas = ['doctor', 'admisiones', 'callcenter'];

        if (in_array($userRole, $rolesCitas)) {
            if (
                !$request->is('citas*') &&
                !$request->is('pacientes*') &&
                !$request->is('historias*')
            ) {
                return redirect()->route('citas.index')
                    ->with('info', 'Tu rol ha cambiado. Ahora estás viendo la vista de citas.');
            }
        }

        if (!$hasRequiredRole) {
            return redirect()->back()->with('error', 'Tu rol actual no permite acceder a esa sección.');
        }

        return $next($request);
    }
}
