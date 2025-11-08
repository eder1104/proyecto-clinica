<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Crear Cita</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <strong>Errores encontrados:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('citas.store') }}" method="POST" id="form-cita">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Hora de inicio</label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Hora de fin</label>
                    <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Tipo de cita</label>
                    <select name="tipo_cita_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                        <option value="">-- Seleccione el tipo de cita --</option>
                        @foreach ($tipos_citas as $id => $nombre)
                        <option value="{{ $id }}" {{ old('tipo_cita_id') == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Motivo de consulta</label>
                    <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Paciente</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label>Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento" class="border-gray-300 rounded-md shadow-sm w-full">
                                <option value="CC">C.C.</option>
                                <option value="TI">T.I.</option>
                                <option value="CE">C.E.</option>
                            </select>
                        </div>
                        <div>
                            <label>Número de Documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" placeholder="Número de documento" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Nombres</label>
                            <input type="text" name="nombres" id="nombres" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label>Sexo</label>
                            <select name="sexo" id="sexo" class="border-gray-300 rounded-md shadow-sm w-full">
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="paciente_id" id="paciente_id">
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pacienteIdInput = document.getElementById('paciente_id');

            const obtenerDatosPaciente = () => ({
                tipo_documento: document.getElementById('tipo_documento').value,
                documento: document.getElementById('numero_documento').value,
                nombres: document.getElementById('nombres').value,
                apellidos: document.getElementById('apellidos').value,
                telefono: document.getElementById('telefono').value,
                direccion: document.getElementById('direccion').value,
                email: document.getElementById('email').value,
                fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                sexo: document.getElementById('sexo').value
            });

            const actualizarCamposPaciente = (data) => {
                pacienteIdInput.value = data.id;
                document.getElementById('tipo_documento').value = data.tipo_documento;
                document.getElementById('numero_documento').value = data.documento;
                document.getElementById('nombres').value = data.nombres;
                document.getElementById('apellidos').value = data.apellidos;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('direccion').value = data.direccion;
                document.getElementById('email').value = data.email;
                document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento ?? '';
                document.getElementById('sexo').value = data.sexo ?? '';
            };

            const limpiarCamposCompletos = () => {
                pacienteIdInput.value = '';
                document.getElementById('tipo_documento').value = 'CC';
                document.getElementById('numero_documento').value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('apellidos').value = '';
                document.getElementById('telefono').value = '';
                document.getElementById('direccion').value = '';
                document.getElementById('email').value = '';
                document.getElementById('fecha_nacimiento').value = '';
                document.getElementById('sexo').value = 'M';
            };

            const limpiarResultados = () => {
                pacienteIdInput.value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('apellidos').value = '';
                document.getElementById('telefono').value = '';
                document.getElementById('direccion').value = '';
                document.getElementById('email').value = '';
                document.getElementById('fecha_nacimiento').value = '';
                document.getElementById('sexo').value = 'M';
            };

            const numeroInput = document.getElementById('numero_documento');
            const tipoInput = document.getElementById('tipo_documento');

            let timeout = null;
            numeroInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(async () => {
                    const numero = numeroInput.value.trim();
                    const tipo = tipoInput.value;

                    if (!numero) {
                        limpiarCamposCompletos();
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('pacientes.buscar') }}?tipo=${tipo}&numero=${numero}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        const data = await response.json();

                        if (!response.ok || !data.id) {
                            limpiarResultados();
                            return;
                        }

                        actualizarCamposPaciente(data);
                    } catch (error) {
                        limpiarResultados();
                    }
                }, 500);
            });

            const formCita = document.getElementById('form-cita');
            formCita.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!pacienteIdInput.value) {
                    alert('Debe ingresar un paciente válido antes de guardar la cita.');
                    return;
                }

                const pacienteId = pacienteIdInput.value;
                const datosPaciente = obtenerDatosPaciente();

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`{{ route('pacientes.actualizarApi', ['id' => 'ID_REEMPLAZAR']) }}`.replace('ID_REEMPLAZAR', pacienteId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(datosPaciente)
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        alert('Error al actualizar paciente: ' + (err?.mensaje || `HTTP ${response.status}`));
                        return;
                    }

                    formCita.submit();

                } catch (error) {
                    alert('Error de conexión al actualizar paciente.');
                }
            });
        });
    </script>
</x-app-layout>