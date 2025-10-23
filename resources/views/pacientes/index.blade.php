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
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('pacientes.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md transition flex items-center gap-2">
                            ➕ Nuevo Paciente
                        </a>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('pacientes.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-md transition flex items-center gap-2">
                                🔄 Recargar
                            </a>

                            <button onclick="abrirModalBuscar()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-md transition flex items-center gap-2">
                                🔍 Buscar Paciente
                            </button>
                        </div>
                    </div>

                    <div class="Table_Pacientes">
                        <table class="mt-4 w-full border border-gray-300 text-sm table_paciente">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-16">ID</th>
                                    <th class="border px-4 py-2 w-32">Documento</th>
                                    <th class="border px-4 py-2 w-32">Nombre Completo</th>
                                    <th class="border px-4 py-2 w-40">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pacientes as $paciente)
                                <tr class="hover:bg-gray-100 cursor-pointer"
                                    onclick="mostrarInfoPaciente('{{ $paciente->toJson() }}')">
                                    <td class="border px-4 py-2 text-center">{{ $paciente->id }}</td>
                                    <td class="border px-4 py-2">{{ $paciente->documento }}</td>
                                    <td class="border px-4 py-2 truncate" title="{{ $paciente->nombres }} {{ $paciente->apellidos }}">
                                        {{ $paciente->nombres }} {{ $paciente->apellidos }}
                                    </td>
                                    <td class="border px-4 py-2 flex space-x-2 justify-center">
                                        <a href="{{ route('pacientes.edit', $paciente) }}"
                                            class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 edit">
                                            ✎
                                        </a>
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar este paciente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow hover:bg-red-700 delete">
                                                ❌
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="border px-4 py-2 text-center text-gray-500">
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


    <div id="modalInfoPaciente" class="modal hidden">
        <div class="modal-content max-w-lg">
            <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Detalles del Paciente</h2>

            <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm" id="infoPacienteContent">
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="cerrarModalInfo()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <div id="modalBuscar" class="modal hidden">
        <div class="modal-content">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Buscar Paciente</h2>

            <form action="{{ route('pacientes.buscar') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre:</label>
                    <input type="text" name="nombre" class="w-full border-gray-300 rounded-md shadow-sm p-2"
                        placeholder="Ingrese nombre del paciente">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Documento:</label>
                    <input type="text" name="documento" class="w-full border-gray-300 rounded-md shadow-sm p-2"
                        placeholder="Ingrese número de documento">
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="cerrarModalBuscar()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow">
                        Cerrar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md shadow">
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    .Table_Pacientes {
        overflow-x: auto;
        max-width: 100%;
    }

    .table_paciente tbody tr:hover {
        background-color: #f3f4f6;
    }

    .delete,
    .edit {
        display: flex;
        flex-direction: row;
        gap: 5px;
    }

    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 50;
    }

    .modal.hidden {
        display: none;
    }

    .modal-content {
        background: #fff;
        padding: 1.5rem;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.25s ease;
    }

    .modal-content.max-lg {
        max-width: 600px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .recargar {
        justify-content: right;
    }
</style>

<script>
    function abrirModalBuscar() {
        document.getElementById('modalBuscar').classList.remove('hidden');
    }

    function cerrarModalBuscar() {
        document.getElementById('modalBuscar').classList.add('hidden');
    }

    function cerrarModalInfo() {
        document.getElementById('modalInfoPaciente').classList.add('hidden');
    }

    function mostrarInfoPaciente(paciente) {
        const modal = document.getElementById('modalInfoPaciente');
        const content = document.getElementById('infoPacienteContent');

        function getTipoDocumento(code) {
            switch (code) {
                case 'CC':
                    return 'Cédula de Ciudadanía';
                case 'TI':
                    return 'Tarjeta de Identidad';
                case 'CE':
                    return 'Cédula de Extranjería';
                case 'PA':
                    return 'Pasaporte';
                default:
                    return code;
            }
        }

        modal.classList.remove('hidden');
    }


</script>