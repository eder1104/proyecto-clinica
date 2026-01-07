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
                !$request->is('historias*') &&
                !$request->is('calendario*') &&
                !$request->is('consentimientos*') &&
                !$request->is('catalogos*') &&
                !$request->is('legacy*')
            ) {
                return redirect()->route('citas.index')
                    ->with('error', 'Usted no está autorizado para ingresar a esta ruta');
            }
        }

        if (!$hasRequiredRole) {
            return redirect()->route('dashboard')
                ->with('error', 'Usted no está autorizado para ingresar a esta ruta');
        }

        return $next($request);
    }
}