@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800">
    {{ __('Usuarios') }}
</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4" 
    x-data="{ 
        openRoleModal: false, 
        openCancelModal: false,
        selectedRole: '', 
        currentUserId: null,
        currentUserName: '',

        openChangeRoleModal(userId, currentRole) {
            this.currentUserId = userId;
            this.selectedRole = currentRole;
            this.openRoleModal = true;
        },

        openCancelUserModal(userId, userName) {
            this.currentUserId = userId;
            this.currentUserName = userName;
            this.openCancelModal = true;
        }
    }">

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-end p-4 bg-gray-50 border-b">
            <a href="{{ route('users.create') }}"
                class="inline-block px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2">
                ➕ Agregar Usuario
            </a>
            <button onclick="abrirModalBuscar()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-md transition flex items-center">
                🔍 Buscar usuario
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button"
                                @click="openChangeRoleModal({{ $user->id }}, '{{ $user->role }}')"
                                class="px-3 py-1 rounded-full text-xs font-semibold uppercase transition cursor-pointer">
                                {{ $user->role }}
                            </button>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2 py-1 rounded {{ $user->status == 'activo' ? 'bg-green-200 text-green-800 hover:bg-green-300' : 'bg-red-200 text-red-800 hover:bg-red-300' }}">
                                    {{ $user->status == 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2">
                            @if($user->status == 'activo')
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 editar">
                                ✎ Editar
                            </a>
                            <button type="button"
                                @click="openCancelUserModal({{ $user->id }}, '{{ $user->nombres }} {{ $user->apellidos }}')"
                                class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow hover:bg-red-700">
                                ❌ Eliminar
                            </button>
                            @else
                            <span class="text-gray-400 text-sm">usuario inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL CAMBIO DE ROL --}}
        <div x-show="openRoleModal" x-cloak 
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75" 
            @click.away="openRoleModal = false">

            <div class="bg-white w-full max-w-sm rounded-lg shadow-2xl p-6 transform transition-all duration-300 ease-out" @click.stop>
                <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Seleccionar Nuevo Rol</h3>

                <form :action="'{{ url('/users') }}' + '/' + currentUserId + '/role'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="role_select_modal" class="block text-sm font-medium text-gray-700">Rol</label>
                        <select id="role_select_modal" name="role" class="w-full border-gray-300 rounded-md shadow-sm p-2 mt-1" x-model="selectedRole" required>
                            <option value="admin">Admin</option>
                            <option value="admisiones">Admisiones</option>
                            <option value="callcenter">Callcenter</option>
                            <option value="doctor">Doctor</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="openRoleModal = false"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow">
                            Cerrar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow">
                            Guardar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ELIMINAR --}}
        <div x-show="openCancelModal" x-cloak 
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75" 
            @click.away="openCancelModal = false">

            <div class="bg-white w-full max-w-sm rounded-lg shadow-2xl p-6 transform transition-all duration-300 ease-out" @click.stop>
                <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Confirmar Eliminación</h3>
                <p class="text-gray-700 mb-4 text-sm">
                    ¿Estás seguro de que deseas eliminar al usuario <strong x-text="currentUserName"></strong>? <br>
                    Esta acción no se puede deshacer.
                </p>

                <form :action="'{{ url('/users') }}' + '/' + currentUserId" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openCancelModal = false"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md shadow">
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- PAGINACIÓN --}}
        @if ($users->hasPages())
        <div class="pagination">
            @if ($users->onFirstPage())
            <span>&laquo;</span>
            @else
            <a href="{{ $users->previousPageUrl() }}">&laquo;</a>
            @endif

            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
            <a href="{{ $url }}" class="active">{{ $page }}</a>
            @else
            <a href="{{ $url }}">{{ $page }}</a>
            @endif
            @endforeach

            @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}">&raquo;</a>
            @else
            <span>&raquo;</span>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

{{-- MODAL DE BÚSQUEDA --}}
<div id="modalBuscar" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div onclick="cerrarModalBuscar()" class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>

    <div class="bg-white w-full max-w-lg rounded-lg shadow-2xl p-6 transform transition-all duration-300 ease-out z-10">
        <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Buscar Usuario</h2>

        <form action="{{ route('users.buscar.lista') }}" method="GET" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Buscar por nombre o correo:</label>
                <input type="text" name="query" class="w-full border-gray-300 rounded-md shadow-sm p-2 mt-1"
                    placeholder="Escriba el nombre o correo del usuario" required>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
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

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }
    .pagination a,
    .pagination span {
        color: #333;
        padding: 6px 12px;
        text-decoration: none;
        border: 1px solid #ccc;
        margin: 0 2px;
        border-radius: 4px;
    }
    .pagination a:hover {
        background-color: #f0f0f0;
    }
    .pagination a.active {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }
    .editar {
        height: 2.6em;
        align-items: center;
        display: flex;
    }
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    function abrirModalBuscar() {
        const modal = document.getElementById('modalBuscar');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function cerrarModalBuscar() {
        const modal = document.getElementById('modalBuscar');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
