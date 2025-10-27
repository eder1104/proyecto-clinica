<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Citas</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div id="tabla" class="tab-content active">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                    @endif

                    <a href="{{ route('citas.create') }}" class="btn">Nueva Cita</a>

                    <form method="GET" action="{{ route('citas.index') }}" class="mt-6 mb-4 flex space-x-4">
                        <select name="estado" class="border rounded px-6 py-3">
                            <option value="">Estado</option>
                            <option value="programada" {{ request('estado') == 'programada' ? 'selected' : '' }}>
                                Programada</option>
                            <option value="modificada" {{ request('estado') == 'modificada' ? 'selected' : '' }}>
                                Modificada</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada
                            </option>
                            <option value="no_asistida" {{ request('estado') == 'no_asistida' ? 'selected' : '' }}>No
                                asistida</option>
                            <option value="asistida" {{ request('estado') == 'asistida' ? 'selected' : '' }}>Asistida
                            </option>
                            <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>
                                Finalizada</option>
                        </select>

                        <input type="date" name="fecha" value="{{ request('fecha') }}"
                            class="border rounded px-2 py-1">

                        <button type="submit" class="btn">Buscar</button>
                        <a href="{{ route('citas.index') }}" class="btn-gray">Limpiar</a>
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
                                @php
                                    $isBlocked = in_array($c->estado, [
                                        'cancelada',
                                        'finalizada',
                                        'no_asistida',
                                        'asistida',
                                    ]);
                                @endphp
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ date('d/m/Y', strtotime($c->fecha)) }}</td>
                                    <td>{{ $c->hora_inicio }} - {{ $c->hora_fin }}</td>
                                    <td>{{ optional($c->paciente)->nombres ?? 'N/A' }}
                                        {{ optional($c->paciente)->apellidos ?? '' }}</td>
                                    <td>{{ optional($c->createdBy)->nombres ?? 'N/A' }}
                                        {{ optional($c->createdBy)->apellidos ?? '' }}</td>
                                    <td>
                                        @if ($c->estado === 'cancelada')
                                            <span class="estado-cancelada">Cancelada</span>
                                        @elseif($c->estado === 'finalizada')
                                            <span class="estado-finalizada">Finalizada</span>
                                        @elseif($c->estado === 'no_asistida')
                                            <span class="estado-no-asistida">No asistida</span>
                                        @elseif($c->estado === 'asistida')
                                            <span class="estado-asistida">Asistida</span>
                                        @else
                                            {{ ucfirst($c->estado) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (in_array($c->estado, ['cancelada', 'finalizada', 'no_asistida']))
                                            {{ $c->cancel_reason ?? ($c->mensaje ?? 'Sin motivo registrado') }}
                                        @else
                                            {{ $c->mensaje ?? '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$isBlocked)
                                            <a href="{{ route('citas.edit', ['cita' => $c->id]) }}">Editar</a>
                                            <form id="formEliminar{{ $c->id }}"
                                                action="{{ route('citas.destroy', $c->id) }}" method="POST"
                                                onsubmit="return abrirModalCancelacion(event, '{{ $c->id }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger">Cancelar</button>
                                            </form>
                                        @else
                                            <span class="btn-disabled">Sin acciones</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$isBlocked)
                                            <a href="{{ route('preexamen.create', ['cita' => $c->id]) }}"
                                                class="btn">Tomar Atención</a>
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

        @include('citas.calendario')
    </div>

    <div id="modalCancelacion" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Cancelar cita</h3>
            <p>Por favor, ingresa el motivo de cancelación:</p>
            <textarea id="motivoCancelacion" rows="3" style="width:100%;"></textarea>
            <div style="margin-top:10px; text-align:right;">
                <button onclick="cerrarModal()" class="btn-gray">Cerrar</button>
                <button onclick="confirmarCancelacion()" class="btn">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        let citaIdAEliminar = null

        function abrirModalCancelacion(event, id) {
            event.preventDefault()
            citaIdAEliminar = id
            document.getElementById('modalCancelacion').style.display = 'flex'
            return false
        }

        function cerrarModal() {
            document.getElementById('modalCancelacion').style.display = 'none'
            document.getElementById('motivoCancelacion').value = ''
            citaIdAEliminar = null
        }

        function confirmarCancelacion() {
            const motivo = document.getElementById('motivoCancelacion').value.trim()
            if (!motivo) {
                alert('Debes ingresar un motivo para cancelar la cita.')
                return
            }

            const form = document.getElementById('formEliminar' + citaIdAEliminar)
            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = 'delete_reason'
            input.value = motivo
            form.appendChild(input)
            form.submit()
        }
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 25px 35px;
            border-radius: 12px;
            width: 90%;
            height: auto;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content p {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .modal-content button {
            border: none;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-content button[type="submit"] {
            background-color: #e74c3c;
            color: white;
            margin-right: 10px;
        }

        .modal-content button[type="submit"]:hover {
            background-color: #c0392b;
        }

        .modal-content button[type="button"] {
            background-color: #bdc3c7;
            color: #2c3e50;
        }

        .modal-content button[type="button"]:hover {
            background-color: #95a5a6;
        }

        .btn {
            background-color: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
        }

        .btn-gray:hover {
            background-color: #6b7280;
        }

        .btn-danger {
            color: #dc2626;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 0;
            margin-left: 10px;
            display: inline;
        }

        .btn-danger:hover {
            text-decoration: underline;
        }

        .btn-disabled {
            color: #9ca3af;
            font-style: italic;
            cursor: not-allowed;
            display: inline-block;
        }

        .estado-cancelada {
            color: #dc2626;
            font-weight: bold;
        }

        .estado-finalizada {
            color: #059669;
            font-weight: bold;
        }

        .estado-no-asistida {
            color: #d97706;
            font-weight: bold;
        }

        .estado-asistida {
            color: #2563eb;
            font-weight: bold;
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
    </style>
</x-app-layout>
