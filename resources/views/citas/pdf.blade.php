<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cita #{{ $cita->id }}</title>
</head>
<body>
    <h2>Detalle de la Cita</h2>

    <div class="section">
        <h3>Información del Paciente</h3>
        <table>
            <tr>
                <td><strong>Nombre:</strong> {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</td>
                <td><strong>Documento:</strong> {{ $cita->paciente->documento }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> {{ $cita->paciente->telefono }}</td>
                <td><strong>Email:</strong> {{ $cita->paciente->email }}</td>
            </tr>
            <tr>
                <td><strong>Dirección:</strong> {{ $cita->paciente->direccion }}</td>
                <td><strong>Fecha de Nacimiento:</strong> {{ $cita->paciente->fecha_nacimiento }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Datos de la Cita</h3>
        <table>
            <tr>
                <td><strong>Número Fuente:</strong> {{ $cita->numero_fuente }}</td>
                <td><strong>Fecha:</strong> {{ $cita->fecha }}</td>
            </tr>
            <tr>
                <td><strong>Hora Inicio:</strong> {{ $cita->hora_inicio }}</td>
                <td><strong>Hora Fin:</strong> {{ $cita->hora_fin }}</td>
            </tr>
            <tr>
                <td><strong>Estado:</strong> {{ ucfirst($cita->estado) }}</td>
                <td><strong>Profesional:</strong> {{ $cita->admisiones->name }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Motivo de Consulta</h3>
        <p>{{ $cita->motivo_consulta ?? 'Sin registrar' }}</p>
    </div>

    <div class="section">
        <h3>Observaciones</h3>
        <p>{{ $cita->mensaje ?? 'Sin observaciones' }}</p>
    </div>

    @if($cita->estado === 'cancelada')
    <div class="section">
        <h3>Cancelación</h3>
        <p><strong>Motivo:</strong> {{ $cita->cancel_reason }}</p>
        <p><strong>Cancelada por:</strong> {{ optional($cita->cancelledBy)->name }}</p>
    </div>
    @endif

    <p style="text-align:center; margin-top:30px;">
        <small>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</small>
    </p>
</body>
</html>

<style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section h3 {
            background: #f2f2f2;
            padding: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table td {
            padding: 6px;
            border: 1px solid #ddd;
        }
    </style>