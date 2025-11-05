@extends('layouts.app')

@section('content')
<div class="calendar-container">
    <div class="calendar-wrapper">
        <h1 class="calendar-title">Calendario de Disponibilidad</h1>

        <div class="doctor-select">
            <label>Buscar Doctor (por cédula):</label>
            <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                <input type="text" id="numeroDocumento" class="form-control" placeholder="Número de cédula" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                <button id="buscarDoctor" class="btn-buscar">Buscar</button>
            </div>
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
    let selectedDoctor = null;
    const calendarDaysDiv = document.getElementById("calendarDays");
    const monthYear = document.getElementById("monthYear");
    const buscarDoctor = document.getElementById("buscarDoctor");
    const numeroDocumento = document.getElementById("numeroDocumento");

    buscarDoctor.addEventListener("click", () => {
        const numero = numeroDocumento.value.trim();

        if (!numero) {
            alert("Ingrese el número de cédula del doctor");
            return;
        }

        fetch(`/buscar-doctor/${numero}`)
            .then(res => {
                if (!res.ok) throw new Error("No se encontró el doctor");
                return res.json();
            })
            .then(data => {
                if (data.id) {
                    selectedDoctor = data.id;
                    renderCalendar(currentDate);
                } else {
                    alert("Doctor no encontrado");
                }
            })
            .catch(() => alert("Error al buscar el doctor"));
    });

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

        if (!selectedDoctor) {
            for (let i = 0; i < startDay; i++) {
                const emptyDiv = document.createElement("div");
                emptyDiv.classList.add("calendar-day", "empty-day");
                calendarDaysDiv.appendChild(emptyDiv);
            }
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement("div");
                dayDiv.classList.add("calendar-day", "disponible");
                dayDiv.innerHTML = `<div class="day-number">${day}</div>`;
                calendarDaysDiv.appendChild(dayDiv);
            }
            return;
        }

        fetch(`/calendario-especialista/${selectedDoctor}/${year}-${String(month + 1).padStart(2, "0")}`)
            .then(res => res.json())
            .then(data => {
                const estadosDias = data;

                for (let i = 0; i < startDay; i++) {
                    const emptyDiv = document.createElement("div");
                    emptyDiv.classList.add("calendar-day", "empty-day");
                    calendarDaysDiv.appendChild(emptyDiv);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const fecha = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
                    const estado = estadosDias[fecha] || "Disponible";

                    const dayDiv = document.createElement("div");
                    dayDiv.classList.add("calendar-day", estado.toLowerCase());
                    dayDiv.innerHTML = `
                        <div class="day-number">${day}</div>
                        <select class="estado-select" data-fecha="${fecha}">
                            <option value="Disponible" ${estado === "Disponible" ? "selected" : ""}>Disponible</option>
                            <option value="Parcial" ${estado === "Parcial" ? "selected" : ""}>Parcial</option>
                            <option value="Bloqueado" ${estado === "Bloqueado" ? "selected" : ""}>Bloqueado</option>
                        </select>
                    `;
                    calendarDaysDiv.appendChild(dayDiv);
                }

                document.querySelectorAll(".estado-select").forEach(select => {
                    select.addEventListener("change", e => {
                        const fecha = e.target.dataset.fecha;
                        const nuevoEstado = e.target.value;

                        fetch("/calendario-especialista/update", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    doctor_id: selectedDoctor,
                                    fecha,
                                    estado: nuevoEstado
                                })
                            })
                            .then(res => res.json())
                            .then(() => renderCalendar(currentDate))
                            .catch(() => alert("Error al actualizar estado"));
                    });
                });
            });
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

    .doctor-select {
        text-align: center;
        margin-bottom: 20px;
    }

    .btn-buscar {
        background: #2563eb;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
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
        padding: 18px 0;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.2s;
        font-weight: 600;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .calendar-day.disponible {
        background: #e0f2fe;
        color: #0369a1;
    }

    .calendar-day.parcial {
        background: #fde68a;
        color: #92400e;
    }

    .calendar-day.bloqueado {
        background: #fecaca;
        color: #991b1b;
    }

    .estado-select {
        border-radius: 6px;
        padding: 4px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
    }
</style>
@endsection