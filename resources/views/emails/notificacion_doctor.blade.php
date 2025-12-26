<!DOCTYPE html>
<html>
<head>
    <title>Nueva Cita Asignada</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hola Dr(a). {{ $cita->doctor->nombres ?? 'Especialista' }}</h2>
    
    <p>Se ha agendado una nueva cita en su calendario.</p>
    
    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px;">
        <p><strong>Paciente:</strong> {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</p>
        <p><strong>Fecha:</strong> {{ $cita->fecha }}</p>
        <p><strong>Hora:</strong> {{ $cita->hora_inicio }}</p>
        <p><strong>Motivo:</strong> {{ $cita->motivo_consulta ?? 'No especificado' }}</p>
    </div>

    <p>Puede ver los detalles ingresando al sistema.</p>
</body>
</html>