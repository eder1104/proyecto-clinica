<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Citas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                    @endif

                    <a href="{{ route('citas.create') }}" class="btn">
                        Nueva Cita
                    </a>

                    <form method="GET" action="{{ route('citas.index') }}" class="mt-6 mb-4 flex space-x-4">
                        <select name="estado" class="border rounded px-6 py-3">
                            <option value=""> Estado </option>
                            <option value="programada" {{ request('estado') == 'programada' ? 'selected' : '' }}>programada</option>
                            <option value="modificada" {{ request('estado') == 'modificada' ? 'selected' : '' }}>modificada</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            <option value="no_asistida" {{ request('estado') == 'no_asistida' ? 'selected' : '' }}>no_asistida</option>
                            <option value="asistida" {{ request('estado') == 'asistida' ? 'selected' : '' }}>asistida</option>
                        </select>

                        <input type="date" name="fecha" value="{{ request('fecha') }}" class="border rounded px-2 py-1">

                        <button type="submit" class="btn">
                            Buscar
                        </button>

                        <a href="{{ route('citas.index') }}" class="btn-gray">
                            Limpiar
                        </a>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Atendido por</th>
                                <th>Estado</th>
                                <th>Observación</th>
                                <th>Acciones</th>
                                <th>Tomar Atención</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $c)
                            <tr>
                                <td>{{ $c->id }}</td>
                                <td>{{ date('d/m/Y', strtotime($c->fecha)) }}</td>
                                <td>{{ $c->hora_inicio }} - {{ $c->hora_fin }}</td>
                                <td>
                                    {{ optional($c->paciente)->nombres ?? 'N/A' }}
                                    {{ optional($c->paciente)->apellidos ?? '' }}
                                </td>
                                <td>
                                    {{ optional($c->createdBy)->nombres ?? 'N/A' }}
                                    {{ optional($c->createdBy)->apellidos ?? '' }}
                                </td>
                                <td>
                                    @if($c->estado === 'cancelada')
                                    <span class="estado-cancelada">Cancelada</span>
                                    @elseif($c->estado === 'finalizada')
                                    <span class="estado-finalizada">Finalizada</span>
                                    @else
                                    {{ ucfirst($c->estado) }}
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($c->estado, ['cancelada', 'finalizada']))
                                    {{ $c->cancel_reason ?? $c->mensaje ?? 'Sin motivo registrado' }}
                                    @else
                                    {{ $c->mensaje ?? '—' }}
                                    @endif
                                </td>
                                <td>
                                    @if (!in_array($c->estado, ['cancelada', 'finalizada']))
                                    <a href="{{ route('citas.edit', $c->id) }}">Editar</a>
                                    <form action="{{ route('citas.destroy', $c->id) }}" method="POST"
                                        onsubmit="return pedirRazon(this);">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="delete_reason">
                                        <button type="submit" class="btn-danger">Cancelar</button>
                                    </form>
                                    @else
                                    <span class="btn-disabled">Sin acciones</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!in_array($c->estado, ['cancelada', 'finalizada']))
                                    <a href="{{ route('preexamen.create', $c->id) }}" class="btn">Tomar Atención</a>
                                    @else
                                    <span class="btn-disabled">No disponible</span>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-gray-500">No hay citas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function pedirRazon(form) {
        const razon = prompt("Por favor ingresa la razón de cancelación:");
        if (!razon) {
            alert("Debes ingresar una razón para cancelar la cita.");
            return false;
        }
        form.querySelector('input[name="delete_reason"]').value = razon;
        return true;
    }
</script>

<style>
    .btn {
        background-color: #2563eb;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: inline-block;
        text-decoration: none;
    }

    .btn:hover {
        background-color: #1d4ed8;
    }

    .btn-gray {
        background-color: #9ca3af;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-gray:hover {
        background-color: #6b7280;
    }

    .btn-link {
        color: #2563eb;
        cursor: pointer;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .btn-danger {
        color: #dc2626;
        background: none;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-danger:hover {
        text-decoration: underline;
    }

    .btn-disabled {
        color: #9ca3af;
        font-style: italic;
        cursor: not-allowed;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
    }

    .table th,
    .table td {
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        text-align: left;
    }

    .table th {
        background-color: #f3f4f6;
    }

    .table tr:hover {
        background-color: #f9fafb;
    }

    .estado-cancelada {
        color: #dc2626;
        font-weight: bold;
    }
</style>