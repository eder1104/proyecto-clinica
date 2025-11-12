@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Bitácora de Auditoría</h2>

    <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro afectado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y hora</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @foreach($bitacoras as $registro)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $registro->usuario->nombres ?? 'Usuario desconocido (ID: ' . $registro->usuario_id . ')' }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $registro->modulo_descriptivo }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @php
                    $color = match($registro->accion) {
                    'crear' => 'green',
                    'editar' => 'blue',
                    'eliminar' => 'red',
                    default => 'gray'
                    };
                    @endphp

                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                        {{ $registro->accion_descriptiva }}
                    </span>

                    @if($registro->accion === 'editar')
                    <button
                        onclick="toggleComparativa('{{ $registro->id }}')"
                        class="ml-3 text-blue-600 text-xs hover:underline">
                        Ver cambios
                    </button>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $registro->registro_afectado }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-500">
                    {!! $registro->observacion_descriptiva !!}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $registro->fecha_hora 
                            ? \Carbon\Carbon::parse($registro->fecha_hora)->isoFormat('DD/MMM/YYYY h:mm A') 
                            : 'N/A' 
                        }}
                </td>
            </tr>

            @if($registro->accion === 'editar' && $registro->historialCambios->isNotEmpty())
            <tr id="comparativa-{{ $registro->id }}" class="hidden bg-gray-50 transition-all duration-200">
                <td colspan="6" class="p-4">
                    @foreach($registro->historialCambios as $cambio)
                    <div class="border rounded-lg p-3 mb-2 bg-white shadow-sm">
                        <strong class="text-blue-700">
                            Comparativa
                            ({{ \Carbon\Carbon::parse($cambio->fecha_cambio)->isoFormat('DD/MMM/YYYY HH:mm A') }})
                        </strong>

                        <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Antes</h4>
                                <pre class="bg-gray-100 p-2 rounded overflow-auto max-h-64">{{ json_encode($cambio->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Después</h4>
                                <pre class="bg-gray-100 p-2 rounded overflow-auto max-h-64">{{ json_encode($cambio->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function toggleComparativa(id) {
        const fila = document.getElementById(`comparativa-${id}`);
        if (fila) fila.classList.toggle('hidden');
    }
</script>
@endsection