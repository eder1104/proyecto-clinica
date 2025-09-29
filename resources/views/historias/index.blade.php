<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historia Clínica') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Módulo de Historia Clínica</h3>
                <p class="mb-4">Aquí podrás gestionar las historias clínicas de los pacientes.</p>

                <div x-data="{ open: false }">
                    <button 
                        @click="open = true" 
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Ver Pacientes
                    </button>

                    <div 
                        x-show="open"
                        class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50"
                        x-cloak>
                        <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-[80vh]">
                            <h2 class="text-xl font-semibold mb-4">Listado de Pacientes</h2>

                            <table class="w-full border-collapse border border-gray-300 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2">ID</th>
                                        <th class="border px-4 py-2">Nombres</th>
                                        <th class="border px-4 py-2">Apellidos</th>
                                        <th class="border px-4 py-2">Documento</th>
                                        <th class="border px-4 py-2">Teléfono</th>
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
                                            <td class="border px-4 py-2 text-center">
                                                <a href="{{ route('historias.show', $paciente->id) }}" 
                                                   class="text-blue-600 hover:underline text-lg"
                                                   title="Ver Historia Clínica">
                                                    📄
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="border px-4 py-2 text-center text-gray-500">
                                                No hay pacientes registrados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-4 flex justify-end">
                                <button 
                                    @click="open = false" 
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @isset($historia)
                    <div class="mt-6">
                        <h3 class="text-lg font-bold mb-4">Historia Clínica del paciente</h3>
                        <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Campo</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Detalle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-2 font-medium">Motivo de consulta</td>
                                    <td class="px-4 py-2">{{ $historia->motivo_consulta }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">Antecedentes</td>
                                    <td class="px-4 py-2">{{ $historia->antecedentes }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">Signos Vitales</td>
                                    <td class="px-4 py-2">{{ $historia->signos_vitales }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">Diagnóstico</td>
                                    <td class="px-4 py-2">{{ $historia->diagnostico }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">Conducta</td>
                                    <td class="px-4 py-2">{{ $historia->conducta }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endisset

                @if(session('error'))
                    <div class="mt-6 p-4 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
