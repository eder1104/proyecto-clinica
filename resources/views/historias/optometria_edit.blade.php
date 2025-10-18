@php
$isEdit = isset($plantilla) && $plantilla->exists;
@endphp

@extends($isEdit ? 'layouts.app' : 'layouts.historia')
@section('title', $isEdit ? 'Editar Consulta de Optometría' : 'Consulta de Optometría')
@section('content')

<div class="container">
    <h2 class="titulo">{{ $isEdit ? 'Editar Plantilla de Consulta de Optometría' : 'Plantilla de Consulta de Optometría' }}</h2>

    <form action="{{ $isEdit ? route('optometria.update', ['cita' => $cita->id]) : route('optometria.store', ['cita' => $cita->id]) }}" method="POST">
        @csrf
        @if($isEdit)
        @method('PUT')
        @endif

        <!-- Optómetra y Consulta Completa -->
        <div class="form-row">
            <div class="form-group small-input">
                <label>Optómetra</label>
                <select name="optometra" class="form-control" required>
                    <option value="">-- Doctor a cargo de la consulta --</option>
                    @forelse ($users as $user)
                        <option value="{{ $user->id }}" {{ old('optometra', $plantilla->optometra ?? '') == $user->id ? 'selected' : '' }}>
                            {{ $user->nombres }} {{ $user->apellidos }}
                        </option>
                    @empty
                        <option value="">No hay optometras disponibles</option>
                    @endforelse
                </select>
                @error('optometra')
                    <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group checkbox-right">
                <label for="consulta_completa">Consulta Completa</label>
                <div>
                    <input type="checkbox" id="consulta_completa" name="consulta_completa" value="1"
                        {{ old('consulta_completa', $plantilla->consulta_completa ?? true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <!-- Anamnesis -->
        <div class="form-group">
            <label>Anamnesis</label>
            <textarea name="anamnesis">{{ old('anamnesis', $plantilla->anamnesis ?? '') }}</textarea>
            @error('anamnesis')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <!-- Alternativa deseada y Dominancia ocular -->
        <div class="grid-2">
            <div>
                <label>Alternativa deseada</label>
                <input type="text" name="alternativa_deseada" value="{{ old('alternativa_deseada', $plantilla->alternativa_deseada ?? '') }}">
                @error('alternativa_deseada')
                    <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label>Dominancia ocular</label>
                <input type="text" name="dominancia_ocular" value="{{ old('dominancia_ocular', $plantilla->dominancia_ocular ?? '') }}">
                @error('dominancia_ocular')
                    <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Agudeza Visual -->
        <h3>Agudeza Visual</h3>
        <div class="grid-2">
            @php
                $valores = [];
                for ($i = -10.0; $i <= 10.0001; $i += 0.5) {
                    $valores[] = number_format($i, 2, '.', '');
                }
            @endphp

            @foreach ([
                'av_lejos_od'=>'Lejos OD', 
                'av_lejos_oi'=>'Lejos OI', 
                'av_intermedia_od'=>'Intermedia OD', 
                'av_intermedia_oi'=>'Intermedia OI', 
                'av_cerca_od'=>'Cerca OD', 
                'av_cerca_oi'=>'Cerca OI'] as $campo => $label)
                <div>
                    <label>{{ $label }}</label>
                    <select name="{{ $campo }}" class="form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach ($valores as $valor)
                            <option value="{{ $valor }}" {{ old($campo, $plantilla->$campo ?? '') == $valor ? 'selected' : '' }}>
                                {{ $valor }}
                            </option>
                        @endforeach
                    </select>
                    @error($campo)
                        <div class="invalid-feedback alerta">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>

        <!-- Observaciones -->
        <div class="form-group">
            <label>Observaciones internas</label>
            <textarea name="observaciones_internas">{{ old('observaciones_internas', $plantilla->observaciones_internas ?? '') }}</textarea>
            @error('observaciones_internas')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Observaciones optometría</label>
            <textarea name="observaciones_optometria">{{ old('observaciones_optometria', $plantilla->observaciones_optometria ?? '') }}</textarea>
            @error('observaciones_optometria')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fórmula y lentes -->
        <h3>Fórmula y lentes</h3>
        <div class="grid-2">
            @foreach (['tipo_lente','especificaciones_lente','vigencia_formula','filtro','tiempo_formulacion','distancia_pupilar','cantidad'] as $campo)
                <div>
                    <label>{{ ucwords(str_replace('_',' ',$campo)) }}</label>
                    <input type="{{ $campo === 'cantidad' ? 'number' : 'text' }}" name="{{ $campo }}" value="{{ old($campo, $plantilla->$campo ?? '') }}">
                    @error($campo)
                        <div class="invalid-feedback alerta">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>

        <!-- Diagnósticos -->
        <h3>Diagnósticos</h3>
        <div class="form-group">
            <label>Diagnóstico principal</label>
            <input type="text" name="diagnostico_principal" value="{{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') }}">
            @error('diagnostico_principal')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Otros diagnósticos</label>
            <textarea name="otros_diagnosticos">{{ old('otros_diagnosticos', $plantilla->otros_diagnosticos ?? '') }}</textarea>
            @error('otros_diagnosticos')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Datos adicionales</label>
            <textarea name="datos_adicionales">{{ old('datos_adicionales', $plantilla->datos_adicionales ?? '') }}</textarea>
            @error('datos_adicionales')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Finalidad de la consulta</label>
            <input type="text" name="finalidad_consulta" value="{{ old('finalidad_consulta', $plantilla->finalidad_consulta ?? '') }}">
            @error('finalidad_consulta')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <!-- Causa / Motivo atención -->
        <div class="form-group">
            <label>Causa / Motivo atención</label>
            <input type="text" name="causa_motivo_atencion" value="{{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') }}">
            @error('causa_motivo_atencion')
                <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="boton-guardar">
            <button type="submit">{{ $isEdit ? 'Actualizar' : 'Guardar' }}</button>
        </div>
    </form>
</div>

<style>
    .container {
        max-width: 900px;
        margin: 20px auto;
        padding: 25px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .titulo {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    .form-group {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 14px;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
    }

    textarea {
        resize: vertical;
        height: 70px;
    }

    .small-input input[type="text"] {
        width: 160px;
    }

    .checkbox-right {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }

    h3 {
        margin: 25px 0 10px;
        font-size: 16px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
        color: #444;
        text-align: center;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 20px;
    }

    .grid-2 label {
        font-size: 13px;
        font-weight: bold;
        display: block;
        margin-top: 8px;
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
        background: linear-gradient(135deg, #0056b3, #004080);
        transform: scale(1.05);
    }

    .alerta {
        background: #f8d7da;
        color: #842029;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .alerta ul {
        margin: 0;
        padding-left: 20px;
    }

    .alerta-exito {
        background: #d1e7dd;
        color: #0f5132;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
</style>

@endsection
