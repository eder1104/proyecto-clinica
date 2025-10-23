@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Usuarios') }}
</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4">
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
                    @forelse ($users as $user)
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
                            @else
                            <span class="text-gray-400 text-sm">usuario inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
        <div class="pagination">
            @if ($users->onFirstPage())
            <span>&laquo;</span>
            @else
            <a href="{{ $users->previousPageUrl() }}">&laquo;</a>
            @endif

            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
            <a href="{{ $url }}" class="active">{{ $page }}</a>
            @else
            <a href="{{ $url }}">{{ $page }}</a>
            @endif
            @endforeach

            @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}">&raquo;</a>
            @else
            <span>&raquo;</span>
            @endif
        </div>
        @endif

    </div>
</div>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }

    .pagination a,
    .pagination span {
        color: #333;
        padding: 6px 12px;
        text-decoration: none;
        border: 1px solid #ccc;
        margin: 0 2px;
        border-radius: 4px;
    }

    .pagination a:hover {
        background-color: #f0f0f0;
    }

    .pagination a.active {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }
</style>
@endsection