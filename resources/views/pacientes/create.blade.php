<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Paciente</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="{ open: true }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal-container">
                <form action="{{ route('pacientes.store') }}" method="POST">
                    @csrf
                    <h2 class="modal-title">Nuevo Paciente</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="nombres">Nombres</label>
                            <input type="text" name="nombres" id="nombres"
                                class="input-field @error('nombres') border-red-500 @enderror"
                                value="{{ old('nombres') }}" required>
                            @error('nombres')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos"
                                class="input-field @error('apellidos') border-red-500 @enderror"
                                value="{{ old('apellidos') }}" required>
                            @error('apellidos')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento"
                                class="input-field @error('tipo_documento') border-red-500 @enderror" required>
                                <option value="">Seleccione...</option>
                                <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                <option value="TI" {{ old('tipo_documento') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                <option value="PA" {{ old('tipo_documento') == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                            @error('tipo_documento')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="documento">Documento</label>
                            <input type="text" name="documento" id="documento"
                                class="input-field @error('documento') border-red-500 @enderror"
                                value="{{ old('documento') }}" required>
                            @error('documento')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono"
                                class="input-field @error('telefono') border-red-500 @enderror"
                                value="{{ old('telefono') }}">
                            @error('telefono')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" name="email" id="email"
                                class="input-field @error('email') border-red-500 @enderror"
                                value="{{ old('email') }}">
                            @error('email')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion"
                                class="input-field @error('direccion') border-red-500 @enderror"
                                value="{{ old('direccion') }}">
                            @error('direccion')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                class="input-field @error('fecha_nacimiento') border-red-500 @enderror"
                                value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select name="sexo" id="sexo"
                                class="input-field @error('sexo') border-red-500 @enderror">
                                <option value="">Seleccione...</option>
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('sexo')
                            <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>
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
        max-width: 900px;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.25);
        overflow-y: auto;
        max-height: 90vh;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #1e3a8a;
        text-align: center;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }

    .input-field {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .input-field:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 6px rgba(37, 99, 235, 0.4);
    }

    .error-msg {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-cancel {
        background: #e5e7eb;
        color: #111827;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }
</style>