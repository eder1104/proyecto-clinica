@extends('layouts.app')

@section('content')

<div class="container parcial-container">
    <div class="header">
        <h1>Configuración de Parcialidades</h1>
        <p>Doctor ID user: {{ $doctorId }}</p>
        <p>Día a Editar: <strong>{{ \Carbon\Carbon::parse($dia)->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</strong></p>
    </div>

    <form id="parcialidades-form" action="{{ route('citas.parcial.store') }}" method="POST">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
        <input type="hidden" name="fecha" value="{{ $dia }}">

        <div class="form-section">
            <h2>Añadir Nuevo Rango</h2>
            <div class="range-group">
                <div>
                    <label for="hora_inicio">Hora Inicio</label>
                    <select id="hora_inicio" name="hora_inicio" required>
                        @for ($h = 8; $h < 18; $h++)
                            @for ($m=0; $m < 60; $m +=20)
                            @php
                            $time=sprintf('%02d:%02d', $h, $m);
                            @endphp
                            <option value="{{ $time }}">{{ date("g:i A", strtotime($time)) }}</option>
                            @endfor
                            @endfor
                    </select>
                </div>
                <div>
                    <label for="hora_fin">Hora Fin</label>
                    <select id="hora_fin" name="hora_fin" required>
                        @for ($h = 8; $h <= 18; $h++)
                            @if ($h < 18)
                            @for ($m=0; $m < 60; $m +=20)
                            @php
                            $time=sprintf('%02d:%02d', $h, $m);
                            $selected=($h==13 && $m==0) ? 'selected' : '' ;
                            @endphp
                            <option value="{{ $time }}" {{ $selected }}>{{ date("g:i A", strtotime($time)) }}</option>
                            @endfor
                            @else
                            <option value="18:00">6:00 PM</option>
                            @endif
                            @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-add">Añadir</button>
            </div>
        </div>
    </form>

    <div class="list-section">
        <h2>Rangos Guardados</h2>
        <div class="parcialidades-lista">
            @forelse ($parcialidades_guardadas as $parcialidad)
            <div class="parcialidad-item">
                <span>{{ date("g:i A", strtotime($parcialidad->hora_inicio)) }} - {{ date("g:i A", strtotime($parcialidad->hora_fin)) }}</span>
                <form action="{{ route('citas.parcial.destroy', $parcialidad->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </div>
            @empty
            <p style="color: #718096;">No hay rangos de disponibilidad parcial definidos para este día.</p>
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

    .range-group>div {
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
        transition: background-color 0.3s;
        white-space: nowrap;
        display: inline-block;
        text-decoration: none;
        line-height: 1.4;
        box-sizing: border-box;
    }

    .btn-add {
        background-color: #10b981;
        color: white;
        height: 40px;
    }

    .btn-add:hover {
        background-color: #059669;
    }

    .parcialidades-lista {
        margin-top: 15px;
    }

    .parcialidad-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background-color: #f3f4f6;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .parcialidad-item span {
        font-weight: 600;
    }

    .parcialidad-item button {
        background: none;
        border: none;
        color: #ef4444;
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

    .btn-save:hover {
        background-color: #1d4ed8;
        color: white;
    }

    .btn-back {
        background-color: transparent;
        color: #555;
        padding: 10px 20px;
    }
</style>
@endsection