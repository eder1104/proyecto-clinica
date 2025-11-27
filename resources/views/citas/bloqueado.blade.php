@extends('layouts.app')

@section('content')

<div class="container parcial-container">
    <div class="header">
        <h1>Configuración de Bloqueos</h1>
        <p>Doctor ID: {{ $doctorId }}</p>
        <p>Día a Editar: <strong>{{ \Carbon\Carbon::parse($dia)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</strong></p>
    </div>

    <form id="bloqueados-form" action="{{ route('citas.bloqueado.store') }}" method="POST">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
        <input type="hidden" name="fecha" value="{{ $dia }}">

        <div class="form-section">
            <h2>Añadir Bloqueo</h2>
            <div class="range-group">
                @php
                    $inicio = strtotime("08:00");
                    $fin = strtotime("18:00");
                    $intervalo = 20 * 60;
                @endphp

                <div>
                    <label for="hora_inicio">Hora Inicio</label>
                    <select id="hora_inicio" name="hora_inicio" required style="max-height: 200px; overflow-y: auto;">
                        @for ($t = $inicio; $t < $fin; $t += $intervalo)
                            <option value="{{ date('H:i', $t) }}">{{ date('g:i A', $t) }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="hora_fin">Hora Fin</label>
                    <select id="hora_fin" name="hora_fin" required style="max-height: 200px; overflow-y: auto;">
                        @for ($t = $inicio + $intervalo; $t <= $fin; $t += $intervalo)
                            <option value="{{ date('H:i', $t) }}">{{ date('g:i A', $t) }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="btn btn-add">Añadir</button>
            </div>
        </div>
    </form>

    <div class="list-section">
        <h2>Bloqueos Guardados</h2>

        <div class="parcialidades-lista">
            @forelse ($bloqueos_guardados as $bloqueo)
                <div class="parcialidad-item">
                    <span>{{ date("g:i A", strtotime($bloqueo->hora_inicio)) }} - {{ date("g:i A", strtotime($bloqueo->hora_fin)) }}</span>

                    <form action="{{ route('citas.bloqueado.destroy', ['doctorId' => $doctorId, 'fecha' => $dia, 'id' => $bloqueo->id]) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </div>
            @empty
                <p style="color: #718096;">No hay bloqueos registrados para este día.</p>
            @endforelse
        </div>
    </div>

    <div class="footer">
        <a href="{{ route('citas.CalendarioEspecialista', ['doctorId' => $doctorId]) }}" class="btn btn-back">Volver al Calendario</a>
        <a href="{{ route('citas.CalendarioEspecialista', ['doctorId' => $doctorId]) }}" class="btn btn-save">Finalizar y Guardar</a>
    </div>
</div>

<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #f4f7f6 !important;
        color: #333;
    }
    .container.parcial-container {
        max-width: 700px;
        margin: 20px auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .header {
        padding: 25px 30px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e6e9eb;
    }
    .header h1 {
        margin: 0;
        font-size: 22px;
        color: #1a202c;
    }
    .header p {
        margin: 5px 0 0;
        font-size: 15px;
        color: #718096;
    }
    .form-section,
    .list-section {
        padding: 30px;
        border-bottom: 1px solid #e6e9eb;
    }
    .list-section {
        border-bottom: none;
    }
    .range-group {
        display: flex;
        gap: 20px;
        align-items: flex-end;
    }
    .range-group > div {
        flex: 1;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-sizing: border-box;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        line-height: 1.4;
        box-sizing: border-box;
    }
    .btn-add {
        background-color: #ef4444;
        color: white;
        height: 40px;
    }
    .parcialidades-lista {
        margin-top: 15px;
    }
    .parcialidad-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background-color: #fee2e2;
        border-radius: 6px;
        margin-bottom: 10px;
        color: #991b1b;
        font-weight: 600;
    }
    .parcialidad-item button {
        background: none;
        border: none;
        color: #dc2626;
        cursor: pointer;
        font-weight: 600;
    }
    .footer {
        padding: 20px 30px;
        background-color: #f9fafb;
        border-top: 1px solid #e6e9eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-save {
        background-color: #2563eb;
        color: white;
    }
    .btn-back {
        background-color: transparent;
        color: #555;
        padding: 10px 20px;
    }
</style>

@endsection
