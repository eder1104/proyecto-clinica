@extends('layouts.app')

@section('header')
<h2 class="page-title">
    {{ __('Usuarios') }}
</h2>
@endsection

@section('content')
<div class="main-container"
    x-data="{ 
        openRoleModal: false, 
        openCancelModal: false,
        openConfirmSelfModal: false,
        selectedRole: '', 
        tempRole: '',
        currentUserId: null,
        currentUserName: '',
        authUserId: {{ Auth::id() }},

        openChangeRoleModal(userId, currentRole) {
            this.currentUserId = userId;
            this.selectedRole = currentRole;
            this.openRoleModal = true;
        },

        openCancelUserModal(userId, userName) {
            this.currentUserId = userId;
            this.currentUserName = userName;
            this.openCancelModal = true;
        },

        handleRoleChange(newRole) {
            if (this.currentUserId === this.authUserId) {
                this.tempRole = newRole;
                this.openConfirmSelfModal = true;
            } else {
                this.selectedRole = newRole;
            }
        },

        confirmSelfChange() {
            this.selectedRole = this.tempRole;
            this.openConfirmSelfModal = false;
        }
    }">

    <div class="card-container">
        <div class="card-header">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                ➕ Agregar Usuario
            </a>
            <button onclick="abrirModalBuscar()" class="btn btn-success">
                🔍 Buscar usuario
            </button>
        </div>

        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="td-name">
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </td>
                        <td class="td-email">
                            {{ $user->email }}
                        </td>

                        <td class="td-role">
                            <button type="button"
                                @click="openChangeRoleModal({{ $user->id }}, '{{ $user->role }}')"
                                class="role-badge">
                                {{ $user->role }}
                            </button>
                        </td>

                        <td>
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="status-badge {{ $user->status == 'activo' ? 'status-active' : 'status-inactive' }}">
                                    {{ $user->status == 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>

                        <td class="td-actions">
                            @if($user->status == 'activo')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn-action btn-edit editar">
                                ✎ Editar
                            </a>
                            <button type="button"
                                @click="openCancelUserModal({{ $user->id }}, '{{ $user->nombres }} {{ $user->apellidos }}')"
                                class="btn-action btn-delete">
                                ❌ Eliminar
                            </button>
                            @else
                            <span class="text-inactive">usuario inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="td-empty">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="openRoleModal" x-cloak class="modal-overlay" @click.away="openRoleModal = false">
            <div class="modal-content" @click.stop>
                <h3 class="modal-title">Seleccionar Nuevo Rol</h3>

                <form :action="'{{ url('/users') }}' + '/' + currentUserId + '/role'" method="POST" class="modal-form">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="role_select_modal" class="form-label">Rol</label>
                        <select id="role_select_modal" name="role" class="form-select"
                            x-model="selectedRole"
                            @change="handleRoleChange($event.target.value)"
                            required>
                            <option value="admin">Admin</option>
                            <option value="admisiones">Admisiones</option>
                            <option value="callcenter">Callcenter</option>
                            <option value="doctor">Doctor</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="openRoleModal = false" class="btn btn-secondary">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Guardar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openConfirmSelfModal" x-cloak class="modal-overlay-dark">
            <div class="modal-content-small">
                <h3 class="modal-subtitle">Confirmar cambio de rol</h3>
                <p class="modal-text">¿Estás seguro de cambiar tu propio rol? Esto puede modificar tu acceso inmediato.</p>
                <div class="modal-footer-center">
                    <button @click="confirmSelfChange()" class="btn btn-primary">Sí, cambiar</button>
                    <button @click="openConfirmSelfModal=false" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>

        <div x-show="openCancelModal" x-cloak class="modal-overlay" @click.away="openCancelModal = false">
            <div class="modal-content" @click.stop>
                <h3 class="modal-title">Confirmar Eliminación</h3>
                <p class="modal-text">
                    ¿Estás seguro de que deseas eliminar al usuario <strong x-text="currentUserName"></strong>? <br>
                    Esta acción no se puede deshacer.
                </p>

                <form :action="'{{ url('/users') }}' + '/' + currentUserId" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-footer">
                        <button type="button" @click="openCancelModal = false" class="btn btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>

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

<div id="modalBuscar" class="modal-hidden">
    <div onclick="cerrarModalBuscar()" class="modal-backdrop"></div>

    <div class="modal-content-search">
        <h2 class="modal-title">Buscar Usuario</h2>

        <form action="{{ route('users.buscar.lista') }}" method="GET" class="modal-form">
            <div>
                <label class="form-label">Buscar por nombre o correo:</label>
                <input type="text" name="query" class="form-input"
                    placeholder="Escriba el nombre o correo del usuario" required>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="cerrarModalBuscar()" class="btn btn-secondary">
                    Cerrar
                </button>
                <button type="submit" class="btn btn-success">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Global & Typography */
    .page-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #1f2937;
    }

    /* Layout Containers */
    .main-container {
        max-width: 64rem;
        margin-left: auto;
        margin-right: auto;
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .card-container {
        background-color: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: flex-end;
        padding: 1rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
    }

    .btn-primary {
        background-color: #2563eb;
        color: white;
        margin-right: 0.5rem;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
    }

    .btn-success {
        background-color: #16a34a;
        color: white;
        display: flex;
        align-items: center;
    }

    .btn-success:hover {
        background-color: #15803d;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

    .btn-danger {
        background-color: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
    }

    .users-table {
        min-width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background-color: #f9fafb;
    }

    .users-table th {
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .users-table tbody {
        background-color: white;
        border-top: 1px solid #e5e7eb;
    }

    .users-table tr:nth-child(even) {
        background-color: #f9fafb;
    }

    .users-table tr:nth-child(odd) {
        background-color: white;
    }

    .users-table tr:hover {
        background-color: #f3f4f6;
        transition: background-color 0.2s;
    }

    .td-name {
        padding: 1rem 1.5rem;
        white-space: nowrap;
        font-size: 0.875rem;
        font-weight: 500;
        color: #111827;
    }

    .td-email {
        padding: 1rem 1.5rem;
        white-space: nowrap;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .td-role, .td-actions {
        padding: 1rem 1.5rem;
        white-space: nowrap;
        font-size: 0.875rem;
    }

    .td-role {
        font-weight: 500;
    }

    .td-actions {
        display: flex;
        gap: 0.5rem;
    }

    .td-empty {
        padding: 1rem 1.5rem;
        text-align: center;
        color: #6b7280;
        font-size: 0.875rem;
    }

    /* Badges & Specific Action Buttons */
    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
        background: #fff;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .status-active {
        background-color: #bbf7d0;
        color: #166534;
    }
    .status-active:hover { background-color: #86efac; }

    .status-inactive {
        background-color: #fecaca;
        color: #991b1b;
    }
    .status-inactive:hover { background-color: #fca5a5; }

    .btn-action {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        text-decoration: none;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background-color: #2563eb;
    }
    .btn-edit:hover { background-color: #1d4ed8; }

    .btn-delete {
        background-color: #dc2626;
        padding: 0.5rem 0.75rem;
    }
    .btn-delete:hover { background-color: #b91c1c; }

    .text-inactive {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .editar {
        height: 2.6em;
        align-items: center;
        display: flex;
    }

    /* Modals */
    .modal-overlay, .modal-overlay-dark {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay {
        background-color: rgba(17, 24, 39, 0.75);
    }

    .modal-overlay-dark {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content, .modal-content-small, .modal-content-search {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        padding: 1.5rem;
        transform: scale(1);
        transition: all 0.3s ease-out;
    }

    .modal-content {
        width: 100%;
        max-width: 24rem;
    }

    .modal-content-small {
        width: 100%;
        max-width: 24rem;
        text-align: center;
    }

    .modal-content-search {
        width: 100%;
        max-width: 32rem;
        z-index: 10;
        position: relative;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.5rem;
    }

    .modal-subtitle {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .modal-text {
        color: #374151;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .modal-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .form-select, .form-input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        padding: 0.5rem;
        margin-top: 0.25rem;
        box-sizing: border-box;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1rem;
    }

    .modal-footer-center {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }

    /* JavaScript Modal Logic Styles */
    .modal-hidden {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none; /* Default hidden */
        align-items: center;
        justify-content: center;
    }
    
    .modal-flex {
        display: flex !important;
    }

    .modal-backdrop {
        position: absolute;
        inset: 0;
        background-color: rgba(17, 24, 39, 0.5);
    }

    /* Pagination */
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

    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    function abrirModalBuscar() {
        const modal = document.getElementById('modalBuscar');
        modal.classList.remove('modal-hidden');
        modal.classList.add('modal-flex');
    }

    function cerrarModalBuscar() {
        const modal = document.getElementById('modalBuscar');
        modal.classList.remove('modal-flex');
        modal.classList.add('modal-hidden');
    }
</script>