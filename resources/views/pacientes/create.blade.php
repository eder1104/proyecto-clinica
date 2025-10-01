<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Paciente</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
        x-data="{ open: true }">

        <!-- fondo oscuro -->
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <!-- modal -->
            <div class="modal-container">
                <form action="{{ route('pacientes.store') }}" method="POST">
                    @csrf
                    <h2 class="modal-title">Nuevo Paciente</h2>

                    <div class="form-group">
                        <label for="nombres">Nombres</label>
                        <input type="text" name="nombres" id="nombres" class="input-field" value="{{ old('nombres') }}" required>
                        @error('nombres') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" class="input-field" value="{{ old('apellidos') }}" required>
                        @error('apellidos') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="documento">Documento</label>
                        <input type="text" name="documento" id="documento" class="input-field" value="{{ old('documento') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="input-field" value="{{ old('telefono') }}">
                    </div>

                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input type="email" name="email" id="email" class="input-field" value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="input-field" value="{{ old('direccion') }}">
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="input-field" value="{{ old('fecha_nacimiento') }}">
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select name="sexo" id="sexo" class="input-field">
                            <option value="">Seleccione...</option>
                            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('pacientes.index') }}" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .modal-container {
        background: #fff;
        width: 100%;
        max-width: 600px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 16px;
        color: #333;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
    }

    .input-field {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .input-field:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 4px rgba(37, 99, 235, 0.5);
    }

    .error-msg {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    .btn-cancel {
        background: #e5e7eb;
        color: #333;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }
</style>

