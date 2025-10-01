<x-app-layout>
    <x-slot name="header">
        <h2 class="form-title">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="form-container" x-data="{ open: true }">
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

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="form-input @error('password') input-error @enderror" required>
                        @error('password')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Rol</label>
                        <select name="role" id="role"
                            class="form-input @error('role') input-error @enderror" required>
                            <option value="">Seleccione un rol</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="admisiones" {{ old('role') == 'admisiones' ? 'selected' : '' }}>Admisiones</option>
                            <option value="callcenter" {{ old('role') == 'callcenter' ? 'selected' : '' }}>Callcenter</option>
                        </select>
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
    </div>

    <style>
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937; /* gray-800 */
        }
        .form-container {
            max-width: 56rem; /* 7xl */
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
            max-width: 32rem; /* lg */
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151; /* gray-700 */
            margin-bottom: 0.5rem;
        }
        .form-input {
            display: block;
            width: 100%;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            outline: none;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: #2563eb; /* blue-600 */
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }
        .input-error {
            border-color: #dc2626; /* red-600 */
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
            background: #e5e7eb; /* gray-200 */
            color: #111827; /* gray-900 */
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-cancel:hover {
            background: #d1d5db; /* gray-300 */
        }
        .btn-submit {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background: #2563eb; /* blue-600 */
            color: white;
            font-weight: 600;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-submit:hover {
            background: #1e40af; /* blue-800 */
        }
        .btn-submit:active {
            transform: scale(0.98);
        }
    </style>
</x-app-layout>
