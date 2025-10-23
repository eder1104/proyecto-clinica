<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, Role $role): RedirectResponse
{
    $request->authenticate();

    $user = Auth::user();
    $roles = Role::all();

    if ($user->status !== 'activo') {
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Tu usuario está inactivo, comunícate con el administrador.',
        ]);
    }

   /*  if ($roles == $user->'$admin'){
        return redirect(route('administracion.index'))
    } else {
        log("no existe el rol")
    }; */
    $request->session()->regenerate();

    return redirect()->intended(route('pacientes.index', absolute: false));
}





    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
