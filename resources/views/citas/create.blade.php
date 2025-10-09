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
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de inicio</label>
                        <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de fin</label>
                        <input type="time" name="hora_fin" value="{{ old('hora_fin') }}"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Estado</label>
                        <select name="estado" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                            <option value="">Seleccione un estado</option>
                            <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmada" {{ old('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="finalizada" {{ old('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Motivo de consulta</label>
                        <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Admisiones</label>
                        <select name="admisiones_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
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
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                                <option value="">Seleccione un paciente</option>
                                @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                                    {{ $paciente->nombres }} {{ $paciente->apellidos }}
                                </option>
                                @endforeach
                            </select>
                            <button type="button" @click="openPaciente = true" class="bg-blue-600 text-white rounded px-3">🔎</button>
                            <button type="button" @click="openNuevoPaciente = true" class="bg-green-600 text-white rounded px-3">➕</button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" id="Save">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .agregar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .agregar button {
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const desc = document.getElementById('descripcion');
        if (desc) {
            desc.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('form-cita').submit();
                }
            });
        }
    });


    document.getElementById('Save').addEventListener('click', function() {
        document.getElementById('tipo_cita').addEventListener('change', function() {
            const value = this.value;
            if (value === '1') {
                window.location.href = "{{ route('plantillas.optometria') }}";
            }
        });
    });
</script>