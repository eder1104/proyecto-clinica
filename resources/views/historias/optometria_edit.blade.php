@php
use Illuminate\Support\Facades\Auth;

$isEdit = isset($plantilla) && $plantilla->exists;

$user = Auth::user();

$idOptometra = optional($user->doctor)->id;

$nombreCompletoOptometra = trim(($user->nombres ?? '') . ' ' . ($user->apellidos ?? ''));

if (empty($nombreCompletoOptometra) && $idOptometra) {
    $nombreCompletoOptometra = 'Doctor ID: ' . $idOptometra;
}

if (empty($nombreCompletoOptometra)) {
    $nombreCompletoOptometra = 'Usuario no identificado';
}
@endphp
@section('title', 'Consulta de Optometría')
@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="titulo">Plantilla de Consulta de Optometría</h2>

    <form action="{{ $isEdit ? route('optometria.update', ['cita' => $cita->id]) : route('optometria.store', ['cita' => $cita->id]) }}" method="POST">
        @csrf
        @if($isEdit)
        @method('PUT')
        @endif

        <div class="form-row">
            <div class="form-row" style="flex-grow: 1;">
                <div class="form-group small-input" style="flex-grow: 1;">
                    <label>Optómetra</label>
                    <p class="form-control-static" style="border: 1px solid #ccc; padding: 8px; border-radius: 4px; background-color: #f0f0f0; color: #333; font-weight: bold;">
                        {{ $nombreCompletoOptometra }}
                    </p>
                    
                    @error('optometra')
                    @enderror
                </div>
            </div>

            <div class="form-group checkbox-right">
                <label for="consulta_completa">Consulta Completa</label>
                <div>
                    <input type="checkbox" id="consulta_completa" name="consulta_completa" value="1"
                        {{ old('consulta_completa', $plantilla->consulta_completa ?? true) ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Anamnesis</label>
            <textarea name="anamnesis">{{ old('anamnesis', $plantilla->anamnesis ?? '') }}</textarea>
            @error('anamnesis')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

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

        <h3>Agudeza Visual</h3>
        <div class="SelectAgudeza grid-7-column">
            @php
            $valores = [];
            for ($i = -10.0; $i <= 10.0001; $i +=0.5) {
                $valores[]=number_format($i, 2, '.' , '' );
            }
            @endphp
            
            {{-- Columna 1 (Lejos OD) --}}
            <div class="AgudezaVisual">
                <label>Lejos OD</label>
                <div class="Box_Agudeza">
                    <select name="av_lejos_od" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_lejos_od', $plantilla->av_lejos_od ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_lejos_od"></div>
                </div>
            </div>

            {{-- Columna 2 (Intermedia OD) --}}
            <div class="AgudezaVisual">
                <label>Intermedia OD</label>
                <div class="Box_Agudeza">
                    <select name="av_intermedia_od" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_intermedia_od', $plantilla->av_intermedia_od ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_intermedia_od"></div>
                </div>
            </div>

            {{-- Columna 3 (Cerca OD) --}}
            <div class="AgudezaVisual">
                <label>Cerca OD</label>
                <div class="Box_Agudeza">
                    <select name="av_cerca_od" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_cerca_od', $plantilla->av_cerca_od ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_cerca_od"></div>
                </div>
            </div>

            {{-- Columna 4 (Etiqueta AVSC centrada) --}}
            <div class="AgudezaVisual" style="justify-content: center;">
                <label for="" class="SubTitle_op" style="margin-top: 0; font-size: 25px;">AVSC</label>
            </div>

            {{-- Columna 5 (Lejos OI) --}}
            <div class="AgudezaVisual">
                <label>Lejos OI</label>
                <div class="Box_Agudeza">
                    <select name="av_lejos_oi" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_lejos_oi', $plantilla->av_lejos_oi ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_lejos_oi"></div>
                </div>
            </div>

            {{-- Columna 6 (Intermedia OI) --}}
            <div class="AgudezaVisual">
                <label>Intermedia OI</label>
                <div class="Box_Agudeza">
                    <select name="av_intermedia_oi" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_intermedia_oi', $plantilla->av_intermedia_oi ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_intermedia_oi"></div>
                </div>
            </div>

            {{-- Columna 7 (Cerca OI) --}}
            <div class="AgudezaVisual">
                <label>Cerca OI</label>
                <div class="Box_Agudeza">
                    <select name="av_cerca_oi" class="form-control agudeza-select">
                        <option value=""></option>
                        @foreach($valores as $valor)
                            <option value="{{ $valor }}" {{ old('av_cerca_oi', $plantilla->av_cerca_oi ?? '') == $valor ? 'selected' : '' }}>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <div class="color-box" data-input="av_cerca_oi"></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Observaciones optometría</label>
            <textarea name="observaciones_optometria" placeholder="Escribe observaciones relevantes...">{{ old('observaciones_optometria', $plantilla->observaciones_optometria ?? '') }}</textarea>
            @error('observaciones_optometria')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <h3>Fórmula y lentes</h3>
        <div class="grid-2">
            <div>
                <label>Tipo de lente</label>
                <select name="tipo_lente" class="form-control">
                    <option value="">-- Selecciona tipo --</option>
                    <option value="Monofocal" {{ old('tipo_lente', $plantilla->tipo_lente ?? '') == 'Monofocal' ? 'selected' : '' }}>Monofocal</option>
                    <option value="Bifocal" {{ old('tipo_lente', $plantilla->tipo_lente ?? '') == 'Bifocal' ? 'selected' : '' }}>Bifocal</option>
                    <option value="Progresivo" {{ old('tipo_lente', $plantilla->tipo_lente ?? '') == 'Progresivo' ? 'selected' : '' }}>Progresivo</option>
                </select>
            </div>

            <div>
                <label>Especificaciones del lente</label>
                <input type="text" name="especificaciones_lente" placeholder="Ej: Antirreflejo, transición, etc." value="{{ old('especificaciones_lente', $plantilla->especificaciones_lente ?? '') }}">
            </div>

            <div>
                <label>Vigencia de fórmula</label>
                <input type="date" name="vigencia_formula" value="{{ old('vigencia_formula', $plantilla->vigencia_formula ?? '') }}">
            </div>

            <div>
                <label>Filtro</label>
                <select name="filtro" class="form-control">
                    <option value="">-- Selecciona filtro --</option>
                    <option value="Antirreflejo" {{ old('filtro', $plantilla->filtro ?? '') == 'Antirreflejo' ? 'selected' : '' }}>Antirreflejo</option>
                    <option value="Luz azul" {{ old('filtro', $plantilla->filtro ?? '') == 'Luz azul' ? 'selected' : '' }}>Luz azul</option>
                    <option value="Fotocromático" {{ old('filtro', $plantilla->filtro ?? '') == 'Fotocromático' ? 'selected' : '' }}>Fotocromático</option>
                </select>
            </div>

            <div>
                <label>Tiempo de formulación (meses)</label>
                <input type="number" min="0" name="tiempo_formulacion" value="{{ old('tiempo_formulacion', $plantilla->tiempo_formulacion ?? '') }}">
            </div>

            <div>
                <label>Distancia pupilar (mm)</label>
                <input type="number" step="0.5" min="0" name="distancia_pupilar" value="{{ old('distancia_pupilar', $plantilla->distancia_pupilar ?? '') }}">
            </div>

            <div>
                <label>Cantidad de lentes</label>
                <input type="number" min="1" name="cantidad" value="{{ old('cantidad', $plantilla->cantidad ?? '') }}">
            </div>
        </div>

        <h3>Diagnósticos</h3>
        <div class="form-group">
            <label>Diagnóstico principal</label>
            <select name="diagnostico_principal" class="form-control">
                <option value="">-- Selecciona diagnóstico --</option>
                <option value="Miopía" {{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') == 'Miopía' ? 'selected' : '' }}>Miopía</option>
                <option value="Hipermetropía" {{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') == 'Hipermetropía' ? 'selected' : '' }}>Hipermetropía</option>
                <option value="Astigmatismo" {{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') == 'Astigmatismo' ? 'selected' : '' }}>Astigmatismo</option>
                <option value="Presbicia" {{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') == 'Presbicia' ? 'selected' : '' }}>Presbicia</option>
            </select>
        </div>

        <div class="form-group">
            <label>Otros diagnósticos</label>
            <textarea name="otros_diagnosticos" placeholder="Escribe otros diagnósticos relevantes...">{{ old('otros_diagnosticos', $plantilla->otros_diagnosticos ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Datos adicionales</label>
            <textarea name="datos_adicionales" placeholder="Ejemplo: Antecedentes, notas del paciente...">{{ old('datos_adicionales', $plantilla->datos_adicionales ?? '') }}</textarea>
        </div>

        <div class="grid-2">
            <div>
                <label>Finalidad de la consulta</label>
                <select name="finalidad_consulta" class="form-control">
                    <option value="">-- Selecciona --</option>
                    <option value="Control" {{ old('finalidad_consulta', $plantilla->finalidad_consulta ?? '') == 'Control' ? 'selected' : '' }}>Control</option>
                    <option value="Diagnóstico" {{ old('finalidad_consulta', $plantilla->finalidad_consulta ?? '') == 'Diagnóstico' ? 'selected' : '' }}>Diagnóstico</option>
                    <option value="Formulación" {{ old('finalidad_consulta', $plantilla->finalidad_consulta ?? '') == 'Formulación' ? 'selected' : '' }}>Formulación</option>
                </select>
            </div>

            <div>
                <label>Causa / Motivo de atención</label>
                <select name="causa_motivo_atencion" class="form-control">
                    <option value="">-- Selecciona --</option>
                    <option value="Molestias visuales" {{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') == 'Molestias visuales' ? 'selected' : '' }}>Molestias visuales</option>
                    <option value="Control rutinario" {{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') == 'Control rutinario' ? 'selected' : '' }}>Control rutinario</option>
                    <option value="Cambio de fórmula" {{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') == 'Cambio de fórmula' ? 'selected' : '' }}>Cambio de fórmula</option>
                    <option value="Otros" {{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') == 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>
        </div>

        <div class="boton-guardar">
            <button type="submit">{{ $isEdit ? 'Actualizar' : 'Guardar' }}</button>
        </div>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const colors = ["green", "blue", "red", "transparent"];

        document.querySelectorAll(".color-box").forEach(box => {
            box.addEventListener("click", () => {
                let current = box.dataset.colorIndex ? parseInt(box.dataset.colorIndex) : 0;
                current = (current + 1) % colors.length;
                box.dataset.colorIndex = current;

                const newColor = colors[current];
                box.style.backgroundColor = newColor;

                const inputName = box.dataset.input;
                const field = document.querySelector(`[name="${inputName}"]`);

                if (field) {
                    field.style.color = (newColor === "transparent") ? "black" : newColor;

                    if (field.tagName.toLowerCase() === "select") {
                        field.style.color = (newColor === "transparent") ? "black" : newColor;
                    }
                }
            });
        });
    });
</script>


<style>
    .container {
        max-width: 900px;
        margin: 20px auto;
        padding: 25px;
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

    .SubTitle_op{
        /* Modificado para que se alinee en el centro de la fila de Agudeza Visual */
        display: flex;
        align-items: center;
        font-size: 25px;
        font-weight: bold;
        color: #444;
        justify-content: center;
        text-align: center;
        margin-top: 0;
        margin-bottom: 0;
    }

    .SelectAgudeza {
        /* Estructura Grid de 7 columnas para OD(3) - AVSC(1) - OI(3) */
        display: grid;
        grid-template-columns: repeat(3, 1fr) auto repeat(3, 1fr);
        gap: 5px 15px; /* Espacio vertical y horizontal */
        margin-bottom: 20px;
        align-items: flex-start;
    }
    
    /* Clase auxiliar para la nueva estructura de grid */
    .grid-7-column {
        display: grid;
        grid-template-columns: repeat(3, 1fr) auto repeat(3, 1fr);
        gap: 5px 15px;
        margin-bottom: 20px;
        align-items: flex-start;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
        font-size: 14px;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select.form-control {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
        transition: color 0.3s ease;
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

    .AgudezaVisual {
        display: flex;
        width: 100%;
        place-items: center;
        flex-direction: column;
        font-size: 15px;
        font-style: italic;
    }

    .Box_Agudeza{
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .color-box {
        width: 14px;
        height: 14px;
        border: 1px solid #666;
        border-radius: 2px;
        cursor: pointer;
        margin-left: 4px;
        transition: background-color 0.3s ease;
    }

    .agudeza-select {
        flex-grow: 1;
        width: auto !important;
        height: 40px;
        padding: 4px;
        text-align: center;
        font-weight: bold;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #333;
        transition: color 0.3s;
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

    button:hover {
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