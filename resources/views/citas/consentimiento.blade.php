<div class="modal fade" id="consentimientoModal" tabindex="-1" aria-labelledby="consentimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content card-clinica shadow-lg border-0">

            <form action="{{ route('consentimientos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="cita_id" id="cita_id">
                <input type="hidden" name="redirect_to_url" id="redirect_to_url" value="">

                <div class="modal-header header-clinica border-0 pb-2">
                    <h5 class="modal-title fs-5" id="consentimientoModalLabel">
                        <i class="bi bi-file-earmark-medical-fill me-2"></i> Registrar Consentimiento
                    </h5>
                </div>

                <div class="modal-body py-3 px-4">

                    <div class="mb-3 input-group-modern">
                        <label class="form-label form-label-clinica">Paciente</label>
                        <select name="paciente_id" class="form-select form-select-clinica" required id="consentimiento_paciente_id">
                            <option value="">Seleccione...</option>
                            @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}">{{ $paciente->nombres }} {{ $paciente->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 input-group-modern">
                        <label class="form-label form-label-clinica">Plantilla</label>
                        <select name="plantilla_id" class="form-select form-select-clinica" required>
                            <option value="">Seleccione...</option>
                            @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->id }}">{{ $plantilla->titulo }} @isset($plantilla->version) (v{{ $plantilla->version }})@endisset</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 input-group-modern">
                        <label class="form-label form-label-clinica">Nombre del Firmante</label>
                        <input type="text" name="nombre_firmante" class="form-control form-input-clinica" required placeholder="Nombre completo de quien firma">
                    </div>

                    <div class="mb-3 input-group-modern">
                        <label class="form-label form-label-clinica">Fecha de Firma</label>
                        <input type="date" name="fecha_firma" class="form-control form-input-clinica" required>
                    </div>

                    <div class="mb-3 input-group-modern">
                        <label class="form-label form-label-clinica">Firma (imagen)</label>
                        <input type="file" name="imagen_firma" class="form-control form-input-clinica" accept="image/jpeg,image/png" required>
                    </div>
                </div>

                <div class="modal-footer footer-clinica border-0 pt-3 justify-content-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm btn-save-clinica">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var consentimientoModal = document.getElementById('consentimientoModal');

        consentimientoModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var citaId = button.getAttribute('data-cita-id');
            var pacienteId = button.getAttribute('data-paciente-id');
            var tipoCita = button.getAttribute('data-tipo-cita');

            var modal = this;

            modal.querySelector('#cita_id').value = citaId;
            modal.querySelector('#consentimiento_paciente_id').value = pacienteId;

            var redirectToUrlInput = modal.querySelector('#redirect_to_url');

            if (tipoCita == '2') {
                var urlExamen = '{{ route("citas.examen", ["cita" => "PLACEHOLDER"]) }}';
                redirectToUrlInput.value = urlExamen.replace('PLACEHOLDER', citaId);
            } else {
                redirectToUrlInput.value = '';
            }
        });
    });

    function cerrarModalConsentimiento() {
        const modalElement = document.getElementById('consentimientoModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
</script>

<style>
    :root {
        --color-principal: #0d6efd;
        --color-texto: #343a40;
        --color-fondo: #f8f9fa;
        --color-borde: #dee2e6;
    }


    .modal-content {
        background-color: #fff;
        margin: 1.75rem auto;
        padding: 25px 35px;
        border-radius: 12px;
        width: 90%;
        height: auto;
        max-width: 420px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        text-align: center;
        font-family: 'Segoe UI', sans-serif;
        animation: slideDown 0.3s ease-out;
    }

    .modal {
        display: flex;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        justify-content: center;
        align-items: center;
    }

    .card-clinica {
        border-radius: 1rem !important;
        background: white;
        animation: fadeIn 0.3s ease-out;
    }

    .header-clinica {
        background-color: var(--color-fondo);
        color: var(--color-principal);
        border-bottom: 1px solid var(--color-borde);
    }

    .modal-title {
        color: var(--color-principal) !important;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .modal-body {
        padding-bottom: 10px !important;
    }

    .footer-clinica {
        background-color: var(--color-fondo);
        border-top: 1px solid var(--color-borde) !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
    }

    .input-group-modern {
        margin-bottom: 1rem;
    }

    .form-label-clinica {
        color: var(--color-texto);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .form-select-clinica,
    .form-input-clinica {
        border-radius: 0.5rem;
        border: 1px solid var(--color-borde);
        padding: 0.65rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-size: 0.95rem;
        width: 100%;
        background-color: white;
    }

    .form-select-clinica:focus,
    .form-input-clinica:focus {
        border-color: var(--color-principal);
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }

    .btn-save-clinica {
        font-weight: 600;
        background-color: var(--color-principal) !important;
        border-color: var(--color-principal) !important;
    }

    .btn-save-clinica:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
    }
</style>