<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historia Clínica') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Lista de Pacientes</h3>

                <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nombre completo</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Documento</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td class="px-4 py-2">{{ $paciente->id }}</td>
                                <td class="px-4 py-2">{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                                <td class="px-4 py-2">{{ $paciente->documento }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('historias.cita', $paciente->id) }}" 
                                       class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
                                        Historia Clínica
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(session('success'))
                    <div class="mt-6 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-6 p-4 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
