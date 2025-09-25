<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pacientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('pacientes.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md transition">
                        Nuevo Paciente
                    </a>

                    <table class="mt-6 w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Nombres</th>
                                <th class="border px-4 py-2">Apellidos</th>
                                <th class="border px-4 py-2">Documento</th>
                                <th class="border px-4 py-2">Teléfono</th>
                                <th class="border px-4 py-2">Dirección</th>
                                <th class="border px-4 py-2">Email</th>
                                <th class="border px-4 py-2">Fecha Nacimiento</th>
                                <th class="border px-4 py-2">Sexo</th>
                                <th class="border px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pacientes as $paciente)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $paciente->id }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->nombres }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->apellidos }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->documento }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->telefono }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->direccion }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->email }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->fecha_nacimiento }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->sexo }}</td>
                                    <td class="border px-4 py-2 flex space-x-2">
                                        <a href="{{ route('pacientes.edit', $paciente) }}" 
                                           class="text-blue-600 hover:underline">Editar</a>
                                        
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Seguro que quieres eliminar este paciente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:underline">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="border px-4 py-2 text-center text-gray-500">
                                        No hay pacientes registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
