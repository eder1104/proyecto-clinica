@extends('layouts.historia')
@section('title', isset($plantilla) ? 'Editar Exámenes de Optometría' : 'Registrar Exámenes de Optometría')

@section('content')

<div class="contenedor-principal">
    <div class="examenes-container">
        <div class="tabs">
            <button type="button" class="tab active" data-tab="examenes">Exámenes</button>
            <button type="button" class="tab" data-tab="diagnosticos">Diagnósticos</button>
        </div>

        <form
            action="{{ isset($plantilla) 
                ? route('examenes.update', ['cita' => $cita->id, 'examen' => $plantilla->id]) 
                : route('examenes.store', ['cita' => $cita->id]) }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @if(isset($plantilla))
            @method('PUT')
            @endif

            <div id="examenes" class="tab-content active">
                <input type="hidden" name="cita" value="{{ $cita->id }}">

                <div class="campo">
                    <label>Profesional</label>
                    <select name="profesional" class="form-control" required>
                        @forelse ($users as $user)
                        @if($user->role == 'admisiones')
                        <option value="{{ $user->id }}"
                            {{ isset($plantilla) && $plantilla->profesional == $user->id ? 'selected' : '' }}>
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </option>
                        @endif
                        @empty
                        <option value="">No hay usuarios de admisión</option>
                        @endforelse
                    </select>
                    @error('profesional')
                    <div class="invalid-feedback alerta">{{ $message }}</div>
                    @enderror
                </div>

                <div class="campo">
                    <label>Tipo de examen</label>
                    <select name="tipoExamen" required>
                        <option value="Agudeza Visual" {{ isset($plantilla) && $plantilla->tipoExamen=='Agudeza Visual' ? 'selected' : '' }}>Agudeza Visual</option>
                        <option value="Refracción" {{ isset($plantilla) && $plantilla->tipoExamen=='Refracción' ? 'selected' : '' }}>Refracción</option>
                        <option value="Fondo de Ojo" {{ isset($plantilla) && $plantilla->tipoExamen=='Fondo de Ojo' ? 'selected' : '' }}>Fondo de Ojo</option>
                        <option value="Biomicroscopía" {{ isset($plantilla) && $plantilla->tipoExamen=='Biomicroscopía' ? 'selected' : '' }}>Biomicroscopía</option>
                        <option value="Test de Visión Cromática" {{ isset($plantilla) && $plantilla->tipoExamen=='Test de Visión Cromática' ? 'selected' : '' }}>Test de Visión Cromática</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Ojo</label>
                    <select name="ojo" required>
                        <option value="Ojo Derecho" {{ isset($plantilla) && $plantilla->ojo=='Ojo Derecho' ? 'selected' : '' }}>Ojo Derecho</option>
                        <option value="Ojo Izquierdo" {{ isset($plantilla) && $plantilla->ojo=='Ojo Izquierdo' ? 'selected' : '' }}>Ojo Izquierdo</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Cargar archivos (PDF)</label>
                    <input type="file" name="archivo" accept="application/pdf">
                </div>

                <div class="campo">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="4">{{ isset($plantilla) ? $plantilla->observaciones : '' }}</textarea>
                </div>
            </div>

            <div id="diagnosticos" class="tab-content">
                <h3 class="titulo-seccion">Diagnósticos Oculares</h3>

                <div class="campo">
                    <label>Seleccione el ojo</label>
                    <select name="ojoDiag">
                        <option value="Ojo Derecho" {{ isset($plantilla) && $plantilla->ojoDiag=='Ojo Derecho' ? 'selected' : '' }}>Ojo Derecho</option>
                        <option value="Ojo Izquierdo" {{ isset($plantilla) && $plantilla->ojoDiag=='Ojo Izquierdo' ? 'selected' : '' }}>Ojo Izquierdo</option>
                    </select>
                </div>

                <table class="tabla-diagnosticos">
                    <thead>
                        <tr>
                            <th>Código CIEX</th>
                            <th>Diagnóstico</th>
                            <th>Ojo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="codigoCiex">
                                    <option value="H52.1 - Miopía" {{ isset($plantilla) && $plantilla->codigoCiex=='H52.1 - Miopía' ? 'selected' : '' }}>H52.1 - Miopía</option>
                                    <option value="H52.2 - Hipermetropía" {{ isset($plantilla) && $plantilla->codigoCiex=='H52.2 - Hipermetropía' ? 'selected' : '' }}>H52.2 - Hipermetropía</option>
                                    <option value="H52.3 - Astigmatismo" {{ isset($plantilla) && $plantilla->codigoCiex=='H52.3 - Astigmatismo' ? 'selected' : '' }}>H52.3 - Astigmatismo</option>
                                    <option value="H52.4 - Presbicia" {{ isset($plantilla) && $plantilla->codigoCiex=='H52.4 - Presbicia' ? 'selected' : '' }}>H52.4 - Presbicia</option>
                                    <option value="H53.0 - Ambliopía" {{ isset($plantilla) && $plantilla->codigoCiex=='H53.0 - Ambliopía' ? 'selected' : '' }}>H53.0 - Ambliopía</option>
                                    <option value="H50.0 - Estrabismo" {{ isset($plantilla) && $plantilla->codigoCiex=='H50.0 - Estrabismo' ? 'selected' : '' }}>H50.0 - Estrabismo</option>
                                    <option value="H53.1 - Deficiencia cromática" {{ isset($plantilla) && $plantilla->codigoCiex=='H53.1 - Deficiencia cromática' ? 'selected' : '' }}>H53.1 - Deficiencia cromática</option>
                                    <option value="H10.0 - Conjuntivitis" {{ isset($plantilla) && $plantilla->codigoCiex=='H10.0 - Conjuntivitis' ? 'selected' : '' }}>H10.0 - Conjuntivitis</option>
                                    <option value="H57.0 - Dolor ocular" {{ isset($plantilla) && $plantilla->codigoCiex=='H57.0 - Dolor ocular' ? 'selected' : '' }}>H57.0 - Dolor ocular</option>
                                    <option value="H54.0 - Ceguera total" {{ isset($plantilla) && $plantilla->codigoCiex=='H54.0 - Ceguera total' ? 'selected' : '' }}>H54.0 - Ceguera total</option>
                                </select>
                            </td>
                            <td><input type="text" name="diagnostico" value="{{ isset($plantilla) ? $plantilla->diagnostico : '' }}" placeholder="Diagnóstico específico"></td>
                            <td>
                                <select name="ojoDiag2">
                                    <option value="Ojo Derecho" {{ isset($plantilla) && $plantilla->ojoDiag2=='Ojo Derecho' ? 'selected' : '' }}>Ojo Derecho</option>
                                    <option value="Ojo Izquierdo" {{ isset($plantilla) && $plantilla->ojoDiag2=='Ojo Izquierdo' ? 'selected' : '' }}>Ojo Izquierdo</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="boton-guardar">
                <button type="submit">{{ isset($plantilla) ? 'Actualizar' : 'Guardar' }}</button>
            </div>
        </form>
    </div>
</div>

<style>
    .contenedor-principal {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 2rem;
    }

    .examenes-container {
        width: 100%;
        max-width: 750px;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
        padding: 2rem;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #d1d5db;
        margin-bottom: 1.5rem;
    }

    .tab {
        flex: 1;
        text-align: center;
        padding: .75rem 1rem;
        cursor: pointer;
        background: #f3f4f6;
        border: none;
        font-weight: 500;
        color: #374151;
        transition: .2s;
    }

    .tab.active {
        background: #2563eb;
        color: #fff;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .campo {
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
    }

    .campo label {
        font-weight: 500;
        color: #111827;
        margin-bottom: .5rem;
    }

    .campo select,
    .campo input[type=file],
    .campo textarea,
    .campo input[type=text] {
        border: 1px solid #d1d5db;
        border-radius: .5rem;
        padding: .5rem;
        font-size: 1rem;
        outline: none;
    }

    .campo select:focus,
    .campo textarea:focus,
    .campo input:focus {
        border-color: #2563eb;
    }

    .tabla-diagnosticos {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .tabla-diagnosticos th,
    .tabla-diagnosticos td {
        border: 1px solid #d1d5db;
        padding: .75rem;
        text-align: left;
    }

    .tabla-diagnosticos th {
        background: #f3f4f6;
        color: #111827;
        font-weight: 600;
    }

    .titulo-seccion {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e3a8a;
        margin-bottom: 1rem;
    }

    .boton-guardar {
        text-align: center;
        margin-top: 25px;
    }

    .boton-guardar button {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: #fff;
        padding: 12px 28px;
        border: none;
        border-radius: 8px;
        font-size: 17px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s, background 0.3s;
    }

    .boton-guardar button:hover {
        transform: scale(1.05);
        background: linear-gradient(135deg, #0066d1, #004999);
    }
</style>

<script>
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });
</script>
@endsection