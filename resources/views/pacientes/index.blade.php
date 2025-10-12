<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pacientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">

                    <a href="{{ route('pacientes.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md transition">
                        Nuevo Paciente
                    </a>
                    <div  class="Table_Pacientes">
                        <table class="mt-9 w-full border border-gray-300 text-sm table_paciente">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-16">ID</th>
                                    <th class="border px-4 py-2 w-32">Nombres</th>
                                    <th class="border px-4 py-2 w-32">Apellidos</th>
                                    <th class="border px-4 py-2 w-24">Documento</th>
                                    <th class="border px-4 py-2 w-28">Teléfono</th>
                                    <th class="border px-4 py-2 w-48">Dirección</th>
                                    <th class="border px-4 py-2 w-40">Email</th>
                                    <th class="border px-4 py-2 w-28">Fecha Nac.</th>
                                    <th class="border px-4 py-2 w-20">Sexo</th>
                                    <th class="border px-4 py-2 w-40">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pacientes as $paciente)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2 text-center">{{ $paciente->id }}</td>
                                    <td class="border px-4 py-2 truncate" title="{{ $paciente->nombres }}">{{ $paciente->nombres }}</td>
                                    <td class="border px-4 py-2 truncate" title="{{ $paciente->apellidos }}">{{ $paciente->apellidos }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->documento }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->telefono }}</td>
                                    <td class="border px-4 py-2 truncate" title="{{ $paciente->direccion }}">{{ $paciente->direccion }}</td>
                                    <td class="border px-4 py-2 truncate" title="{{ $paciente->email }}">{{ $paciente->email }}</td>
                                    <td class="border px-4 py-2 truncate">{{ $paciente->fecha_nacimiento }}</td>
                                    <td class="border px-4 py-2 text-center">{{ $paciente->sexo }}</td>
                                    <td class="border px-4 py-2 flex space-x-2">
                                        <a href="{{ route('pacientes.edit', $paciente) }}"
                                            class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 edit">
                                            ✎ <h4>Editar</h4>
                                        </a>

                                        <form action="{{ route('pacientes.destroy', $paciente) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar este paciente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow hover:bg-red-700 delete">
                                                ❌ <h4>Eliminar</h4>
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
    </div>
</x-app-layout>

<style>
    .Table_Pacientes {
        overflow-x: auto;
        max-width: 100%;
    }

    .delete {
        display: flex;
        flex-direction: row;
        gap: 5px;
    }

    .edit {
        display: flex;
        flex-direction: row;
        gap: 5px;
    }
</style>