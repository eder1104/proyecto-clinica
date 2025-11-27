@extends('layouts.app')

@section('content')
@php
    use App\Models\User;
    use Carbon\Carbon;

    $disponibilidadDoctores = [];
    $doctores = User::where('role', 'doctor')->get();

    foreach($doctores as $doc) {
        $nombreCompleto = trim($doc->nombres . ' ' . $doc->apellidos);
        $disponibilidadDoctores[$nombreCompleto] = 30;
    }

    $doctoresConParcial = [];

    foreach(($parciales ?? []) as $p) {
        $nombre = $p['doctor'] ?? '';
        
        if (isset($disponibilidadDoctores[$nombre])) {
            if (!in_array($nombre, $doctoresConParcial)) {
                $disponibilidadDoctores[$nombre] = 0;
                $doctoresConParcial[] = $nombre;
            }

            $inicio = Carbon::parse($p['hora_inicio']);
            $fin = Carbon::parse($p['hora_fin']);
            $minutos = abs($fin->diffInMinutes($inicio));
            $slotsCalculados = floor($minutos / 20);
            
            $disponibilidadDoctores[$nombre] += $slotsCalculados;
        }
    }

    foreach(($bloqueos ?? []) as $b) {
        $nombre = $b['doctor'] ?? '';
        
        if (isset($disponibilidadDoctores[$nombre])) {
            $inicio = Carbon::parse($b['hora_inicio']);
            $fin = Carbon::parse($b['hora_fin']);
            $minutos = abs($fin->diffInMinutes($inicio));
            $slotsDescontar = ceil($minutos / 20);
            
            $disponibilidadDoctores[$nombre] -= $slotsDescontar;
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
                    <th>Filtrar Bloqueos/Parciales (Disponibilidad)</th>
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
                        <select id="filtroDoctor" class="form-select" onchange="aplicarFiltrosYPaginacion()" style="width: 100%; padding: 5px; text-align: center; border: 1px solid #dee2e6; border-radius: 5px;">
                            <option value="">Ver todos los doctores...</option>
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
            <tbody id="tabla-bloqueos-body">
                @forelse(($bloqueos ?? collect()) as $b)
                <tr class="fila-bloqueo" data-doctor="{{ $b['doctor'] }}">
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
            <tbody id="tabla-parciales-body">
                @forelse(($parciales ?? collect()) as $p)
                <tr class="fila-parcial" data-doctor="{{ $p['doctor'] }}">
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
        <div class="detalle-header">Detalle de Citas del Día (Todas)</div>
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
            <tbody id="tabla-citas-body">
                @forelse($citas as $cita)
                @php
                    $nombreDocCita = $cita->doctor && $cita->doctor->user 
                        ? trim($cita->doctor->user->nombres . ' ' . $cita->doctor->user->apellidos) 
                        : '';
                @endphp
                <tr class="fila-cita">
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
                    <td colspan="6" class="sin-citas">No hay citas registradas para hoy.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div id="paginacion-container" class="paginacion-container">
            <button id="btn-prev" onclick="cambiarPagina(-1)">Anterior</button>
            <span id="info-pagina">Página 1</span>
            <button id="btn-next" onclick="cambiarPagina(1)">Siguiente</button>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mb-4">
        <button id="btn-imprimir" class="btn-imprimir" onclick="imprimirAgenda()">Imprimir Agenda</button>
    </div>
</div>

<script>
    let paginaActual = 1;
    const filasPorPagina = 5;
    let filasTodasCitas = [];

    document.addEventListener("DOMContentLoaded", function() {
        aplicarFiltrosYPaginacion();
    });

    function imprimirAgenda() {
        const boton = document.getElementById('btn-imprimir');
        const paginacion = document.getElementById('paginacion-container');
        
        const filas = document.querySelectorAll('.fila-cita');
        filas.forEach(fila => {
            fila.style.display = "table-row";
        });

        boton.style.display = 'none';
        paginacion.style.display = 'none';

        setTimeout(() => {
            window.print();
            boton.style.display = 'inline-block';
            paginacion.style.display = 'flex';
            renderizarTablaCitas(); 
        }, 300);
    }

    function aplicarFiltrosYPaginacion() {
        const select = document.getElementById('filtroDoctor');
        const doctorSeleccionado = select.value;

        filtrarSimple('.fila-bloqueo', doctorSeleccionado);
        filtrarSimple('.fila-parcial', doctorSeleccionado);

        const nodeListCitas = document.querySelectorAll('.fila-cita');
        filasTodasCitas = Array.from(nodeListCitas);

        nodeListCitas.forEach(fila => fila.style.display = 'none');

        paginaActual = 1;
        renderizarTablaCitas();
    }

    function filtrarSimple(selectorClase, doctorSeleccionado) {
        const filas = document.querySelectorAll(selectorClase);
        filas.forEach(fila => {
            const docFila = fila.getAttribute('data-doctor');
            if (doctorSeleccionado === "" || docFila === doctorSeleccionado) {
                fila.style.display = "table-row";
            } else {
                fila.style.display = "none";
            }
        });
    }

    function renderizarTablaCitas() {
        const totalPaginas = Math.ceil(filasTodasCitas.length / filasPorPagina);
        
        if (paginaActual < 1) paginaActual = 1;
        if (paginaActual > totalPaginas && totalPaginas > 0) paginaActual = totalPaginas;

        const inicio = (paginaActual - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        document.querySelectorAll('.fila-cita').forEach(f => f.style.display = 'none');

        const filasAmostrar = filasTodasCitas.slice(inicio, fin);
        filasAmostrar.forEach(fila => fila.style.display = 'table-row');

        document.getElementById('info-pagina').innerText = totalPaginas > 0 
            ? `Página ${paginaActual} de ${totalPaginas}` 
            : 'Sin resultados';

        document.getElementById('btn-prev').disabled = paginaActual === 1;
        document.getElementById('btn-next').disabled = paginaActual === totalPaginas || totalPaginas === 0;
    }

    function cambiarPagina(delta) {
        paginaActual += delta;
        renderizarTablaCitas();
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

    .estado-programada { background-color: #0dcaf0; }
    .estado-atendida { background-color: #198754; }
    .estado-cancelada { background-color: #dc3545; }
    .estado-finalizada { background-color: green; }
    .estado-modificada { background-color: #ffc107; }
    .estado-default { background-color: #6c757d; }

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

    .paginacion-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 15px;
        background-color: #fff;
        border-top: 1px solid #dee2e6;
        gap: 15px;
    }

    .paginacion-container button {
        padding: 5px 15px;
        border: 1px solid #0d6efd;
        background-color: white;
        color: #0d6efd;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
    }

    .paginacion-container button:hover:not(:disabled) {
        background-color: #0d6efd;
        color: white;
    }

    .paginacion-container button:disabled {
        border-color: #ccc;
        color: #ccc;
        cursor: not-allowed;
    }

    #info-pagina {
        font-weight: bold;
        color: #495057;
    }

    @media print {
        .btn-imprimir, .paginacion-container {
            display: none !important;
        }
    }
</style>
@endsection