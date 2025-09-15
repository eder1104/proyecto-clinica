<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historia Clínica de {{ $paciente->nombres }} {{ $paciente->apellidos }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Detalles de la Historia Clínica</h3>

                @if($historias->isEmpty())
                    <p class="text-gray-600">Este paciente no tiene historia clínica registrada.</p>
                @else
                    <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Motivo de consulta</th>
                                <th class="px-4 py-2">Antecedentes</th>
                                <th class="px-4 py-2">Signos Vitales</th>
                                <th class="px-4 py-2">Diagnóstico</th>
                                <th class="px-4 py-2">Conducta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($historias as $historia)
                                <tr>
                                    <td class="px-4 py-2">{{ $historia->motivo_consulta }}</td>
                                    <td class="px-4 py-2">{{ $historia->antecedentes }}</td>
                                    <td class="px-4 py-2">{{ $historia->signos_vitales }}</td>
                                    <td class="px-4 py-2">{{ $historia->diagnostico }}</td>
                                    <td class="px-4 py-2">{{ $historia->conducta }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
