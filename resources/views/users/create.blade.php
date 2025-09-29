<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
        x-data="{ open: true }">

        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="nombres" class="block text-gray-700">Nombres</label>
                        <input type="text" name="nombres" id="nombres"
                            value="{{ old('nombres') }}"
                            class="mt-1 block w-full rounded-md shadow-sm @error('nombres') border-red-500 @enderror" required>
                        @error('nombres')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="apellidos" class="block text-gray-700">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos"
                            value="{{ old('apellidos') }}"
                            class="mt-1 block w-full rounded-md shadow-sm @error('apellidos') border-red-500 @enderror" required>
                        @error('apellidos')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Correo Electrónico</label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            class="mt-1 block w-full rounded-md shadow-sm @error('email') border-red-500 @enderror" required>
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700">Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md shadow-sm @error('password') border-red-500 @enderror" required>
                        @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="block text-gray-700">Rol</label>
                        <select name="role" id="role"
                            class="mt-1 block w-full rounded-md shadow-sm @error('role') border-red-500 @enderror" required>
                            <option value="">Seleccione un rol</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="admisiones" {{ old('role') == 'admisiones' ? 'selected' : '' }}>Admisiones</option>
                            <option value="callcenter" {{ old('role') == 'callcenter' ? 'selected' : '' }}>Callcenter</option>
                        </select>
                        @error('role')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
