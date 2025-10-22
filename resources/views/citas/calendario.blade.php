<link rel="stylesheet" href="{{ asset('css/calendario.css') }}">

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

            <div id="calendarDays" class="calendar-days" data-citas="{{ $citas->pluck('fecha')->toJson() }}"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const calendarDays = document.getElementById("calendarDays");
        const monthYear = document.getElementById("monthYear");
        const citas = JSON.parse(calendarDays.dataset.citas);
        let currentDate = new Date();

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

                if (citas.includes(fullDate)) {
                    dayDiv.classList.add("active-day");
                }

                dayDiv.innerHTML = `<div class="day-number">${day}</div>`;
                dayDiv.addEventListener("click", () => {
                    alert(`Día seleccionado: ${fullDate}`);
                });

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
</style>
