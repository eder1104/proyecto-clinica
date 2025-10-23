<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Crear Cita
        </h2>
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
                    <label class="block text-gray-700">Motivo de consulta</label>
                    <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Paciente</label>
                    <div class="flex items-center gap-2">
                        <select id="tipo_documento" class="border-gray-300 rounded-md shadow-sm">
                            <option value="CC">C.C.</option>
                            <option value="TI">T.I.</option>
                            <option value="CE">C.E.</option>
                        </select>
                        <input type="text" id="numero_documento" placeholder="Número de documento" class="border-gray-300 rounded-md shadow-sm">
                        <button type="button" id="buscarPaciente" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                            Buscar 🔍
                        </button>
                    </div>

                    <p id="paciente_info" class="mt-2 text-sm text-gray-600 italic hidden">Paciente Seleccionado: <span id="nombre_paciente" class="font-semibold text-green-700"></span></p>

                    <input type="hidden" name="paciente_id" id="paciente_id">
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalPaciente" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Datos del Paciente</h3>

            <form id="formPaciente">
                <input type="hidden" id="paciente_id_modal">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700">Tipo de Documento</label>
                        <select id="tipo_documento_modal" class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="CC">C.C.</option>
                            <option value="TI">T.I.</option>
                            <option value="CE">C.E.</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700">Número de Documento</label>
                        <input type="text" id="documento_modal" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Nombres</label>
                        <input type="text" id="nombres" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Apellidos</label>
                        <input type="text" id="apellidos" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Teléfono</label>
                        <input type="text" id="telefono" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Dirección</label>
                        <input type="text" id="direccion" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" id="email" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700">Sexo</label>
                        <select id="sexo" class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" id="cerrarModal" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cerrar</button>
                    <button type="button" id="guardarPaciente" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    #modalPaciente {
        display: none;
    }

    #modalPaciente.active {
        display: flex;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pacienteIdInput = document.getElementById('paciente_id');
        const nombrePacienteSpan = document.getElementById('nombre_paciente');
        const pacienteInfoP = document.getElementById('paciente_info');
        const formCita = document.getElementById('form-cita');
        const modalPaciente = document.getElementById('modalPaciente');

        const actualizarInfoPacienteSeleccionado = (id, nombres, apellidos) => {
            pacienteIdInput.value = id;
            nombrePacienteSpan.textContent = `${nombres} ${apellidos}`;
            pacienteInfoP.classList.remove('hidden');
        };

        const limpiarInfoPaciente = () => {
            pacienteIdInput.value = '';
            nombrePacienteSpan.textContent = '';
            pacienteInfoP.classList.add('hidden');
        };

        document.getElementById('buscarPaciente').addEventListener('click', async () => {
            const tipo = document.getElementById('tipo_documento').value;
            const numero = document.getElementById('numero_documento').value.trim();

            if (!numero) {
                alert('Por favor ingrese el número de documento');
                return;
            }

            try {
                const buscarPacienteUrl = "{{ route('pacientes.buscar') }}";

                const response = await fetch(`${buscarPacienteUrl}?tipo=${tipo}&numero=${numero}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (!response.ok || !data || !data.id) {
                    limpiarInfoPaciente();
                    alert(data.error || 'Paciente no encontrado. Por favor, verifique el número.');
                    return;
                }

                document.getElementById('paciente_id_modal').value = data.id;
                document.getElementById('tipo_documento_modal').value = data.tipo_documento;
                document.getElementById('documento_modal').value = data.documento;
                document.getElementById('nombres').value = data.nombres;
                document.getElementById('apellidos').value = data.apellidos;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('direccion').value = data.direccion;
                document.getElementById('email').value = data.email;
                document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento ?? '';
                document.getElementById('sexo').value = data.sexo ?? '';
                
                modalPaciente.classList.add('active');

            } catch (error) {
                limpiarInfoPaciente();
                alert('Error al buscar el paciente');
            }
        });


        const actualizarPacienteUrl = "{{ route('pacientes.actualizarApi', ['id' => 'ID_REEMPLAZAR']) }}";

        document.getElementById('guardarPaciente').addEventListener('click', async () => {
            const id = document.getElementById('paciente_id_modal').value;
            const url = actualizarPacienteUrl.replace('ID_REEMPLAZAR', id);

            const datos = {
                tipo_documento: document.getElementById('tipo_documento_modal').value,
                documento: document.getElementById('documento_modal').value,
                nombres: document.getElementById('nombres').value,
                apellidos: document.getElementById('apellidos').value,
                telefono: document.getElementById('telefono').value,
                direccion: document.getElementById('direccion').value,
                email: document.getElementById('email').value,
                fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                sexo: document.getElementById('sexo').value,
            };

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(datos)
                });

                if (response.ok) {
                    const data = await response.json();
                    alert('Datos actualizados correctamente y paciente seleccionado para la cita.');
                    modalPaciente.classList.remove('active');
                    
                    actualizarInfoPacienteSeleccionado(data.paciente.id, data.paciente.nombres, data.paciente.apellidos);

                } else if (response.status === 422) {
                    const err = await response.json();
                    let errorMessages = 'Errores de validación: \n';
                    for (const field in err.errors) {
                        errorMessages += `- ${err.errors[field].join(', ')}\n`;
                    }
                    alert(errorMessages);
                } else {
                    const err = await response.json().catch(() => ({}));
                    alert('Error al actualizar: ' + (err?.mensaje || `Desconocido (HTTP ${response.status})`));
                }
            } catch (error) {
                alert('Error de conexión con el servidor');
            }
        });

        document.getElementById('cerrarModal').addEventListener('click', () => {
            modalPaciente.classList.remove('active');
        });

        formCita.addEventListener('submit', (event) => {
            if (!pacienteIdInput.value) {
                event.preventDefault();
                alert('Debe buscar, seleccionar y/o actualizar el paciente antes de guardar la cita.');
            }
        });
    });
</script>