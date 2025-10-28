<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'admisiones', 'callcenter', 'doctor'])
            ->orderBy('nombres', 'asc')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function indexDoctors()
    {
        $users = User::where('role', 'doctor')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        User::create([
            'nombres'    => trim($request->nombres),
            'apellidos'  => trim($request->apellidos),
            'email'      => strtolower(trim($request->email)),
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'created_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente. ✅');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = [
            'nombres'    => trim($request->nombres),
            'apellidos'  => trim($request->apellidos),
            'email'      => strtolower(trim($request->email)),
            'updated_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index', $user->id)
            ->with('success', 'Usuario actualizado correctamente. ✅');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,admisiones,callcenter,doctor',
        ]);

        $user->update([
            'role' => $request->input('role'),
        ]);

        return redirect()->route('users.index')
            ->with('success', "El rol de {$user->nombres} fue actualizado a '{$user->role}' correctamente. ✅");
    }

    public function destroy(User $user)
    {
        $user->update([
            'cancelled_by' => Auth::id(),
        ]);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente. 🗑️');
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'activo' ? 'inactivo' : 'activo';
        $user->updated_by = Auth::check() ? Auth::id() : 0;
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Estado actualizado correctamente. 🔄');
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
            'email'      => strtolower($request->email),
            'password'   => Hash::make($request->password),
            'role'       => 'users',
            'status'     => 'activo',
            'created_by' => Auth::check() ? Auth::id() : 0,
            'updated_by' => Auth::check() ? Auth::id() : 0,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registro completado correctamente. 🥳');
    }

    public function Usuario_buscar(Request $request)
    {
        $query = trim($request->input('query'));
        if (empty($query)) {
            $users = User::paginate(15);
        } else {
            $users = User::where(function ($q) use ($query) {
                $q->where('nombres', 'LIKE', "%{$query}%")
                    ->orWhere('apellidos', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })->paginate(10)->withQueryString();
        }

        return view('users.index', compact('users'));
    }


    public function buscar(Request $request)
    {
        $tipo = $request->query('tipo');
        $numero = $request->query('numero');

        if (!$tipo || !$numero) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        $users = User::where('tipo_documento', $tipo)
            ->where('documento', $numero)
            ->first();

        if (!$users) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json([
            'id' => $users->id,
            'tipo_documento' => $users->tipo_documento,
            'documento' => $users->documento,
            'nombres' => $users->nombres,
            'apellidos' => $users->apellidos,
            'telefono' => $users->telefono,
            'direccion' => $users->direccion,
            'email' => $users->email,
            'fecha_nacimiento' => $users->fecha_nacimiento ?? '',
            'sexo' => $users->sexo ?? '',
        ]);
    }
}
