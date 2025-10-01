<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto">
            <div class="card-container">
                <div class="card-header">
                    <h1 class="card-title">Cita De Optometria</h1>
                    <p class="card-subtitle">Registro de atencion unico por paciente</p>
                </div>
            </div>

            <div class="Box-Paciente">
                <button id="open">🔽</button>
                <div class="nombres">{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</div>
                <div>{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }}</div>
                <div>{{ $cita->paciente->direccion }}</div>
                <div>{{ $cita->paciente->telefono }}</div>
                <div>{{ $cita->paciente->email }}</div>
            </div>
        </div>
    </div>

    @include('ModalPaciente')
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
</style>

<script>
    const btn = document.getElementById('open');
    btn.addEventListener('click', () => {
        document.getElementById('modalPaciente').classList.remove('hidden');
    });
</script>
