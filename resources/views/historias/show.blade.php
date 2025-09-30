<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historia Clínica de {{ $paciente->nombres }} {{ $paciente->apellidos }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Historial de PDFs de la Historia Clínica</h3>

                @if($pdfs->isEmpty())
                    <p class="text-gray-600">Este paciente no tiene PDFs generados aún.</p>
                @else
                    <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Nombre del archivo</th>
                                <th class="px-4 py-2">Fecha de creación</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pdfs as $pdf)
                                <tr>
                                    <td class="px-4 py-2">{{ basename($pdf->pdf_path) }}</td>
                                    <td class="px-4 py-2">{{ $pdf->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('citas.pdf', $pdf->id) }}"
                                           class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-700">
                                            Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
