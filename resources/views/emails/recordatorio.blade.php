<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Cita</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h1>Hola, {{ $recordatorio->cita->paciente->nombres }}</h1>
    <p>Le recordamos su cita médica programada.</p>
    <ul>
        <li><strong>Fecha:</strong> {{ $recordatorio->cita->fecha }}</li>
        <li><strong>Hora:</strong> {{ $recordatorio->cita->hora_inicio }}</li>
        <li><strong>Doctor:</strong> {{ $recordatorio->cita->doctor->nombres ?? 'Por asignar' }}</li>
    </ul>
    <p>Por favor, llegue 15 minutos antes.</p>
</body>
</html>