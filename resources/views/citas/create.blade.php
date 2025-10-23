<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Crear Cita
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">

            {{-- Mensajes --}}
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

            {{-- Formulario principal --}}
            <form action="{{ route('citas.store') }}" method="POST" id="form-cita">
                @csrf

                {{-- Fecha --}}
                <div class="mb-4">
                    <label class="block text-gray-700">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                {{-- Hora inicio --}}
                <div class="mb-4">
                    <label class="block text-gray-700">Hora de inicio</label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                {{-- Hora fin --}}
                <div class="mb-4">
                    <label class="block text-gray-700">Hora de fin</label>
                    <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                {{-- Motivo --}}
                <div class="mb-4">
                    <label class="block text-gray-700">Motivo de consulta</label>
                    <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>

                {{-- Paciente: buscar por tipo y número de documento --}}
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

                    {{-- Campo oculto donde se guarda el ID del paciente encontrado --}}
                    <input type="hidden" name="paciente_id" id="paciente_id">
                </div>

                {{-- Botones --}}
                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de edición de paciente --}}
    <div id="modalPaciente" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Datos del Paciente</h3>

            <form id="formPaciente">
                <input type="hidden" id="paciente_id_modal">

                <div class="grid grid-cols-2 gap-4">
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
    // Simulación de búsqueda de paciente (se reemplazará con AJAX más adelante)
    document.getElementById('buscarPaciente').addEventListener('click', async () => {
        const tipo = document.getElementById('tipo_documento').value;
        const numero = document.getElementById('numero_documento').value.trim();

        if (!numero) {
            alert('Por favor ingrese el número de documento');
            return;
        }

        try {
            const response = await fetch(`/api/pacientes/buscar?tipo=${tipo}&numero=${numero}`);
            const data = await response.json();

            if (!data || !data.id) {
                alert('Paciente no encontrado');
                return;
            }

            // Rellenar modal con los datos del paciente
            document.getElementById('paciente_id_modal').value = data.id;
            document.getElementById('nombres').value = data.nombres;
            document.getElementById('apellidos').value = data.apellidos;
            document.getElementById('telefono').value = data.telefono;
            document.getElementById('direccion').value = data.direccion;
            document.getElementById('email').value = data.email;

            document.getElementById('modalPaciente').classList.add('active');
        } catch (error) {
            alert('Error al buscar el paciente');
        }
    });

    // Guardar cambios en el paciente
    document.getElementById('guardarPaciente').addEventListener('click', async () => {
        const id = document.getElementById('paciente_id_modal').value;

        const datos = {
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            telefono: document.getElementById('telefono').value,
            direccion: document.getElementById('direccion').value,
            email: document.getElementById('email').value,
        };

        try {
            const response = await fetch(`/api/pacientes/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(datos)
            });

            if (response.ok) {
                alert('Datos actualizados correctamente');
                document.getElementById('modalPaciente').classList.remove('active');
                document.getElementById('paciente_id').value = id;
            } else {
                alert('Error al actualizar los datos');
            }
        } catch (error) {
            alert('Error de conexión con el servidor');
        }
    });

    // Cerrar modal
    document.getElementById('cerrarModal').addEventListener('click', () => {
        document.getElementById('modalPaciente').classList.remove('active');
    });
</script>
