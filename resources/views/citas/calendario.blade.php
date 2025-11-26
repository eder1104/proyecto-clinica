@section('content')
<div class="calendar-container">
    <div class="calendar-wrapper">
        <h1 class="calendar-title">Calendario de Citas y Bloqueos</h1>

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

<div id="modalBloqueosParcialidades" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2 style="color:#10b981;">Detalles del <span id="modalFechaDisplayBloqueos"></span></h2>

        <div id="listadoCitas">
            <h3>Citas del Día</h3>
            <div id="citasList"></div>
        </div>

        <div id="listadoBloqueos">
            <h3>Bloqueos de Agenda</h3>
            <div id="bloqueosList"></div>
        </div>

        <div id="listadoParcialidades">
            <h3>Parcialidades</h3>
            <div id="parcialidadesList"></div>
        </div>

        <div id="noBloqueosAviso" style="display:none;" class="bloqueado-aviso">
            No se registraron bloqueos, parcialidades o citas para esta fecha.
        </div>
    </div>
</div>

<script>
    const calendarDaysDiv = document.getElementById("calendarDays");
    const monthYear = document.getElementById("monthYear");
    
    if (calendarDaysDiv) {
        const diasData = JSON.parse(calendarDaysDiv.dataset.dias || '[]');
        const citasUrlTemplate = calendarDaysDiv.dataset.citasUrl;
        let currentDate = new Date();
        let citasCached = {};

        const modalBloqueos = document.getElementById("modalBloqueosParcialidades");
        const modalFechaDisplayBloqueos = document.getElementById("modalFechaDisplayBloqueos");
        const bloqueosList = document.getElementById("bloqueosList");
        const parcialidadesList = document.getElementById("parcialidadesList");
        const citasList = document.getElementById("citasList");
        const noBloqueosAviso = document.getElementById("noBloqueosAviso");
        const closeModalBloqueos = modalBloqueos.querySelector(".modal-close");

        const diasInfo = {};
        diasData.forEach(d => diasInfo[d.fecha] = d);

        window.abrirModal = async (fecha) => {
            modalFechaDisplayBloqueos.textContent = fecha;

            bloqueosList.innerHTML = '';
            parcialidadesList.innerHTML = '';
            citasList.innerHTML = '';
            noBloqueosAviso.style.display = 'none';

            const infoDia = diasInfo[fecha];
            let hayRegistros = false;

            const citas = await fetchCitas(fecha);

            if (citas.length > 0) {
                citas.forEach(c => {
                    const item = document.createElement('div');
                    item.className = "card-bloqueo";
                    item.style.background = "#e0f2fe";
                    item.style.borderColor = "#38bdf8";

                    item.innerHTML = `
                        <p><strong>Paciente:</strong> ${c.paciente_nombre ?? 'Sin nombre'}</p>
                        <p><strong>Hora:</strong> ${c.hora.slice(0,5)}</p>
                        <p><strong>Doctor:</strong> ${c.doctor_nombre ?? 'Desconocido'}</p>
                        ${c.procedimiento ? `<p><strong>Procedimiento:</strong> ${c.procedimiento}</p>` : ""}
                    `;

                    citasList.appendChild(item);
                });
                hayRegistros = true;
            } else {
                citasList.innerHTML = `<p class="no-citas">No hay citas para esta fecha.</p>`;
            }

            if (infoDia?.bloqueos?.length) {
                infoDia.bloqueos.forEach((b) => {
                    const item = document.createElement('div');
                    item.className = "card-bloqueo bloque-total";
                    item.innerHTML = `
                        <p><strong>Doctor:</strong> ${b.doctor_nombre || 'Desconocido'}</p>
                        <p><strong>Bloqueo:</strong> Agenda Completa</p>
                        <p><strong>Motivo:</strong> ${b.motivo || 'Sin especificar'}</p>
                    `;
                    bloqueosList.appendChild(item);
                    hayRegistros = true;
                });
            } else {
                bloqueosList.innerHTML = '<p class="no-citas">No hay bloqueos de agenda completa registrados.</p>';
            }

            if (infoDia?.parcialidades?.length) {
                infoDia.parcialidades.forEach((p) => {
                    const item = document.createElement('div');
                    item.className = "card-bloqueo bloque-parcial";
                    item.innerHTML = `
                        <p><strong>Doctor:</strong> ${p.doctor_nombre || 'Desconocido'}</p>
                        <p><strong>Horario Bloqueado:</strong> ${p.hora_inicio.slice(0,5)} - ${p.hora_fin.slice(0,5)}</p>
                        <p><strong>Motivo:</strong> ${p.motivo || 'Parcialidad sin motivo'}</p>
                    `;
                    parcialidadesList.appendChild(item);
                    hayRegistros = true;
                });
            } else {
                parcialidadesList.innerHTML = '<p class="no-citas">No hay parcialidades registradas.</p>';
            }

            if (!hayRegistros) noBloqueosAviso.style.display = 'block';

            modalBloqueos.style.display = "flex";
        };

        const fetchCitas = async (fecha) => {
            try {
                if (citasCached[fecha]) return citasCached[fecha];
                const url = citasUrlTemplate.replace('__DATE__', fecha);
                const res = await fetch(url);
                if (!res.ok) throw new Error('Error fetching citas');
                const citas = await res.json();
                citasCached[fecha] = citas;
                return citas;
            } catch (error) {
                console.error(error);
                return [];
            }
        };

        closeModalBloqueos.onclick = () => modalBloqueos.style.display = "none";
        window.onclick = (e) => {
            if (e.target === modalBloqueos) modalBloqueos.style.display = "none";
        };

        const renderCalendar = (date) => {
            calendarDaysDiv.innerHTML = "";
            const year = date.getFullYear();
            const month = date.getMonth();
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

                let hasBloqueos = info?.bloqueos?.length > 0;
                let hasParcialidades = info?.parcialidades?.length > 0;

                let classes = [];
                let labels = [];

                if (hasParcialidades) {
                    classes.push("day-parcial");
                    labels.push("<div>PARCIALIDADES ACTIVAS</div>");
                }
                if (hasBloqueos) {
                    classes.push("day-bloqueado");
                    labels.push("<div>BLOQUEOS COMPLETOS</div>");
                }
                if (!hasBloqueos && !hasParcialidades) {
                    classes.push("day-activo");
                    labels.push("<div>DISPONIBLE</div>");
                }

                classes.forEach(c => dayDiv.classList.add(c));

                let doctorText = '';
                if (info?.doctores?.length) doctorText = `<br><small>${info.doctores.join(', ')}</small>`;

                if (hasBloqueos && hasParcialidades) {
                    dayDiv.classList.add("day-mixto");
                    dayDiv.innerHTML = `
        <div class="day-number">${d}</div>
        <div class="split-box">
            <div class="split-left">BLOQUEOS ACTIVOS</div>
            <div class="split-right">PARCIALIDADES ACTIVAS</div>
        </div>
        ${doctorText}
    `;
                } else {
                    dayDiv.innerHTML = `
        <div class="day-number">${d}</div>
        ${labels.join("")}
        ${doctorText}
    `;
                }


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
    }
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
        max-width: 1200px;
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
        background: #FF6F59;
        border: 4px solid red;
        border-radius: 10px;
        padding: 18px 0;
        font-weight: 600;
        position: relative;
        color: #4b5563;
        transition: all 0.2s ease;
        cursor: pointer;
        height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        z-index: 1;
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
        max-width: 500px;
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

    .modal-content h3 {
        font-size: 1.2rem;
        color: #374151;
        margin-top: 15px;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #f3f4f6;
    }

    #listadoBloqueos,
    #listadoParcialidades,
    #listadoCitas {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-top: 5px;
        margin-bottom: 15px;
    }

    .card-bloqueo {
        padding: 12px 15px;
        border-radius: 7px;
        font-size: 1rem;
        color: #222;
        font-weight: 500;
        border-left: 5px solid;
    }

    .bloque-total {
        background: #fdecea;
        border-color: #f87171;
    }

    .bloque-parcial {
        background: #fef9c3;
        border-color: #facc15;
    }

    .card-bloqueo p {
        margin: 0;
        line-height: 1.4;
    }

    .bloqueado-aviso {
        padding: 14px 18px;
        background-color: #e5e7eb;
        border: 1px solid #9ca3af;
        border-radius: 8px;
        color: #374151;
        font-size: 1em;
        text-align: center;
        font-weight: 500;
        margin-top: 10px;
    }

    .no-citas {
        padding: 5px 0;
        color: #6b7280;
        text-align: center;
        font-size: 0.9em;
    }

    .alert-bloqueo {
        padding: 12px 15px;
        background-color: #fef2f2;
        border: 1px solid #f87171;
        border-radius: 8px;
        color: #b91c1c;
        font-size: 0.95rem;
        font-weight: 500;
        text-align: center;
    }

    .day-mixto {
        background: #fffbea !important;
        border-color: #f59e0b !important;
        color: #78350f !important;
    }

    .split-box {
        width: 100%;
        height: 100%;
    }

    .split-left {
        flex: 1;
        background: #fecaca;
        display: flex;
        height: 65%;
        width: 100%;
        justify-content: center;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        position: relative;
        z-index: 99999 !important;
    }

    .split-right {
        flex: 1;
        background: #fef9c3;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        height: 65%;
        z-index: 99999;
    }
</style>
@endsection