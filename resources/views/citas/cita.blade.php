<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">
            Detalle de Cita Médica
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto">
            <div class="card-container">
                <div class="card-header">
                    <h1 class="text-2xl font-bold">Cita Médica General</h1>
                    <p class="text-sm">Registro detallado de atención médica</p>
                </div>

                <div class="p-8 space-y-8">

                    <div>
                        <h2 class="section-title">Datos del Paciente</h2>
                        <div class="grid grid-cols-2 gap-6 section-content">
                            <p><span class="font-medium">Nombre:</span> {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</p>
                            <p><span class="font-medium">Documento:</span> {{ $cita->paciente->documento }}</p>
                            <p><span class="font-medium">Edad:</span> {{ $cita->paciente->edad }}</p>
                            <p><span class="font-medium">Sexo:</span> {{ $cita->paciente->sexo }}</p>
                            <p><span class="font-medium">Teléfono:</span> {{ $cita->paciente->telefono }}</p>
                            <p><span class="font-medium">Dirección:</span> {{ $cita->paciente->direccion }}</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="section-title">Datos de la Cita</h2>
                        <div class="grid grid-cols-2 gap-6 section-content">
                            <p><span class="font-medium">Fecha:</span> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
                            <p><span class="font-medium">Hora:</span> {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</p>
                            <p><span class="font-medium">Estado:</span> {{ $cita->estado }}</p>
                            <p><span class="font-medium">Atendido por:</span> {{ $cita->admisiones->nombres }} {{ $cita->admisiones->apellidos }}</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="section-title">Motivo de Consulta</h2>
                        <form action="{{ route('citas.updateMotivo', $cita) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <p>
                                <textarea
                                    name="motivo_consulta"
                                    class="input-text">{{ old('motivo_consulta', $cita->motivo_consulta) }}</textarea>
                            </p>
                            <button type="submit" class="btn-submit">Guardar Motivo</button>
                        </form>

                    </div>

                    <div>
                        <h2 class="section-title">Antecedentes</h2>
                        <ul class="list-disc pl-6 section-content">
                            <li>{{ $cita->paciente->antecedentes ?? 'No registra antecedentes.' }}</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="section-title">Signos Vitales</h2>
                        <div class="grid grid-cols-3 gap-6 section-content">
                            <p><span class="font-medium">Tensión arterial:</span> {{ $cita->paciente->tension_arterial ?? '---' }}</p>
                            <p><span class="font-medium">Frecuencia cardiaca:</span> {{ $cita->paciente->frecuencia_cardiaca ?? '---' }}</p>
                            <p><span class="font-medium">Frecuencia respiratoria:</span> {{ $cita->paciente->frecuencia_respiratoria ?? '---' }}</p>
                            <p><span class="font-medium">Temperatura:</span> {{ $cita->paciente->temperatura ?? '---' }}</p>
                            <p><span class="font-medium">Saturación O₂:</span> {{ $cita->paciente->saturacion ?? '---' }}</p>
                            <p><span class="font-medium">Peso:</span> {{ $cita->paciente->peso ?? '---' }}</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="section-title">Examen Físico</h2>
                        <p class="section-content">
                            {{ $cita->paciente->examen_fisico ?? 'No registra examen físico.' }}
                        </p>
                    </div>

                    <div>
                        <h2 class="section-title">Diagnóstico</h2>
                        <p class="section-content">
                            {{ $cita->paciente->diagnostico ?? 'No registra diagnóstico.' }}
                        </p>
                    </div>

                    <div>
                        <h2 class="section-title">Conducta / Plan</h2>
                        <ul class="list-disc pl-6 section-content">
                            <li>{{ $cita->paciente->plan ?? 'No registra plan de tratamiento.' }}</li>
                        </ul>
                    </div>

                    <div class="pt-8 border-t">
                        <p class="text-sm text-gray-500">Fecha de registro: {{ \Carbon\Carbon::parse($cita->created_at)->format('d/m/Y - h:i A') }}</p>
                        <p class="mt-6 font-medium text-gray-700">_____________________________</p>
                        <p class="text-sm text-gray-600">{{ $cita->admisiones->name }} <br>Médico General</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .header-title {
        font-weight: 600;
        font-size: 1.25rem;
        color: #1f2937;
        line-height: 1.5rem;
    }

    .card-container {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #2563eb, #3b82f6);
        color: #fff;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.5rem;
    }

    .section-content {
        margin-top: 1rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .input-text {
        font-size: 0.875rem;
        color: #374151;
        width: 100%;
        height: 300px;
        padding: 1rem;
        text-align: left;
        resize: vertical;
    }

    .btn-submit {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        background: #2563eb;
        color: #fff;
        border-radius: 0.5rem;
    }
</style>