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
                data-citas='@json($citas)'></div>
        </div>
    </div>
</div>

<div id="modalCitasDia" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2>Citas del <span id="modalFechaDisplay"></span></h2>

        <select id="selectCita" class="form-control">
            <option value="">Seleccione una cita</option>
        </select>

        <div id="detalleCita" class="cita-item"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const calendarDaysDiv = document.getElementById("calendarDays");
        const monthYear = document.getElementById("monthYear");
        const initialCitas = JSON.parse(calendarDaysDiv.dataset.citas || '[]');
        let currentDate = new Date();
        let citasCached = {};

        const modal = document.getElementById("modalCitasDia");
        const modalFechaDisplay = document.getElementById("modalFechaDisplay");
        const selectCita = document.getElementById("selectCita");
        const detalleCita = document.getElementById("detalleCita");
        const closeModal = modal.querySelector(".modal-close");

        const createReadOnlySelect = (label, value) => `
        <div class="form-group-read-only">
            <label>${label}:</label>
            <select disabled><option selected>${value || 'N/A'}</option></select>
        </div>`;

        window.abrirModal = async (fecha) => {
            modalFechaDisplay.textContent = fecha;
            selectCita.innerHTML = `<option value="">Cargando citas...</option>`;
            detalleCita.style.display = "none";
            detalleCita.innerHTML = "";
            modal.style.display = "flex";

            const citas = citasCached[fecha] || await fetchCitas(fecha);
            fillSelect(citas);
        };

        const fetchCitas = async (fecha) => {
            try {
                const res = await fetch(`/calendario/citas/${fecha}`);
                const citas = await res.json();
                citasCached[fecha] = citas;
                return citas;
            } catch {
                return [];
            }
        };

        const fillSelect = (citas) => {
            selectCita.citasData = citas;

            if (!citas || !citas.length) {
                selectCita.innerHTML = `<option value="">No hay citas para esta fecha</option>`;
            } else {
                selectCita.innerHTML = `<option value="" selected>Seleccione una cita (${citas.length} disponibles)</option>`;
                citas.forEach((c, i) => {
                    const hora = c.hora_inicio ? c.hora_inicio.slice(0, 5) : "Sin hora";
                    const estado = c.estado || 'N/A';
                    const tipoCita = c.tipo_cita ? c.tipo_cita.nombre : 'Sin Tipo';
                    const paciente = c.paciente ?
                        `${c.paciente.nombres.split(' ')[0]} ${c.paciente.apellidos.split(' ')[0]}` :
                        'Sin paciente';

                    const displayText = `[${estado}] ${hora} - ${tipoCita} (${paciente})`;

                    selectCita.innerHTML += `<option value="${i}">${displayText}</option>`;
                });
            }
        }

        selectCita.onchange = () => {
            const c = selectCita.citasData[selectCita.value];

            if (!c) {
                detalleCita.style.display = "none";
                detalleCita.innerHTML = "";
                return;
            }

            detalleCita.style.display = "block";

            const tipoCita = c.tipo_cita ? c.tipo_cita.nombre : 'N/A';
            const pacienteNombre = c.paciente ? c.paciente.nombres + ' ' + c.paciente.apellidos : 'N/A';

            detalleCita.innerHTML =
                createReadOnlySelect('Fecha', c.fecha) +
                createReadOnlySelect('Hora inicio', c.hora_inicio ? c.hora_inicio.slice(0, 5) : 'N/A') +
                createReadOnlySelect('Hora fin', c.hora_fin ? c.hora_fin.slice(0, 5) : 'N/A') +
                createReadOnlySelect('Tipo de cita', tipoCita) +
                createReadOnlySelect('Paciente', pacienteNombre) +
                (c.cancel_reason ? createReadOnlySelect('Motivo cancelación', c.cancel_reason) : '');
        };

        closeModal.addEventListener("click", () => modal.style.display = "none");
        window.addEventListener("click", (e) => {
            if (e.target === modal) modal.style.display = "none";
        });

        function renderCalendar(date) {
            calendarDaysDiv.innerHTML = "";
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = (firstDay.getDay() + 6) % 7;

            const datesWithCitas = new Set(initialCitas.map(c => c.fecha));

            monthYear.textContent = date.toLocaleString("es-ES", {
                month: "long",
                year: "numeric"
            }).toUpperCase();

            for (let i = 0; i < startDay; i++) {
                const emptyDiv = document.createElement("div");
                emptyDiv.classList.add("calendar-day", "empty-day");
                calendarDaysDiv.appendChild(emptyDiv);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const fullDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
                const dayDiv = document.createElement("div");
                dayDiv.classList.add("calendar-day");
                dayDiv.style.cursor = "pointer";

                if (datesWithCitas.has(fullDate)) {
                    dayDiv.classList.add("active-day");
                }

                dayDiv.innerHTML = `<div class="day-number">${day}</div>`;
                dayDiv.addEventListener("click", () => abrirModal(fullDate));
                calendarDaysDiv.appendChild(dayDiv);
            }
        }

        document.getElementById("prevMonth").addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        document.getElementById("nextMonth").addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

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
    }

    .calendar-day:hover {
        background: #e5e7eb;
        transform: scale(1.03);
    }

    .active-day {
        background-color: #a7f3d0 !important;
        border-color: #6ee7b7 !important;
        color: #065f46 !important;
        font-weight: 700 !important;
    }

    .empty-day {
        background: transparent;
        border: none;
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
    }

    .modal-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        color: #9ca3af;
        cursor: pointer;
        font-weight: bold;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #374151;
    }

    .modal-content h2 {
        font-size: 1.5rem;
        color: #10b981;
        margin-bottom: 25px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 10px;
    }

    #selectCita {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: #f9fafb;
        font-size: 1rem;
        color: #374151;
        cursor: pointer;
        margin-bottom: 15px;
    }

    #detalleCita {
        margin-top: 20px;
        padding: 15px;
        background-color: #f3f4f6;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .form-group-read-only {
        margin-bottom: 10px;
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

    .form-group-read-only select:disabled {
        opacity: 1;
        cursor: default;
    }
</style>

@endsection