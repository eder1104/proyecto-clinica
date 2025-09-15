@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Usuarios') }}
</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4">
    @if(session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-100 text-green-800 px-4 py-2">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-2">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-end p-4 bg-gray-50 border-b">
            <button type="button" onclick="openAddModal()"
                class="inline-block px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                ➕ Agregar Usuario
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('usuarios.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2 py-1 rounded {{ $user->status == 'activo' ? 'bg-green-200 text-green-800 hover:bg-green-300' : 'bg-red-200 text-red-800 hover:bg-red-300' }} transition">
                                    {{ $user->status == 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2">
                            <button type="button"
                                data-id="{{ $user->id }}"
                                data-nombres="{{ $user->nombres }}"
                                data-apellidos="{{ $user->apellidos }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}"
                                onclick="openEditModal(this)"
                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 focus:outline-none transition">
                                ✎ Editar
                            </button>
                            <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm font-medium rounded-md shadow hover:bg-red-700 focus:outline-none transition">
                                    ❌ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Agregar Usuario</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombres</label>
                    <input type="text" name="nombres" value="{{ old('nombres') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('nombres') border-red-500 @enderror focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('nombres')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('apellidos') border-red-500 @enderror focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('apellidos')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('email') border-red-500 @enderror focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" name="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('password') border-red-500 @enderror focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="role"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <option value="admin">Admin</option>
                        <option value="admisiones">Admisiones</option>
                        <option value="callcenter">Callcenter</option>
                        <option value="paciente">Paciente</option>
                    </select>
                    @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Editar Usuario</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombres</label>
                    <input type="text" id="editNombres" name="nombres"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('nombres')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" id="editApellidos" name="apellidos"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('apellidos')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="editEmail" name="email"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                    @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <select id="editRole" name="role"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <option value="admin">Admin</option>
                        <option value="admisiones">Admisiones</option>
                        <option value="callcenter">Callcenter</option>
                        <option value="paciente">Paciente</option>
                    </select>
                    @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(button) {
        const id = button.getAttribute("data-id");
        const nombres = button.getAttribute("data-nombres");
        const apellidos = button.getAttribute("data-apellidos");
        const email = button.getAttribute("data-email");
        const role = button.getAttribute("data-role");

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editNombres').value = nombres;
        document.getElementById('editApellidos').value = apellidos;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        document.getElementById('editForm').action = `/usuarios/${id}`;
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }
</script>
@endsection