<div class="modal fade" id="consentimientoModal" tabindex="-1" aria-labelledby="consentimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <input type="hidden" id="cita_id" name="cita_id">
        <div class="modal-content p-3 rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="consentimientoModalLabel">Registrar Consentimiento Oftalmológico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('consentimientos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="paciente_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}">{{ $paciente->nombres }} {{ $paciente->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Plantilla</label>
                        <select name="plantilla_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->id }}">{{ $plantilla->titulo }} (v{{ $plantilla->version }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del Firmante</label>
                        <input type="text" name="nombre_firmante" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Firma</label>
                        <input type="date" name="fecha_firma" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Firma (imagen)</label>
                        <input type="file" name="imagen_firma" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cerrarModalConsentimiento">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cerrarModalConsentimiento() {
        document.getElementById('citas.index').style.display = 'none'
    }
</script>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        justify-content: center;
        align-items: center;
    }

    .consentimiento-modal {
        background: linear-gradient(145deg, #ffffff, #f2f6ff);
        border-radius: 14px;
        padding: 25px 35px;
        width: 420px;
        max-width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-title {
        text-align: center;
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .form-section {
        margin-bottom: 15px;
    }

    .form-label {
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .input-style {
        border: 1px solid #cfd8dc;
        border-radius: 8px;
        padding: 10px;
        width: 100%;
    }

    .input-style:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.4);
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-cancel {
        background-color: #adb5bd;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
    }

    .btn-save {
        background-color: #0d6efd;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
    }

    .btn-save:hover {
        background-color: #0069d9;
    }
</style>