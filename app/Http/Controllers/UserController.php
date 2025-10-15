<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'admisiones', 'callcenter'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:admin,admisiones,callcenter',
        ]);

        User::create([
            'nombres'    => $request->nombres,
            'apellidos'  => $request->apellidos,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'created_by' => Auth::check() ? Auth::user()->nombres . ' ' . Auth::user()->apellidos : 'Registro por sistema',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }



    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'role'      => 'required|in:admin,admisiones,callcenter',
            'password'  => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nombres'    => $request->nombres,
            'apellidos'  => $request->apellidos,
            'email'      => $request->email,
            'role'       => $request->role,
            'updated_by' => Auth::check() ? Auth::user()->nombres . ' ' . Auth::user()->apellidos : 'Registro por sistema',
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }


    public function destroy(User $user)
    {
        $user->status = 'inactivo';
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Usuario se inactivo correctamente.');
    }


    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'activo' ? 'inactivo' : 'activo';
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Actualizado correctamente.');
    }
    public function register(Request $request)
    {
        $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nombres'    => $request->nombres,
            'apellidos'  => $request->apellidos,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'paciente',
            'status'     => 'activo',
            'created_by' => Auth::check() ? Auth::user()->nombres . ' ' . Auth::user()->apellidos : 'Registro por sistema',
            'updated_by' => Auth::check() ? Auth::user()->nombres . ' ' . Auth::user()->apellidos : 'Registro por sistema',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registro completado correctamente.');
    }
}
