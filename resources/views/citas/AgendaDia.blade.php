@extends('layouts.app')

@section('content')
@php
    use App\Models\User;
    use Carbon\Carbon;

    $citasParaJs = $citas->map(function($c) {
        return [
            'hora_inicio' => $c->hora_inicio,
            'hora_fin' => $c->hora_fin,
        ];
    });

    $disponibilidadDoctores = [];
    $doctores = User::where('role', 'doctor')->get();

    foreach($doctores as $doc) {
        $nombreCompleto = trim($doc->nombres . ' ' . $doc->apellidos);
        $disponibilidadDoctores[$nombreCompleto] = 30;
    }

    foreach($citas as $cita) {
        if ($cita->doctor && $cita->doctor->user) {
            $nombre = trim($cita->doctor->user->nombres . ' ' . $cita->doctor->user->apellidos);
            if (isset($disponibilidadDoctores[$nombre])) {
                $inicio = Carbon::parse($cita->hora_inicio);
                $fin = Carbon::parse($cita->hora_fin);
                $slotsUsados = ceil($fin->diffInMinutes($inicio) / 20);
                $disponibilidadDoctores[$nombre] -= $slotsUsados;
            }
        }
    }

    $bloqueosYParciales = array_merge($bloqueos->toArray() ?? [], $parciales->toArray() ?? []);
    
    foreach($bloqueosYParciales as $item) {
        $nombre = $item['doctor'] ?? '';
        if (isset($disponibilidadDoctores[$nombre])) {
            $inicio = Carbon::parse($item['hora_inicio']);
            $fin = Carbon::parse($item['hora_fin']);
            $slotsUsados = ceil($fin->diffInMinutes($inicio) / 20);
            $disponibilidadDoctores[$nombre] -= $slotsUsados;
        }
    }
@endphp

<div class="agenda-container mt-5" id="agenda-completa">
    <div class="titulo-fecha text-center mb-5">
        <h2 class="fw-bold text-primary">Agenda del Día</h2>
        <h4 class="fecha">
            {{ Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        </h4>
    </div>

    <div class="resumen-container mb-5">
        <table class="resumen-table">
            <thead>
                <tr>
                    <th>Total Horarios (Disponibilidad)</th>
                    <th>Ocupados</th>
                    <th>Bloqueados</th>
                    <th>Programadas</th>
                    <th>Canceladas</th>
                    <th>Atendidas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select class="form-select" style="width: 100%; padding: 5px; text-align: center; border: 1px solid #dee2e6; border-radius: 5px;">
                            <option selected disabled>Ver disponibilidad...</option>
                            @foreach($disponibilidadDoctores as $nombre => $slots)
                                @php $slots = max(0, $slots); @endphp
                                <option value="{{ $nombre }}">
                                    {{ $nombre }} - {{ $slots }} Slots Disp.
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>{{ $ocupados }}</td>
                    <td>{{ $bloqueados }}</td>
                    <td>{{ $programadas }}</td>
                    <td>{{ $canceladas }}</td>
                    <td>{{ $finalizada }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bloqueos-container mb-5">
        <div class="bloqueos-header">Bloqueos</div>
        <table class="bloqueos-table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Fecha Bloqueo</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($bloqueos ?? collect()) as $b)
                <tr>
                    <td>{{ $b['doctor'] }}</td>
                    <td>{{ Carbon::parse($b['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ $b['hora_inicio'] }}</td>
                    <td>{{ $b['hora_fin'] }}</td>
                    <td>{{ $b['motivo'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="sin-citas">No hay bloqueos completos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bloqueos-container mb-5">
        <div class="bloqueos-header">Parciales</div>
        <table class="bloqueos-table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($parciales ?? collect()) as $p)
                <tr>
                    <td>{{ $p['doctor'] }}</td>
                    <td>{{ Carbon::parse($p['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ $p['hora_inicio'] }}</td>
                    <td>{{ $p['hora_fin'] }}</td>
                    <td>{{ $p['motivo'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="sin-citas">No hay bloqueos parciales.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                    <td>{{ Carbon::parse($cita->hora_inicio)->format('H:i') }}</td>
                    <td>{{ Carbon::parse($cita->hora_fin)->format('H:i') }}</td>
                    <td>{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</td>
                    <td>
                        <span class="estado
                                @if($cita->estado == 'programada') estado-programada
                                @elseif($cita->estado == 'atendida') estado-atendida
                                @elseif($cita->estado == 'cancelada') estado-cancelada
                                @elseif($cita->estado == 'finalizada') estado-finalizada
                                @elseif($cita->estado == 'modificada') estado-modificada
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
    <div class="d-flex justify-content-end mb-4">
        <button id="btn-imprimir" class="btn-imprimir" onclick="imprimirAgenda()">Imprimir Agenda</button>
    </div>
</div>

<script>
    function imprimirAgenda() {
        const boton = document.getElementById('btn-imprimir');
        boton.style.display = 'none';

        setTimeout(() => {
            window.print();
            boton.style.display = 'inline-block';
        }, 300);
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

    .estado-finalizada {
        background-color: green;
    }

    .estado-modificada {
        background-color: #ffc107;
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

    @media print {
        .btn-imprimir {
            display: none !important;
        }
    }
</style>
@endsection