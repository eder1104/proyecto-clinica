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

                    <a href="{{ route('citas.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md">
                        Nueva Cita
                    </a>

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
                                        <a href="{{ route('citas.edit', $c) }}" class="text-blue-600 hover:underline">
                                            Editar
                                        </a>

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
    </div>
</x-app-layout>
