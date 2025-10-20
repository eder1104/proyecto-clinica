<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar Paciente</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="{ open: true }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal-container">
                <form action="{{ route('pacientes.update', $paciente) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h2 class="modal-title">Editar Paciente</h2>

                    {{-- Nombres --}}
                    <div class="form-group">
                        <label for="nombres">Nombres</label>
                        <input type="text" name="nombres" id="nombres"
                               class="input-field @error('nombres') border-red-500 @enderror"
                               value="{{ old('nombres', $paciente->nombres) }}" required>
                        @error('nombres')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellidos --}}
                    <div class="form-group">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos"
                               class="input-field @error('apellidos') border-red-500 @enderror"
                               value="{{ old('apellidos', $paciente->apellidos) }}" required>
                        @error('apellidos')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Documento --}}
                    <div class="form-group">
                        <label for="documento">Documento</label>
                        <input type="text" name="documento" id="documento"
                               class="input-field @error('documento') border-red-500 @enderror"
                               value="{{ old('documento', $paciente->documento) }}" required>
                        @error('documento')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono"
                               class="input-field @error('telefono') border-red-500 @enderror"
                               value="{{ old('telefono', $paciente->telefono) }}">
                        @error('telefono')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Correo --}}
                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input type="email" name="email" id="email"
                               class="input-field @error('email') border-red-500 @enderror"
                               value="{{ old('email', $paciente->email) }}">
                        @error('email')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion"
                               class="input-field @error('direccion') border-red-500 @enderror"
                               value="{{ old('direccion', $paciente->direccion) }}">
                        @error('direccion')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de nacimiento --}}
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                               class="input-field @error('fecha_nacimiento') border-red-500 @enderror"
                               value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}">
                        @error('fecha_nacimiento')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sexo --}}
                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select name="sexo" id="sexo"
                                class="input-field @error('sexo') border-red-500 @enderror">
                            <option value="">Seleccione...</option>
                            <option value="M" {{ old('sexo', $paciente->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $paciente->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('sexo')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
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
        transition: all 0.2s ease;
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
        transition: background 0.2s;
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
        transition: background 0.2s;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }
</style>
