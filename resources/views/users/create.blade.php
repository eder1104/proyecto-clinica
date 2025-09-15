<!-- Modal Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-lg shadow-lg">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirmación Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Cita</h2>
        </x-slot>

        <div class="py-6 max-w-2xl mx-auto">
            <form method="POST" action="{{ route('citas.store') }}" class="bg-white p-6 shadow rounded">
                @csrf

                <label>Paciente</label>
                <select name="user_id" class="w-full border rounded p-2 mb-4">
                    @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>

                <label>Fecha</label>
                <input type="date" name="fecha" class="w-full border rounded p-2 mb-4">

                <label>Hora</label>
                <input type="time" name="hora" class="w-full border rounded p-2 mb-4">

                <label>Motivo</label>
                <input type="text" name="motivo" class="w-full border rounded p-2 mb-4">

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
            </form>
        </div>
    </x-app-layout>

</div>