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

            @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
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
                    <label class="block text-gray-700">Admisiones</label>
                    <select name="admisiones_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                        <option value="">Seleccione un usuario de admisiones</option>
                        @forelse ($users as $user)
                        @if($user->role == 'admisiones')
                        <option value="{{ $user->id }}">
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </option>
                        @endif
                        @empty
                        <option value="">No hay usuarios de admisión</option>
                        @endforelse
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Paciente</label>
                    <div class="agregar">
                        <select id="pacienteSelect" name="paciente_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                            <option value="">Seleccione un paciente</option>
                            @foreach($pacientes as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->nombres }} {{ $p->apellidos }}
                            </option>
                            @endforeach
                        </select>

                        </select>
                    </div>
                </div>


                <div class="flex justify-end space-x-2 mt-4">
                    <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded bg-gray-400 text-white hover:bg-gray-500">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    .agregar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tipoCita = document.getElementById('tipo_cita');

        if (tipoCita) {
            tipoCita.addEventListener('change', function() {
                if (this.value === 'optometria') {
                    window.location.href = "{{ route('plantillas.optometria') }}";
                }
            });
        }
    });
</script>