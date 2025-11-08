@section('content')
<div class="calendar-container">
    <div class="calendar-wrapper">
        <h1 class="calendar-title">Calendario de Citas</h1>

        <div class="calendar-box">
            <div class="calendar-header">
                <button id="prevMonth">‹</button>
                <div id="monthYear"></div>
                <button id="nextMonth">›</button>
            </div>

            <div class="calendar-weekdays">
                <div>Lun</div>
                <div>Mar</div>
                <div>Mié</div>
                <div>Jue</div>
                <div>Vie</div>
                <div>Sáb</div>
                <div>Dom</div>
            </div>

            <div id="calendarDays" class="calendar-days"
                data-dias='@json($dias)'
                data-citas-url="{{ route('calendario.citasPorDia', ['fecha' => '__DATE__']) }}">
            </div>
        </div>
    </div>
</div>

<div id="modalCitasDia" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2 style="color:#10b981;">Citas del <span id="modalFechaDisplay"></span></h2>
        <div id="modalBloqueoInfo" class="alert-bloqueo" style="display:none;"></div>
        <div id="listadoCitasDia"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const calendarDaysDiv = document.getElementById("calendarDays");
        const monthYear = document.getElementById("monthYear");
        const diasData = JSON.parse(calendarDaysDiv.dataset.dias || '[]');
        const citasUrlTemplate = calendarDaysDiv.dataset.citasUrl;
        let currentDate = new Date();
        let citasCached = {};

        const modal = document.getElementById("modalCitasDia");
        const modalFechaDisplay = document.getElementById("modalFechaDisplay");
        const modalBloqueoInfo = document.getElementById("modalBloqueoInfo");
        const closeModal = modal.querySelector(".modal-close");
        const listadoCitasDia = document.getElementById("listadoCitasDia");

        const diasInfo = {};
        diasData.forEach(d => diasInfo[d.fecha] = d);

        window.abrirModal = async (fecha) => {
            modalFechaDisplay.textContent = fecha;
            listadoCitasDia.innerHTML = '';

            const infoDia = diasInfo[fecha];
            if (infoDia && infoDia.estado === 'bloqueado') {
                modalBloqueoInfo.style.display = 'block';
                modalBloqueoInfo.innerHTML = `⚠️ Día bloqueado por: ${infoDia.doctor || 'Doctor no especificado'}`;
                listadoCitasDia.innerHTML = `<div class="bloqueado-aviso">Día completamente bloqueado</div>`;
            } else {
                modalBloqueoInfo.style.display = 'none';
                const citas = await fetchCitas(fecha);
                if (!citas.length) {
                    listadoCitasDia.innerHTML = `<div class="no-citas">No hay citas registradas para esta fecha</div>`;
                } else {
                    listadoCitasDia.innerHTML = "";
                    citas.forEach((c) => {
                        const horaInicio = c.hora_inicio?.slice(0, 5) || 'Sin hora';
                        const horaFin = c.hora_fin?.slice(0, 5) || 'Sin hora';
                        const tipo = c.tipo_cita?.nombre || 'Sin tipo';
                        const paciente = c.paciente ? `${c.paciente.nombres} ${c.paciente.apellidos}` : 'Sin paciente';
                        const item = document.createElement('div');
                        item.className = "card-cita";
                        item.innerHTML = `
                            <div><b>Hora:</b> ${horaInicio} - ${horaFin}</div>
                            <div><b>Tipo:</b> ${tipo}</div>
                            <div><b>Paciente:</b> ${paciente}</div>
                            <div><b>Estado:</b> ${c.estado}</div>
                        `;
                        listadoCitasDia.appendChild(item);
                    });
                }
            }
            modal.style.display = "flex";
        };

        const fetchCitas = async (fecha) => {
            try {
                if (citasCached[fecha]) return citasCached[fecha];
                const url = citasUrlTemplate.replace('__DATE__', fecha);
                const res = await fetch(url);
                const citas = await res.json();
                citasCached[fecha] = citas;
                return citas;
            } catch {
                return [];
            }
        };

        closeModal.onclick = () => modal.style.display = "none";
        window.onclick = (e) => {
            if (e.target === modal) modal.style.display = "none";
        };

        const renderCalendar = (date) => {
            calendarDaysDiv.innerHTML = "";
            const year = date.getFullYear(),
                month = date.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDay = (firstDay.getDay() + 6) % 7;
            monthYear.textContent = date.toLocaleString("es-ES", {
                month: "long",
                year: "numeric"
            }).toUpperCase();

            for (let i = 0; i < startDay; i++) calendarDaysDiv.appendChild(document.createElement("div"));

            for (let d = 1; d <= lastDay.getDate(); d++) {
                const fullDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
                const dayDiv = document.createElement("div");
                dayDiv.classList.add("calendar-day");
                const info = diasInfo[fullDate];
                let diaEstado = info?.estado || "disponible";
                if (diaEstado === "bloqueado") dayDiv.classList.add("day-bloqueado");
                else if (diaEstado === "parcial") dayDiv.classList.add("day-parcial");
                else if (diaEstado === "cita" || diaEstado === "disponible") dayDiv.classList.add("day-activo");

                dayDiv.innerHTML = `<div class="day-number">${d}</div><span class="day-status">${diaEstado === "disponible" ? "disponible" : diaEstado}</span>`;
                dayDiv.onclick = () => abrirModal(fullDate);
                calendarDaysDiv.appendChild(dayDiv);
            }
        };

        document.getElementById("prevMonth").onclick = () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        };
        document.getElementById("nextMonth").onclick = () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        };
        renderCalendar(currentDate);
    });
</script>

<style>
    .calendar-container {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 60px 20px;
    }

    .calendar-wrapper {
        width: 90%;
        max-width: 900px;
    }

    .calendar-title {
        text-align: center;
        font-size: 2.2rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 30px;
    }

    .calendar-box {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .calendar-header {
        background: #e5e7eb;
        color: #374151;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 28px;
        font-weight: 600;
    }

    .calendar-header button {
        background: transparent;
        border: none;
        color: #374151;
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .calendar-header button:hover {
        transform: scale(1.2);
    }

    .calendar-header #monthYear {
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        background: #f9fafb;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #6b7280;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        padding: 25px;
        text-align: center;
    }

    .calendar-day {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px 0;
        font-weight: 600;
        color: #4b5563;
        transition: all 0.2s ease;
        cursor: pointer;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .calendar-day:hover {
        background: #e5e7eb;
        transform: scale(1.03);
    }

    .day-number {
        font-size: 1.05rem;
        margin-bottom: 6px;
    }

    .day-status {
        display: block;
        font-size: 0.75rem;
        margin-top: 5px;
        text-transform: capitalize;
        font-weight: 600;
    }

    .day-activo {
        background-color: #d1fae5 !important;
        border-color: #10b981 !important;
        color: #065f46 !important;
    }

    .day-bloqueado {
        background-color: #fecaca !important;
        border-color: #f87171 !important;
        color: #7f1d1d !important;
    }

    .day-parcial {
        background-color: #fef9c3 !important;
        border-color: #facc15 !important;
        color: #78350f !important;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        max-width: 400px;
        width: 90%;
        position: relative;
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .modal-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        color: #9ca3af;
        cursor: pointer;
        font-weight: bold;
    }

    .modal-content h2 {
        font-size: 1.5rem;
        color: #10b981;
        margin-bottom: 25px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 10px;
    }

    #listadoCitasDia {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 12px;
        margin-bottom: 10px;
    }

    .card-cita {
        padding: 12px 15px;
        border-radius: 7px;
        background: #e0f6ec;
        border: 1px solid #10b981;
        font-size: 1rem;
        color: #222;
        font-weight: 500;
        cursor: pointer;
    }

    .card-cita:hover {
        background: #d1fae5;
    }

    .cita-estado {
        float: right;
        color: #065f46;
        font-size: 0.88em;
    }

    .bloqueado-aviso {
        padding: 14px 18px;
        background-color: #fdecea;
        border: 1px solid #f87171;
        border-radius: 8px;
        color: #b91c1c;
        font-size: 1em;
        text-align: center;
        font-weight: 500;
    }

    .no-citas {
        padding: 10px 0 0 0;
        color: #374151;
        text-align: center;
    }

    #detalleCita {
        margin-top: 20px;
        padding: 15px;
        background-color: #f3f4f6;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .form-group-read-only label {
        display: block;
        font-weight: 600;
        margin-bottom: 3px;
        font-size: 0.85rem;
        color: #4b5563;
    }

    .form-group-read-only select {
        width: 100%;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        background-color: #ffffff;
        color: #1f2937;
        font-weight: 500;
    }

    .alert-bloqueo {
        padding: 12px 15px;
        background-color: #fef2f2;
        border: 1px solid #f87171;
        border-radius: 8px;
        color: #b91c1c;
        font-size: 0.95rem;
        font-weight: 500;
        margin-bottom: 10px;
        text-align: center;
    }
</style>
@endsection