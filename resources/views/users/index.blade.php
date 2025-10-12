@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Usuarios') }}
</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4">
    @if(session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-100 text-green-800 px-4 py-2">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 rounded-md bg-red-50 border border-red-100 text-red-800 px-4 py-2">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-2">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-end p-4 bg-gray-50 border-b">
            <a href="{{ route('users.create') }}"
                class="inline-block px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                ➕ Agregar Usuario
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2 py-1 rounded {{ $user->status == 'activo' ? 'bg-green-200 text-green-800 hover:bg-green-300' : 'bg-red-200 text-red-800 hover:bg-red-300' }}">
                                    {{ $user->status == 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2">
                            @if($user->status == 'activo')
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700">
                                ✎ Editar
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded-md shadow hover:bg-red-700">
                                    ❌ Eliminar
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 text-sm">usuario inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection