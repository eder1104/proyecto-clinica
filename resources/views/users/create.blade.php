<x-app-layout>
    <x-slot name="header">
        <h2 class="form-title">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="form-container" x-data="{ open: true, openRoleModal: false, selectedRole: '{{ old('role', '') }}' }">
        <div x-show="open" class="modal-overlay">
            <div class="modal-box">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nombres" class="form-label">Nombres</label>
                        <input type="text" name="nombres" id="nombres"
                            value="{{ old('nombres') }}"
                            class="form-input @error('nombres') input-error @enderror" required>
                        @error('nombres')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos"
                            value="{{ old('apellidos') }}"
                            class="form-input @error('apellidos') input-error @enderror" required>
                        @error('apellidos')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            class="form-input @error('email') input-error @enderror" required>
                        @error('email')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group" x-data="{ show: false }">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="password-container">
                            <input :type="show ? 'text' : 'password'" name="password" id="password"
                                class="form-input @error('password') input-error @enderror"
                                required>
                            <span class="toggle-eye" @click="show = !show" x-text="show ? '🙈' : '👁️'"></span>
                        </div>
                        @error('password')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group" x-data="{ show: false }">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <div class="password-container">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                                class="form-input" required>
                            <span class="toggle-eye" @click="show = !show" x-text="show ? '🙈' : '👁️'"></span>
                        </div>
                        @error('password_confirmation')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role_display" class="form-label">Rol</label>
                        <div class="role-selector-container">
                            <input type="text" id="role_display"
                                :value="selectedRole ? selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1) : 'Seleccione un rol'"
                                class="form-input" readonly
                                :class="{ 'input-error': {{ $errors->has('role') ? 'true' : 'false' }} }">
                            <button type="button" @click="openRoleModal = true" class="btn-open-modal">
                                Cambiar Rol
                            </button>
                            <input type="hidden" name="role" :value="selectedRole">
                        </div>
                        @error('role')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="btn-submit">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openRoleModal" class="modal-overlay">
            <div class="modal-box small-modal">
                <h3 class="modal-subtitle">Seleccionar Rol</h3>
                <div class="form-group">
                    <label for="role_select" class="form-label">Rol</label>
                    <select id="role_select" class="form-input" x-model="selectedRole">
                        <option value="">Seleccione un rol</option>
                        <option value="admin">Admin</option>
                        <option value="admisiones">Admisiones</option>
                        <option value="callcenter">Callcenter</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" @click="openRoleModal = false" class="btn-cancel">Cerrar</button>
                    <button type="button" @click="openRoleModal = false" class="btn-submit">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .form-container {
            max-width: 56rem;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.6);
            z-index: 50;
        }

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 32rem;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

    
        .small-modal {
            max-width: 20rem;
        }

        .modal-subtitle {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .role-selector-container {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .role-selector-container .form-input {
            flex-grow: 1;
            cursor: pointer;
        }

        .btn-open-modal {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background: gray;
            color: white;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
        }

        .btn-open-modal:hover {
            background: black;
        }

        .form-input {
            display: block;
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            outline: none;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        .input-error {
            border-color: #dc2626;
        }

        .error-text {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-cancel {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background: #e5e7eb;
            color: #111827;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #d1d5db;
        }

        .btn-submit {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background: #2563eb;
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: #1e40af;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .password-container {
            position: relative;
        }

        .toggle-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            font-size: 1.2rem;
        }
    </style>
</x-app-layout>