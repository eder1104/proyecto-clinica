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
                data-citas="{{ $citas->toJson() }}"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const calendarDays = document.getElementById("calendarDays");
        const monthYear = document.getElementById("monthYear");
        const citas = JSON.parse(calendarDays.dataset.citas);
        let currentDate = new Date();

        const modal = document.createElement("div");
        modal.classList.add("modal-overlay");
        modal.style.display = "none";
        modal.innerHTML = `
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h2>Citas del <span id="modalFecha"></span></h2>
                <div id="modalCitas"></div>
            </div>
        `;
        document.body.appendChild(modal);

        const modalFecha = modal.querySelector("#modalFecha");
        const modalCitas = modal.querySelector("#modalCitas");
        const closeModal = modal.querySelector(".modal-close");

        function abrirModal(fecha) {
            modalFecha.textContent = fecha;

            const citasDia = citas.filter(c => c.fecha === fecha);

            if (citasDia.length > 0) {
                modalCitas.innerHTML = citasDia.map(c => `
            <div class="cita-item">
                <p><strong>Paciente:</strong> ${c.paciente ? `${c.paciente.nombres} ${c.paciente.apellidos}` : 'N/A'}</p>
                <p><strong>Tipo de cita:</strong> ${c.tipo_cita ? c.tipo_cita.nombre : 'Sin tipo'}</p>
                <p><strong>Hora inicio:</strong> ${c.hora_inicio || 'Sin hora'}</p>
                <p><strong>Hora fin:</strong> ${c.hora_fin || 'Sin hora'}</p>
                <p><strong>Estado:</strong> ${c.estado || 'Sin estado'}</p>
                ${c.cancel_reason ? `<p><strong>Motivo cancelación:</strong> ${c.cancel_reason}</p>` : ''}
            </div>
            <hr>
        `).join('');
            } else {
                modalCitas.innerHTML = `<p>No hay citas para esta fecha.</p>`;
            }

            modal.style.display = "flex";
        }


        closeModal.addEventListener("click", () => modal.style.display = "none");
        window.addEventListener("click", (e) => {
            if (e.target === modal) modal.style.display = "none";
        });

        function renderCalendar(date) {
            calendarDays.innerHTML = "";
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = (firstDay.getDay() + 6) % 7;

            monthYear.textContent = date.toLocaleString("es-ES", {
                month: "long",
                year: "numeric"
            }).toUpperCase();

            for (let i = 0; i < startDay; i++) {
                const emptyDiv = document.createElement("div");
                emptyDiv.classList.add("calendar-day", "empty-day");
                calendarDays.appendChild(emptyDiv);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const fullDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
                const dayDiv = document.createElement("div");
                dayDiv.classList.add("calendar-day");
                dayDiv.style.cursor = "pointer";

                if (citas.some(c => c.fecha === fullDate)) {
                    dayDiv.classList.add("active-day");
                }

                dayDiv.innerHTML = `<div class="day-number">${day}</div>`;
                dayDiv.addEventListener("click", () => abrirModal(fullDate));
                calendarDays.appendChild(dayDiv);
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
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: #fff;
        padding: 25px 35px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 400px;
        width: 90%;
        text-align: center;
    }

    .modal-close {
        position: absolute;
        top: 12px;
        right: 18px;
        font-size: 22px;
        color: #444;
        cursor: pointer;
    }

    .modal-content h2 {
        margin-top: 10px;
        font-size: 1.4rem;
        color: #065f46;
    }
</style>

@endsection