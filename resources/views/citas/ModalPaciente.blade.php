<div id="modal-paciente" aria-hidden="true">
    <div class="modal-paciente-backdrop"></div>

    <div class="modal-paciente-panel" role="dialog" aria-modal="true" aria-labelledby="modal-paciente-title">
        <div class="modal-paciente-header">
            <h3 id="modal-paciente-title" class="modal-paciente-title">Detalles y observaciones</h3>
            <button type="button" class="modal-paciente-close" data-modal-close aria-label="Cerrar">✕</button>
        </div>

        <form id="modal-paciente-form" method="POST" action="">
            @csrf

            <div class="modal-paciente-form">
                <div class="form-grid">

                    <div class="full">
                        <label class="modal-paciente-label">H.C Name</label>
                        <input name="hc_name" class="modal-paciente-input"
                            value="{{ old('hc_name', $cita->paciente->nombres . ' ' . $cita->paciente->apellidos) }}">
                    </div>

                    <div>
                        <label class="modal-paciente-label">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="modal-paciente-input"
                            value="{{ old('fecha_nacimiento', \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->format('Y-m-d')) }}">
                    </div>

                    <div>
                        <label class="modal-paciente-label">Género</label>
                        <select name="sexo" class="modal-paciente-select">
                            <option value="M" {{ (old('sexo', $cita->paciente->sexo) == 'M') ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ (old('sexo', $cita->paciente->sexo) == 'F') ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>

                    <div>
                        <label class="modal-paciente-label">Teléfonos</label>
                        <input name="telefono" class="modal-paciente-input"
                            value="{{ old('telefono', $cita->paciente->telefono) }}">
                    </div>

                    <div>
                        <label class="modal-paciente-label">Presión arterial</label>
                        <input name="tension_arterial" class="modal-paciente-input"
                            value="{{ old('tension_arterial', $cita->tension_arterial) }}">
                    </div>

                    <div>
                        <label class="modal-paciente-label">Cita creada por</label>
                        <input name="created_by" class="modal-paciente-input"
                            value="{{ old('created_by', $cita->created_by) }}">
                    </div>

                    <div class="full">
                        <label class="modal-paciente-label">Agregar observaciones</label>
                        <textarea name="observaciones" class="modal-paciente-textarea">{{ old('observaciones', $cita->examen_fisico ?? '') }}</textarea>
                    </div>

                    <div class="full">
                        <label class="modal-paciente-label">Observaciones de la cita</label>
                        <textarea name="observaciones_cita" class="modal-paciente-textarea">{{ old('observaciones_cita', $cita->diagnostico ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="modal-paciente-label">Admisión creada por</label>
                        <input name="admisiones_id" class="modal-paciente-input"
                            value="{{ old('admisiones_id', $cita->admisiones_id) }}">
                    </div>

                    <div class="full">
                        <label class="modal-paciente-label">Motivo de la consulta</label>
                        <textarea name="motivo_consulta" class="modal-paciente-textarea">{{ old('motivo_consulta', $cita->motivo_consulta) }}</textarea>
                    </div>

                    <div>
                        <label class="modal-paciente-label">Fecha (actual con hora)</label>
                        <input type="datetime-local" name="fecha_hora" class="modal-paciente-input"
                            value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                </div>

                <div class="modal-paciente-actions">
                    <button type="submit" class="modal-paciente-btn modal-paciente-btn-primary">Guardar</button>
                    <button type="button" id="modal-cancel" class="modal-paciente-btn modal-paciente-btn-secondary" data-modal-close>Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    #modal-paciente {
        position: fixed;
        inset: 0;
        display: none;
        z-index: 1200;
        align-items: center;
        justify-content: center;
    }

    #modal-paciente.open {
        display: flex;
    }

    .modal-paciente-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.38);
        -webkit-tap-highlight-color: transparent;
    }

    .modal-paciente-panel {
        position: relative;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 12px 30px rgba(2, 6, 23, 0.15);
        width: 94%;
        max-width: 920px;
        max-height: 86vh;
        overflow: auto;
        padding: 20px;
        margin: 12px;
    }

    .modal-paciente-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .modal-paciente-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .modal-paciente-close {
        background: transparent;
        border: none;
        font-size: 1.05rem;
        cursor: pointer;
        padding: 6px;
        line-height: 1;
        color: #374151;
    }

    .modal-paciente-form .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    @media (max-width: 640px) {
        .modal-paciente-form .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .full {
        grid-column: 1 / -1;
    }

    .modal-paciente-label {
        display: block;
        font-size: 0.84rem;
        color: #374151;
        margin-bottom: 6px;
    }

    .modal-paciente-input,
    .modal-paciente-select {
        width: 100%;
        padding: 9px 10px;
        border: 1px solid #e6e9ef;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #111827;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        outline: none;
        transition: box-shadow .12s, border-color .12s;
    }

    .modal-paciente-input:focus,
    .modal-paciente-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
    }

    .modal-paciente-textarea {
        width: 100%;
        min-height: 110px;
        padding: 10px;
        border: 1px solid #e6e9ef;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #111827;
        resize: vertical;
        outline: none;
        transition: box-shadow .12s, border-color .12s;
    }

    .modal-paciente-textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
    }

    .modal-paciente-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .modal-paciente-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
    }

    .modal-paciente-btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .modal-paciente-btn-secondary {
        background: #f3f4f6;
        color: #111827;
    }
</style>

<script>
    (() => {
        const modal = document.getElementById('modal-paciente');
        if (!modal) return;

        const OPEN_EVENT = 'open-modal-paciente';
        const backdrop = modal.querySelector('.modal-paciente-backdrop');
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        const trigger = document.getElementById('open');

        function openModal() {
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        document.addEventListener(OPEN_EVENT, openModal);

        if (backdrop) {
            backdrop.addEventListener('click', closeModal);
        }

        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
        });

        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                document.dispatchEvent(new CustomEvent(OPEN_EVENT));
            });
        }

        const cancelBtn = modal.querySelector('#modal-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    })();
</script>