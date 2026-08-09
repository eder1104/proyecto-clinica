<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;

use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:admin,admisiones,callcenter,doctor',
        ]);

        try {
            $creatorId = Auth::id() ?? 0;

            $user = User::create([
                'nombres'    => $request->nombres,
                'apellidos'  => $request->apellidos,
                'email'      => $request->email,
                'password'   => $request->password,
                'role'       => $request->role,
                'status'     => 'activo',
                'created_by' => $creatorId,
                'updated_by' => $creatorId,
            ]);

            $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'web']);
            $user->assignRole($role);

            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Registro completado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'Error en el servidor: ' . $e->getMessage(),
            ]);
        }
    }
}