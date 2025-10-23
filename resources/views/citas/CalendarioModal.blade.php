<div id="modalCitasDia" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2>Citas del <span id="modalFecha"></span></h2>

        <select id="selectCita" class="form-control" style="width:100%; margin-bottom:15px;">
            <option value="">Seleccione una cita</option>
        </select>

        <div id="detalleCita" class="cita-item" style="display:none;"></div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-content {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        width: 450px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
    .modal-close {
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
        font-size: 22px;
        color: #444;
    }
    .cita-item p {
        margin: 4px 0;
        font-size: 14px;
        color: #374151;
    }
    .empty-msg {
        text-align: center;
        color: #6b7280;
        padding: 12px;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modalCitasDia");
    const modalFecha = document.getElementById("modalFecha");
    const selectCita = document.getElementById("selectCita");
    const detalleCita = document.getElementById("detalleCita");
    const closeModal = modal.querySelector(".modal-close");

    closeModal.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => { if (e.target === modal) modal.style.display = "none"; });

    window.abrirModal = async (fecha) => {
        modalFecha.textContent = fecha;
        selectCita.innerHTML = `<option value="">Cargando citas...</option>`;
        detalleCita.style.display = "none";
        detalleCita.innerHTML = "";

        try {
            const res = await fetch(`/calendario/citas/${fecha}`);
            const citas = await res.json();

            if (!citas.length) {
                selectCita.innerHTML = `<option value="">No hay citas para esta fecha</option>`;
            } else {
                selectCita.innerHTML = `<option value="">Seleccione una cita</option>`;
                citas.forEach((c, i) => {
                    const hora = c.hora_inicio ? c.hora_inicio.slice(0,5) : "Sin hora";
                    const paciente = c.paciente ? `${c.paciente.nombres} ${c.paciente.apellidos}` : "Sin paciente";
                    selectCita.innerHTML += `<option value="${i}">${hora} - ${paciente}</option>`;
                });

                selectCita.onchange = () => {
                    const c = citas[selectCita.value];
                    if (!c) {
                        detalleCita.style.display = "none";
                        detalleCita.innerHTML = "";
                        return;
                    }

                    detalleCita.style.display = "block";
                    detalleCita.innerHTML = `
                        <p><strong>Fecha:</strong> ${c.fecha || 'N/A'}</p>
                        <p><strong>Hora inicio:</strong> ${c.hora_inicio || 'N/A'}</p>
                        <p><strong>Hora fin:</strong> ${c.hora_fin || 'N/A'}</p>
                        <p><strong>Estado:</strong> ${c.estado || 'N/A'}</p>
                        <p><strong>Tipo de cita:</strong> ${c.tipo_cita ? c.tipo_cita.nombre : c.tipo_cita_id || 'N/A'}</p>
                        <p><strong>Paciente:</strong> ${c.paciente ? c.paciente.nombres + ' ' + c.paciente.apellidos : c.paciente_id || 'N/A'}</p>
                        <p><strong>Motivo de cancelación:</strong> ${c.cancel_reason || 'Ninguno'}</p>
                    `;
                };
            }
        } catch {
            selectCita.innerHTML = `<option value="">Error al cargar las citas</option>`;
        }

        modal.style.display = "flex";
    };
});
</script>
