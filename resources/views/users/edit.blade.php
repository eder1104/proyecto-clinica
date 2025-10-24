@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Editar Usuario') }}
</h2>
@endsection

@section('content')
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-1/3 p-6" 
         x-data="{ openRoleModal: false, selectedRole: '{{ old('role', $user->role) }}' }">
        
        <h3 class="text-lg font-semibold mb-4">Editar Usuario</h3>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nombres</label>
                <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                @error('nombres')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                @error('apellidos')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex justify-end gap-2 mt-6"> 
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
            </div>
        </form>
       
        <div x-show="openRoleModal" @click.away="openRoleModal = false" class="modal-overlay" style="display: none;"> 
            <div class="modal-box small-modal">
                <h3 class="modal-subtitle">Seleccionar Rol</h3>
                <div class="form-group">
                    <label for="role_select" class="form-label">Rol</label>
                    
                    <select id="role_select" class="form-input" x-model="selectedRole">
                        <option value="">Seleccione un rol</option>
                        <option value="admin">Admin</option>
                        <option value="admisiones">Admisiones</option>
                        <option value="callcenter">Callcenter</option>
                        <option value="paciente">Paciente</option>
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
@endsection