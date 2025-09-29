@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Editar Usuario') }}
</h2>
@endsection

@section('content')
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
        <h3 class="text-lg font-semibold mb-4">Editar Usuario</h3>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombres</label>
                <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="admisiones" {{ $user->role == 'admisiones' ? 'selected' : '' }}>Admisiones</option>
                    <option value="callcenter" {{ $user->role == 'callcenter' ? 'selected' : '' }}>Callcenter</option>
                    <option value="paciente" {{ $user->role == 'paciente' ? 'selected' : '' }}>Paciente</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
