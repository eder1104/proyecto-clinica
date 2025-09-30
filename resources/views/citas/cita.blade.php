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
                    <h1 class="card-title">Cita Médica General</h1>
                    <p class="card-subtitle">Registro detallado de atención médica</p>
                </div>

                <div class="card-body">
                    <form action="{{ route('citas.update', $cita) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div>
                            <h2 class="section-title">Datos del Paciente</h2>
                            <div class="section-content grid-2">
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
                            <div class="section-content grid-2">
                                <p><span class="font-medium">Fecha:</span> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
                                <p><span class="font-medium">Hora:</span> {{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</p>
                                <p><span class="font-medium">Estado:</span> {{ $cita->estado }}</p>
                                <p><span class="font-medium">Atendido por:</span> {{ $cita->admisiones->nombres }} {{ $cita->admisiones->apellidos }}</p>
                            </div>
                        </div>

                        <div>
                            <h2 class="section-title">Motivo de Consulta</h2>
                            <textarea name="motivo_consulta" class="input-text">{{ old('motivo_consulta', $cita->motivo_consulta) }}</textarea>
                        </div>

                        <div>
                            <h2 class="section-title">Antecedentes</h2>
                            <ul class="section-content list">
                                <li>{{ $cita->paciente->antecedentes ?? 'No registra antecedentes.' }}</li>
                            </ul>
                        </div>

                        <div>
                            <h2 class="section-title">Signos Vitales</h2>
                            <div class="section-content grid-3">
                                <p><span class="font-medium">Tensión arterial:</span> <input type="text" name="tension_arterial" class="input-field"></p>
                                <p><span class="font-medium">Frecuencia cardiaca:</span> <input type="text" name="frecuencia_cardiaca" class="input-field"></p>
                                <p><span class="font-medium">Frecuencia respiratoria:</span> <input type="text" name="frecuencia_respiratoria" class="input-field"></p>
                                <p><span class="font-medium">Temperatura:</span> <input type="text" name="temperatura" class="input-field"></p>
                                <p><span class="font-medium">Saturación O₂:</span> <input type="text" name="saturacion" class="input-field"></p>
                                <p><span class="font-medium">Peso:</span> <input type="text" name="peso" class="input-field"></p>
                            </div>
                        </div>

                        <div>
                            <h2 class="section-title">Examen Físico</h2>
                            <textarea name="examen_fisico" class="input-text">{{ old('examen_fisico', $cita->paciente->examen_fisico ?? '') }}</textarea>
                        </div>

                        <div>
                            <h2 class="section-title">Diagnóstico</h2>
                            <textarea name="diagnostico" class="input-text">{{ old('diagnostico', $cita->paciente->diagnostico ?? '') }}</textarea>
                        </div>

                        <div>
                            <h2 class="section-title">Conducta / Plan</h2>
                            <textarea name="plan" class="input-text">{{ old('plan', $cita->paciente->plan ?? '') }}</textarea>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn-submit">Guardar Cita Completa</button>
                        </div>
                    </form>

                    <div class="registro-footer">
                        <p>Fecha de registro: {{ \Carbon\Carbon::parse($cita->created_at)->format('d/m/Y - h:i A') }}</p>
                        <p class="firma">_____________________________</p>
                        <p>{{ $cita->admisiones->name }} <br>Médico General</p>
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
        background: #fff;
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
    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .card-subtitle {
        font-size: 0.875rem;
    }
    .card-body {
        padding: 2rem;
    }
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .section-content {
        font-size: 0.875rem;
        color: #4b5563;
        margin-top: 0.5rem;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .list {
        list-style-type: disc;
        padding-left: 1.5rem;
    }
    .input-text {
        font-size: 0.875rem;
        color: #374151;
        width: 100%;
        min-height: 150px;
        padding: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        resize: vertical;
        margin-top: 0.5rem;
    }
    .input-field {
        width: 100%;
        padding: 0.25rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #374151;
    }
    .btn-submit {
        padding: 0.75rem 1.5rem;
        background: #2563eb;
        color: #fff;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .form-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 2rem;
        border-top: 1px solid #e5e7eb;
        padding-top: 1.5rem;
    }
    .registro-footer {
        margin-top: 2rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    .registro-footer .firma {
        margin: 1rem 0;
        font-weight: 500;
        color: #374151;
    }
</style>
