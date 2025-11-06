@extends('layouts.app')

@section('content')
<div class="calendar-container" data-doctor-id="{{ $doctor->id }}">
    <div class="calendar-wrapper">
        <h1 class="calendar-title">Agenda Medica Oftalmologica</h1>

        <div class="doctor-info-container">
            <h3 class="doctor-name">Dr. {{ $doctor->nombres }}</h3>
            <p class="doctor-doc">Documento: {{ $doctor->numero_documento }}</p>
            <a href="{{ route('doctor.agenda') }}" class="btn-volver">Volver a la lista</a>
        </div>

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

            <div id="calendarDays" class="calendar-days"></div>
        </div>
    </div>
</div>

<script>
    let currentDate = new Date();

    const calendarContainer = document.querySelector('.calendar-container');
    const selectedDoctor = calendarContainer.dataset.doctorId;

    const calendarDaysDiv = document.getElementById("calendarDays");
    const monthYear = document.getElementById("monthYear");

    function renderCalendar(date) {
        calendarDaysDiv.innerHTML = "";
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
            calendarDaysDiv.appendChild(emptyDiv);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const fecha = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("calendar-day", "disponible");

            dayDiv.innerHTML = `<div class="day-number">${day}</div>
    <select class="estado-select" data-fecha="${fecha}">
        <option value="Disponible" selected>Disponible</option>
        <option value="Parcial">Parcial</option>
        <option value="Bloqueado">Bloqueado</option>
    </select>`;
            calendarDaysDiv.appendChild(dayDiv);
        }

        const mesStr = `${year}-${String(month + 1).padStart(2, "0")}`;
        fetch(`/calendario-especialista/${selectedDoctor}/${mesStr}`)
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll(".estado-select").forEach(select => {
                    const fecha = select.dataset.fecha;
                    if (data[fecha]) {
                        select.value = data[fecha];
                        updateDayColor(select);

                        if (data[fecha] === 'Parcial') {
                            const dayDiv = select.closest(".calendar-day");
                            dayDiv.style.cursor = 'pointer';

                            dayDiv.addEventListener("click", (e) => {
                                if (e.target.classList.contains('estado-select')) {
                                    return;
                                }
                                window.location.href = `/vista-parcial/${selectedDoctor}/${fecha}`;
                            });
                        }
                    }
                });
            })
            .catch(() => console.error("Error al obtener la disponibilidad."));

        document.querySelectorAll(".estado-select").forEach(select => {
            select.addEventListener("input", e => {
                const fecha = e.target.dataset.fecha;
                const nuevoEstado = e.target.value;

                if (nuevoEstado === 'Parcial') {
                    window.location.href = `/vista-parcial/${selectedDoctor}/${fecha}`;
                    return;
                }

                updateDayColor(e.target);

                fetch("/calendario-especialista/update", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            doctor_id: selectedDoctor,
                            fecha,
                            estado: nuevoEstado
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(() => console.error("Error al actualizar el estado."));
            });
        });
    }

    function updateDayColor(selectElement) {
        const dayDiv = selectElement.closest(".calendar-day");
        dayDiv.classList.remove("disponible", "parcial", "bloqueado");
        dayDiv.classList.add(selectElement.value.toLowerCase());
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
</script>

<style>
    .calendar-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 50px 20px;
    }

    .calendar-wrapper {
        width: 90%;
        max-width: 900px;
    }

    .calendar-title {
        text-align: center;
        font-size: 2rem;
        margin-bottom: 25px;
        color: #374151;
    }

    .doctor-info-container {
        padding: 15px 20px;
        background-color: #f3f4f6;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }

    .doctor-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin: 0 0 5px 0;
    }

    .doctor-doc {
        color: #4b5563;
        margin: 0 0 15px 0;
    }

    .btn-volver {
        display: inline-block;
        text-decoration: none;
        background: #6b7280;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background-color 0.2s;
    }

    .btn-volver:hover {
        background: #4b5563;
    }

    .calendar-box {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f3f4f6;
        padding: 10px 20px;
        font-weight: 600;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        background: #f9fafb;
        padding: 10px 0;
        font-weight: 600;
        color: #4b5563;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        padding: 25px;
        text-align: center;
    }

    .calendar-day {
        position: relative;
        height: 100px;
        border-radius: 10px;
        transition: 0.2s;
        font-weight: 600;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .day-number {
        position: absolute;
        top: 8px;
        right: 10px;
        font-weight: bold;
    }

    .calendar-day.disponible {
        background: #d1fae5;
        color: #065f46;
    }

    .calendar-day.parcial {
        background: #fde68a;
        color: #92400e;
    }

    .calendar-day.bloqueado {
        background: #fecaca;
        color: #991b1b;
        border: 2px solid #dc2626;
    }

    .estado-select {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 6px;
        padding: 4px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
        background-color: #f9fafb;
        cursor: pointer;
    }

    .estado-select:disabled {
        background-color: #e5e7eb;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .estado-select option[value="Disponible"] {
        background-color: #22c55e;
        color: white;
    }

    .estado-select option[value="Bloqueado"] {
        background-color: #dc2626;
        color: white;
    }

    .estado-select option[value="Parcial"] {
        background-color: #facc15;
        color: black;
    }
</style>
@endsection