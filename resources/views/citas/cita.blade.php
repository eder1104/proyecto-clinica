<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto contenedor">
            <div class="card-container">
                <div class="card-header">
                    <h1 class="card-title">Cita De Optometria</h1>
                    <p class="card-subtitle">Registro de atencion unico por paciente</p>
                </div>
            </div>

            <div class="Box-Paciente">
                <button id="open">🔽</button>
                <div class="nombres">{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</div>
                <div>{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} Años</div>
                <div>{{ $cita->paciente->direccion }}</div>
                <div>{{ $cita->paciente->telefono }}</div>
                <div>{{ $cita->paciente->email }}</div>
            </div>

            <div id="datosPaciente" class="extra-info hidden">
                <p><strong>Estado Civil:</strong> {{ $cita->paciente->estado ?? '-' }}</p>
                <p><strong>Profesión:</strong> {{ $cita->paciente->profesion ?? '-' }}</p>
                <p><strong>Género:</strong> {{ $cita->paciente->sexo == 'M' ? 'Masculino' : 'Femenino' }}</p>
                <p><strong>Ciudad:</strong> {{ $cita->paciente->ciudad ?? '-' }}</p>

                <hr class="my-4">

                <p><strong>Fecha cita:</strong> {{ $cita->fecha }} {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
                <p><strong>Motivo de la consulta:</strong> {{ $cita->motivo_consulta ?? '-' }}</p>

                <p><strong>Presión arterial:</strong> {{ $cita->tension_arterial ?? '-' }}</p>
                <p><strong>Pulso (frecuencia cardiaca):</strong> {{ $cita->frecuencia_cardiaca ?? '-' }}</p>
                <p><strong>Frecuencia respiratoria:</strong> {{ $cita->frecuencia_respiratoria ?? '-' }}</p>
                <p><strong>Temperatura:</strong> {{ $cita->temperatura ?? '-' }}</p>
                <p><strong>Saturación O₂:</strong> {{ $cita->saturacion ?? '-' }}</p>
                <p><strong>Peso:</strong> {{ $cita->peso ?? '-' }}</p>

                <p><strong>Examen físico:</strong> {{ $cita->examen_fisico ?? '-' }}</p>
                <p><strong>Diagnóstico:</strong> {{ $cita->diagnostico ?? '-' }}</p>

                <p><strong>Estado de la cita:</strong> {{ $cita->estado ?? '-' }}</p>

                <p><strong>hora_inicio</strong> {{ $cita->hora_inicio ?? '-' }}</p>
                <p><strong>hora_fin</strong> {{ $cita->hora_fin ?? '-' }}</p>
                <p><strong>estado</strong> {{ $cita->estado ?? '-' }}</p>

            </div>
        </div>
    </div>

</x-app-layout>

<style>
    .nombres {
        display: flex;
        flex-direction: column;
        font-weight: 600;
        font-size: 1.5rem;
        color: #111827;
    }

    .Box-Paciente {
        display: grid;
        grid-template-columns: 5% 30% 5% 15% 15% 30%;
        align-items: center;
        width: 100%;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        padding: 1rem 1.25rem;
        background: #ffffff;
        margin-top: 1rem;
        gap: 1rem;
        font-size: 0.95rem;
        color: #374151;
    }

    .Box-Paciente button {
        justify-self: start;
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .card-container {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #2563eb, #3b82f6);
        color: #fff;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .contenedor {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #2563eb, #3b82f6);
        color: #fff;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .extra-info {
        background: #f9fafb;
        padding: 1rem;
        margin-top: 1rem;
        border-left: 4px solid #2563eb;
    }
</style>


<script>
    const btn = document.getElementById('open');
    const datos = document.getElementById('datosPaciente');

    btn.addEventListener('click', () => {
        datos.classList.toggle('hidden');
        btn.textContent = datos.classList.contains('hidden') ? '🔽' : '🔼';
    });
</script>