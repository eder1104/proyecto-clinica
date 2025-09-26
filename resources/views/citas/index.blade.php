<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Citas
        </h2>
    </x-slot>

    <div class="py-12"
        x-data="{
            openModal: false,
            editMode: false,
            cita: { id: null, fecha: '', hora_inicio: '', hora_fin: '', paciente_id: '', admisiones_id: '' },
            pacientes: @js($pacientes),
            admisiones: @js($admisiones),

            nuevaCita() {
                this.editMode = false
                this.cita = { id: null, fecha: '', hora_inicio: '', hora_fin: '', paciente_id: '', admisiones_id: '' }
                this.openModal = true
            },

            setCita(c) {
                this.editMode = true
                this.cita = {
                    id: c.id,
                    fecha: c.fecha,
                    hora_inicio: c.hora_inicio ? c.hora_inicio.substring(0,5) : '',
                    hora_fin: c.hora_fin ? c.hora_fin.substring(0,5) : '',
                    paciente_id: c.paciente_id,
                    admisiones_id: c.admisiones_id
                }
                this.openModal = true
            }
        }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
                    @endif

                    <button type="button"
                        @click="nuevaCita()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md">
                        Nueva Cita
                    </button>

                    <table class="mt-6 w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Fecha</th>
                                <th class="border px-4 py-2">Hora</th>
                                <th class="border px-4 py-2">Paciente</th>
                                <th class="border px-4 py-2">Atendido por</th>
                                <th class="border px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $c->id }}</td>
                                    <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2">{{ $c->hora_inicio }} - {{ $c->hora_fin }}</td>
                                    <td class="border px-4 py-2">
                                        {{ optional($c->paciente)->nombres ?? 'N/A' }}
                                        {{ optional($c->paciente)->apellidos ?? '' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ optional($c->admisiones)->nombres ?? 'N/A' }}
                                        {{ optional($c->admisiones)->apellidos ?? '' }}
                                    </td>
                                    <td class="border px-4 py-2 flex space-x-2">
                                        <button type="button"
                                            @click="setCita(@js($c))"
                                            class="text-blue-600 hover:underline">
                                            Editar
                                        </button>

                                        <form action="{{ route('citas.destroy', $c) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar esta cita?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-4 py-2 text-center text-gray-500">No hay citas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openModal" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow"
                @click.away="openModal = false">

                <h3 class="text-lg font-semibold mb-4" x-text="editMode ? 'Editar Cita' : 'Nueva Cita'"></h3>

                <form :action="editMode ? `{{ url('citas') }}/${cita.id}` : `{{ url('citas') }}`" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="mb-4">
                        <label class="block text-gray-700">Fecha</label>
                        <input type="date" name="fecha" x-model="cita.fecha"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de inicio</label>
                        <input type="time" name="hora_inicio" x-model="cita.hora_inicio"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Hora de fin</label>
                        <input type="time" name="hora_fin" x-model="cita.hora_fin"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Paciente</label>
                        <select name="clinica.pacientes_id" x-model="cita.user_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <template x-for="p in user" :key="p.id">
                                <option :value="p.id" x-text="`${p.nombres} ${p.apellidos}`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Admisiones</label>
                        <select name="admisiones_id" x-model="cita.admisiones_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <template x-for="a in admisiones" :key="a.id">
                                <option :value="a.id" x-text="`${a.nombres} ${a.apellidos}`"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="openModal = false"
                            class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                            x-text="editMode ? 'Actualizar' : 'Guardar'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
