@section('content')
<div class="container">
    <h2 class="titulo">Plantilla de Consulta de Optometría</h2>

    <form action="{{route('optometria.store', ['cita' => $cita->id]) }}" method="POST">
        @csrf

        <input type="hidden" name="id" value="{{ $plantilla->id ?? $id ?? '' }}">

        <div class="form-row">
            <div class="form-group small-input">
                <label>Optómetra</label>
                <select name="optometra" class="form-control">
                    <option value="">-- Doctor a cargo de la consulta --</option>
                    @forelse ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ old('optometra', $plantilla->optometra ?? '') == $user->id ? 'selected' : '' }}>
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
        <div class="grid-2">
            <div><label>Lejos OD</label><input type="text" name="av_lejos_od" value="{{ old('av_lejos_od', $plantilla->av_lejos_od ?? '') }}">
                @error('av_lejos_od')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Lejos OI</label><input type="text" name="av_lejos_oi" value="{{ old('av_lejos_oi', $plantilla->av_lejos_oi ?? '') }}">
                @error('av_lejos_oi')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Intermedia OD</label><input type="text" name="av_intermedia_od" value="{{ old('av_intermedia_od', $plantilla->av_intermedia_od ?? '') }}">
                @error('av_intermedia_od')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Intermedia OI</label><input type="text" name="av_intermedia_oi" value="{{ old('av_intermedia_oi', $plantilla->av_intermedia_oi ?? '') }}">
                @error('av_intermedia_oi')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Cerca OD</label><input type="text" name="av_cerca_od" value="{{ old('av_cerca_od', $plantilla->av_cerca_od ?? '') }}">
                @error('av_cerca_od')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Cerca OI</label><input type="text" name="av_cerca_oi" value="{{ old('av_cerca_oi', $plantilla->av_cerca_oi ?? '') }}">
                @error('av_cerca_oi')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group"><label>Observaciones internas</label><textarea name="observaciones_internas">{{ old('observaciones_internas', $plantilla->observaciones_internas ?? '') }}</textarea>
            @error('observaciones_internas')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group"><label>Observaciones optometría</label><textarea name="observaciones_optometria">{{ old('observaciones_optometria', $plantilla->observaciones_optometria ?? '') }}</textarea>
            @error('observaciones_optometria')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <h3>Fórmula y lentes</h3>
        <div class="grid-2">
            <div><label>Tipo lente</label><input type="text" name="tipo_lente" value="{{ old('tipo_lente', $plantilla->tipo_lente ?? '') }}">
                @error('tipo_lente')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Especificaciones</label><input type="text" name="especificaciones_lente" value="{{ old('especificaciones_lente', $plantilla->especificaciones_lente ?? '') }}">
                @error('especificaciones_lente')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Vigencia fórmula</label><input type="text" name="vigencia_formula" value="{{ old('vigencia_formula', $plantilla->vigencia_formula ?? '') }}">
                @error('vigencia_formula')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Filtro</label><input type="text" name="filtro" value="{{ old('filtro', $plantilla->filtro ?? '') }}">
                @error('filtro')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Tiempo formulación</label><input type="text" name="tiempo_formulacion" value="{{ old('tiempo_formulacion', $plantilla->tiempo_formulacion ?? '') }}">
                @error('tiempo_formulacion')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Distancia pupilar</label><input type="text" name="distancia_pupilar" value="{{ old('distancia_pupilar', $plantilla->distancia_pupilar ?? '') }}">
                @error('distancia_pupilar')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
            <div><label>Cantidad</label><input type="number" name="cantidad" value="{{ old('cantidad', $plantilla->cantidad ?? '') }}">
                @error('cantidad')
                <div class="invalid-feedback alerta">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <h3>Diagnósticos</h3>
        <div class="form-group"><label>Diagnóstico principal</label><input type="text" name="diagnostico_principal" value="{{ old('diagnostico_principal', $plantilla->diagnostico_principal ?? '') }}">
            @error('diagnostico_principal')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group"><label>Otros diagnósticos</label><textarea name="otros_diagnosticos">{{ old('otros_diagnosticos', $plantilla->otros_diagnosticos ?? '') }}</textarea>
            @error('otros_diagnosticos')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group"><label>Datos adicionales</label><textarea name="datos_adicionales">{{ old('datos_adicionales', $plantilla->datos_adicionales ?? '') }}</textarea>
            @error('datos_adicionales')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group"><label>Finalidad de la consulta</label><input type="text" name="finalidad_consulta" value="{{ old('finalidad_consulta', $plantilla->finalidad_consulta ?? '') }}">
            @error('finalidad_consulta')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group"><label>Causa / Motivo atención</label><input type="text" name="causa_motivo_atencion" value="{{ old('causa_motivo_atencion', $plantilla->causa_motivo_atencion ?? '') }}">
            @error('causa_motivo_atencion')
            <div class="invalid-feedback alerta">{{ $message }}</div>
            @enderror
        </div>

        <div class="boton-guardar">
            <button type="submit">{{ isset($plantilla) ? 'Actualizar' : 'Guardar' }}</button>
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
    textarea {
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