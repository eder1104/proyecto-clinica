<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Crear Cita
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
        x-data="{ 
            open: true, 
            openPaciente: false, 
            openNuevoPaciente: false, 
            openEditar: false,
            search: '', 
            citaSeleccionada: {} 
        }">

        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow">
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

                @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('citas.store') }}" method="POST" id="form-cita">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700">Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha') }}"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de inicio</label>
                        <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de fin</label>
                        <input type="time" name="hora_fin" value="{{ old('hora_fin') }}"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Admisiones</label>
                        <select name="admisiones_id" class="mt-1 block w-full rounded-md shadow-sm">
                            <option value="">Seleccione un usuario de admisiones</option>
                            @foreach($admisiones as $adm)
                            <option value="{{ $adm->id }}" {{ old('admisiones_id') == $adm->id ? 'selected' : '' }}>
                                {{ $adm->nombres }} {{ $adm->apellidos }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Paciente</label>
                        <div class="agregar">
                            <select id="pacienteSelect" name="paciente_id"
                                class="mt-1 block w-full rounded-md shadow-sm">
                                <option value="">Seleccione un paciente</option>
                                @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" {{ old(key: 'paciente_id') == $paciente->id ? 'selected' : '' }}>
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }}
                                </option>
                                @endforeach
                            </select>
                            <button type="button" @click="openPaciente = true" class="bg-blue-600 rounded text-white">🔎</button>
                            <button type="button" @click="openNuevoPaciente = true" class="bg-green-600 rounded text-white">➕</button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 rou hover:bg-gray-400">Cancelar</a>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- buscar paciente --}}
        <div x-show="openPaciente" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Buscar Paciente</h3>
                <input type="text" x-model="search" placeholder="Buscar por nombre..." class="mb-4 w-full rounded-md shadow-sm">
                <div class="max-h-60 overflow-y-auto">
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border">ID</th>
                                <th class="p-2 border">Nombre</th>
                                <th class="p-2 border">Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pacientes as $paciente)
                            <tr x-show="'{{ strtolower($paciente->nombres . ' ' . $paciente->apellidos) }}'.includes(search.toLowerCase())">
                                <td class="p-2 border">{{ $paciente->id }}</td>
                                <td class="p-2 border">{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                                <td class="p-2 border text-center">
                                    <button type="button"
                                        class="bg-green-500 text-white px-2 py-1 rounded"
                                        @click="document.getElementById('pacienteSelect').value = '{{ $paciente->id }}'; openPaciente = false;">
                                        ✔
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" @click="openPaciente = false" class="px-4 py-2 rou hover:bg-gray-400">Cerrar</button>
                </div>
            </div>
        </div>

        {{-- editar cita --}}
        <div x-show="openEditar" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Editar Cita</h3>
                <form :action="'/citas/' + citaSeleccionada.id" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700">Fecha</label>
                        <input type="date" name="fecha" x-model="citaSeleccionada.fecha"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de inicio</label>
                        <input type="time" name="hora_inicio" x-model="citaSeleccionada.hora_inicio"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de fin</label>
                        <input type="time" name="hora_fin" x-model="citaSeleccionada.hora_fin"
                            class="mt-1 block w-full rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Admisiones</label>
                        <select name="admisiones_id" x-model="citaSeleccionada.admisiones_id"
                            class="mt-1 block w-full rounded-md shadow-sm">
                            <option value="">Seleccione un usuario de admisiones</option>
                            @foreach($admisiones as $adm)
                            <option value="{{ $adm->id }}">{{ $adm->nombres }} {{ $adm->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Paciente</label>
                        <select name="paciente_id" x-model="citaSeleccionada.paciente_id"
                            class="mt-1 block w-full rounded-md shadow-sm">
                            <option value="">Seleccione un paciente</option>
                            @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}">{{ $paciente->nombres }} {{ $paciente->apellidos }}</option>
                            @endforeach
                            <li>-- Seleccione un paciente --</li>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="openEditar = false"
                            class="px-4 py-2 rou hover:bg-gray-400">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

<style>
    .agregar {
        display: flex;
        flex-direction: row;
    }
    .agregar button {
        margin-left: 0.5rem;
        padding: 0.5rem 1rem;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const desc = document.getElementById('descripcion')
        if (desc) {
            desc.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault()
                    document.getElementById('form-cita').submit()
                }
            })
        }
    })
</script>
