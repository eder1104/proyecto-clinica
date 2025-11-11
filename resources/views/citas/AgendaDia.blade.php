@extends('layouts.app')

@section('content')
<div class="container mt-5">
<h2 class="mb-5 text-center fw-bold text-primary">
    Agenda del Día - {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
</h2>

    <div class="resumen-container mb-5">
        <table class="resumen-table">
            <thead>
                <tr>
                    <th>Total Horarios</th>
                    <th>Ocupados</th>
                    <th>Bloqueados</th>
                    <th>Programadas</th>
                    <th>Canceladas</th>
                    <th>Atendidas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $totalHorarios }}</td>
                    <td>{{ $ocupados }}</td>
                    <td>{{ $bloqueados }}</td>
                    <td>{{ $programadas }}</td>
                    <td>{{ $canceladas }}</td>
                    <td>{{ $atendidas }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(isset($bloqueos) && $bloqueos->count() > 0)
    <div class="bloqueos-container mb-5">
        <div class="bloqueos-header">Doctores que bloquearon su agenda</div>
        <table class="bloqueos-table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Fecha Bloqueo</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bloqueos as $bloqueo)
                    <tr>
                        <td>{{ $bloqueo['nombre_doctor'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($bloqueo['fecha'])->format('d/m/Y') }}</td>
                        <td>{{ $bloqueo['motivo'] ?? 'No especificado' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="detalle-container">
        <div class="detalle-header">Detalle de Citas del Día</div>
        <table class="detalle-table">
            <thead>
                <tr>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Paciente</th>
                    <th>Estado</th>
                    <th>Tipo de Examen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                        <td>{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</td>
                        <td>
                            <span class="estado 
                                @if($cita->estado == 'programada') estado-programada 
                                @elseif($cita->estado == 'atendida') estado-atendida 
                                @elseif($cita->estado == 'cancelada') estado-cancelada 
                                @else estado-default @endif">
                                {{ ucfirst($cita->estado) }}
                            </span>
                        </td>
                        <td>{{ $cita->tipo_cita->nombre ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="sin-citas">No hay citas registradas para hoy.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.container {
    max-width: 1100px;
    margin: 0 auto;
    background-color: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

h2 {
    font-weight: 700;
    color: #0d6efd;
    text-align: center;
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    padding: 15px;
    border-radius: 10px;
    font-size: 26px;
}

.resumen-container,
.bloqueos-container,
.detalle-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.resumen-table,
.bloqueos-table,
.detalle-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-size: 15px;
}

.resumen-table thead,
.bloqueos-table th,
.detalle-table th {
    background-color: #0d6efd;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.resumen-table th, .resumen-table td,
.bloqueos-table th, .bloqueos-table td,
.detalle-table th, .detalle-table td {
    padding: 14px;
    border-bottom: 1px solid #dee2e6;
}

.resumen-table tbody tr:nth-child(even),
.bloqueos-table tr:nth-child(even),
.detalle-table tr:nth-child(even) {
    background-color: #f8f9fa;
}

.bloqueos-header,
.detalle-header {
    background-color: #0d6efd;
    color: white;
    font-weight: bold;
    padding: 15px;
    text-align: center;
    font-size: 18px;
    letter-spacing: 0.5px;
}

.estado {
    padding: 6px 14px;
    border-radius: 20px;
    color: white;
    font-size: 13px;
    font-weight: 600;
}

.estado-programada { background-color: #0dcaf0; }
.estado-atendida { background-color: #198754; }
.estado-cancelada { background-color: #dc3545; }
.estado-default { background-color: #6c757d; }

.sin-citas {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

.estado {
    padding: 6px 14px !important;
    border-radius: 20px !important;
    color: white !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    display: inline-block !important;
}

.estado-programada { background-color: #0dcaf0 !important; }
.estado-atendida { background-color: #198754 !important; }
.estado-cancelada { background-color: #dc3545 !important; }
.estado-default { background-color: #6c757d !important; }
</style>


@endsection
