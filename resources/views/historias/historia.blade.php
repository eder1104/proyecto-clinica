@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Citas del Paciente') }}
    </h2>
@endsection

@section('content')
@php
    $paciente = $paciente ?? null;
    $citas = $citas ?? collect();
@endphp

<div class="container">
    <h3 class="titulo">
        @if($paciente)
            Citas de {{ $paciente->nombres ?? '' }} {{ $paciente->apellidos ?? '' }}
        @else
            Citas
        @endif
    </h3>

    @if($citas->isEmpty())
        <p class="sin-citas">Este paciente no tiene citas registradas.</p>
    @else
        <table class="tabla-citas">
            <thead>
                <tr>
                    <th>ID Cita</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                    <tr>
                        <td>{{ $cita->id }}</td>
                        <td>{{ $cita->fecha }}</td>
                        <td>{{ $cita->motivo_consulta ?? 'No especificado' }}</td>
                        <td>
                            @php
                                $estado = $cita->estado ?? 'desconocido';
                            @endphp
                            <span class="estado 
                                @if($estado == 'programada') pendiente 
                                @elseif($estado == 'finalizada') completada 
                                @elseif($estado == 'cancelada') cancelada 
                                @else otro @endif">
                                {{ ucfirst($estado) }}
                            </span>
                        </td>
                        <td>
                            @if ($cita->tipo_cita_id == 1)
                                <a href="{{ route('optometria.edit', $cita->id) }}" class="btn-historia">
                                    Editar Historia (Optometría)
                                </a>
                            @elseif ($cita->tipo_cita_id == 2)
                                <a href="{{ route('examenes.edit', $cita->id) }}" class="btn-historia">
                                    Editar Historia (Exámenes)
                                </a>
                            @else
                                <span class="text-gray-500">Sin plantilla</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="acciones">
        <a href="{{ route('historias.index') }}" class="btn-volver">
            Volver a Pacientes
        </a>
    </div>

    @if(session('success'))
        <div class="alerta exito">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alerta error">{{ session('error') }}</div>
    @endif
</div>

<style>
    .container {
        max-width: 900px;
        margin: 20px auto;
        padding: 25px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .titulo {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }

    .sin-citas {
        color: #555;
        text-align: center;
    }

    .tabla-citas {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .tabla-citas th,
    .tabla-citas td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .tabla-citas thead {
        background-color: #f5f5f5;
    }

    .estado {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
    }

    .pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .completada {
        background: #d4edda;
        color: #155724;
    }

    .cancelada {
        background: #f8d7da;
        color: #721c24;
    }

    .otro {
        background: #e2e3e5;
        color: #383d41;
    }

    .btn-historia {
        background-color: #28a745;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.2s ease-in-out;
    }

    .btn-historia:hover {
        background-color: #218838;
    }

    .btn-volver {
        background-color: #6c757d;
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        transition: background 0.2s ease-in-out;
    }

    .btn-volver:hover {
        background-color: #5a6268;
    }

    .alerta {
        padding: 12px;
        border-radius: 5px;
        margin-top: 15px;
    }

    .alerta.exito {
        background-color: #d4edda;
        color: #155724;
    }

    .alerta.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .acciones {
        margin-top: 20px;
        text-align: center;
    }
</style>
@endsection
