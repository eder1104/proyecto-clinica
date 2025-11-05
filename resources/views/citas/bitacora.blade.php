@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-2xl font-bold mb-4">Bitácora de Auditoría</h2>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">Usuario</th>
                <th class="border px-4 py-2">Módulo</th>
                <th class="border px-4 py-2">Acción</th>
                <th class="border px-4 py-2">Registro afectado</th>
                <th class="border px-4 py-2">Observación</th>
                <th class="border px-4 py-2">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $r)
            <tr>
                <td class="border px-4 py-2">{{ $r->usuario_id }}</td>
                <td class="border px-4 py-2">{{ $r->modulo }}</td>
                <td class="border px-4 py-2">{{ $r->accion }}</td>
                <td class="border px-4 py-2">{{ $r->registro_afectado }}</td>
                <td class="border px-4 py-2">{{ $r->created_at }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-3">No hay registros en la bitácora</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection