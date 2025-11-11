@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-5 text-center fw-bold text-primary">📅 Agenda del Día - {{ $fecha }}</h2>

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
.container { max-width: 1100px; margin: 0 auto; }
.resumen-container { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.resumen-table { width: 100%; border-collapse: collapse; text-align: center; font-size: 16px; }
.resumen-table thead { background-color: #007bff; color: white; }
.resumen-table th, .resumen-table td { padding: 15px; }
.resumen-table tbody tr td:nth-child(1) { color: #0d6efd; font-weight: 600; }
.resumen-table tbody tr td:nth-child(2) { color: #dc3545; font-weight: 600; }
.resumen-table tbody tr td:nth-child(3) { color: #ffc107; font-weight: 600; }
.resumen-table tbody tr td:nth-child(4) { color: #0dcaf0; font-weight: 600; }
.resumen-table tbody tr td:nth-child(5) { color: #6c757d; font-weight: 600; }
.resumen-table tbody tr td:nth-child(6) { color: #198754; font-weight: 600; }

.bloqueos-container { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 30px; }
.bloqueos-header { background-color: #ffc107; color: #000; font-weight: bold; padding: 15px; text-align: center; font-size: 18px; }
.bloqueos-table { width: 100%; border-collapse: collapse; font-size: 15px; }
.bloqueos-table th { background-color: #fff3cd; text-align: center; padding: 12px; }
.bloqueos-table td { padding: 10px; text-align: center; border-bottom: 1px solid #dee2e6; }
.bloqueos-table tr:nth-child(even) { background-color: #fff8e1; }

.detalle-container { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.detalle-header { background-color: #0d6efd; color: white; font-weight: bold; padding: 15px; text-align: center; font-size: 18px; }
.detalle-table { width: 100%; border-collapse: collapse; font-size: 15px; }
.detalle-table th { background-color: #e9ecef; text-align: center; padding: 12px; }
.detalle-table td { padding: 10px; text-align: center; border-bottom: 1px solid #dee2e6; }
.detalle-table tr:nth-child(even) { background-color: #f8f9fa; }
.estado { padding: 5px 12px; border-radius: 12px; color: white; font-size: 14px; font-weight: 600; }
.estado-programada { background-color: #0dcaf0; }
.estado-atendida { background-color: #198754; }
.estado-cancelada { background-color: #dc3545; }
.estado-default { background-color: #6c757d; }
.sin-citas { text-align: center; padding: 20px; color: #6c757d; font-style: italic; }
</style>
@endsection
