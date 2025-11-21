@extends('layouts.app')

@section('content')
<div class="agenda-container mt-5" id="agenda-completa">
    <div class="titulo-fecha text-center mb-5">
        <h2 class="fw-bold text-primary">Agenda del Día</h2>
        <h4 class="fecha">
            {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        </h4>
    </div>

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

    @if(isset($bloqueos) && count($bloqueos) > 0)
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
                            @elseif($cita->estado == 'finalizada') estado-finalizada
                            @else estado-default @endif">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </td>
                    <td>
                        @if($cita->tipo_cita_id == 1)
                        Optometría
                        @elseif($cita->tipo_cita_id == 2)
                        Exámenes
                        @elseif($cita->tipo_cita_id == 3)
                        Retina
                        @else
                        N/A
                        @endif

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="sin-citas">No hay citas registradas para hoy.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center mt-5">
        <button id="btn-imprimir" class="btn-imprimir" onclick="imprimirAgenda()">🖨️ Imprimir Agenda Completa</button>
    </div>
</div>

<script>
    function imprimirAgenda() {
        const boton = document.getElementById('btn-imprimir');

        boton.style.display = 'none';

        setTimeout(() => {
            boton.style.display = 'inline-block';
        }, 1000);

        const agendaHTML = document.getElementById('agenda-completa').outerHTML;
        const ventana = window.open('', '_blank');
        ventana.document.write(`
        <html>
        <head>
            <title>Agenda del Día</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
                h2 { text-align: center; color: #0d6efd; margin-bottom: 0; }
                h4 { text-align: center; color: #495057; font-weight: 500; margin-top: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 25px; font-size: 14px; }
                th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
                th { background-color: #0d6efd; color: white; text-transform: uppercase; letter-spacing: 0.5px; }
                tr:nth-child(even) { background-color: #f8f9fa; }
                .estado-programada { background-color: #0dcaf0; color: white; padding: 6px 14px; border-radius: 20px; }
                .estado-atendida { background-color: #198754; color: white; padding: 6px 14px; border-radius: 20px; }
                .estado-cancelada { background-color: #dc3545; color: white; padding: 6px 14px; border-radius: 20px; }
                .estado-default { background-color: #6c757d; color: white; padding: 6px 14px; border-radius: 20px; }
                .detalle-header, .bloqueos-header { text-align: center; background-color: #0d6efd; color: white; padding: 12px; font-weight: bold; border-radius: 6px; margin-top: 40px; }
            </style>
        </head>
        <body>
            ${agendaHTML}
        </body>
        </html>
    `);
        ventana.document.close();
        ventana.print();
    }
</script>

<style>
    .agenda-container {
        max-width: 1100px;
        margin: 0 auto;
        background-color: #ffffff;
        padding: 35px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .titulo-fecha h2 {
        font-weight: 700;
        color: #0d6efd;
        font-size: 30px;
        margin-bottom: 10px;
    }

    .titulo-fecha .fecha {
        font-size: 18px;
        color: #495057;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .resumen-container,
    .bloqueos-container,
    .detalle-container {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

    .resumen-table th,
    .resumen-table td,
    .bloqueos-table th,
    .bloqueos-table td,
    .detalle-table th,
    .detalle-table td {
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
        border-radius: 10px 10px 0 0;
    }

    .estado {
        padding: 6px 14px;
        border-radius: 20px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .estado-programada {
        background-color: #0dcaf0;
    }

    .estado-atendida {
        background-color: #198754;
    }

    .estado-cancelada {
        background-color: #dc3545;
    }
    
     .estado-finalizada{
        background-color: green;
    }

    .estado-default {
        background-color: #6c757d;
    }

    .sin-citas {
        text-align: center;
        padding: 20px;
        color: #6c757d;
        font-style: italic;
    }

    .btn-imprimir {
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
        font-size: 16px;
    }

    .btn-imprimir:hover {
        background-color: #0b5ed7;
        transform: scale(1.03);
    }
</style>
@endsection