@extends('layouts.app')

@section('title', 'Consulta de Optometría' )
@php

use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$idOptometra = optional($user->doctor)->id;
$nombreCompletoOptometra = trim(($user->nombres ?? '') . ' ' . ($user->apellidos ?? ''));


if (empty($nombreCompletoOptometra) && $idOptometra) {
$nombreCompletoOptometra = 'Doctor ID: ' . $idOptometra;
}

if (empty($nombreCompletoOptometra)) {
$nombreCompletoOptometra = 'Usuario no identificado';
}

$valores = [];
for ($i = -10.0; $i <= 10.0001; $i +=0.5) {
    $valores[]=number_format($i, 2, '.' , '' );
    }
    @endphp
    @section('content')

    @include('components.alertas')

    <div class="optometria-scope">
    <div class="container-custom">
        <h2 class="titulo">Plantilla de Consulta de Optometría</h2>

        <form id="optometriaForm" action="{{ route('optometria.store', ['cita' => $cita->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="paciente_id" id="paciente_id_val"
                value="{{ $cita->paciente->id ?? ($historia->paciente_id ?? '') }}">

            <input type="hidden" name="historia_id" id="historia_id_val"
                value="{{ $historia->id ?? '' }}">

            <div class="nav-simple">
                <button type="button" class="nav-button active" data-target="consulta-content">Consulta</button>
                <button type="button" class="nav-button" data-target="catalogos-content">Diagnósticos</button>
            </div>

            <div id="consulta-content" class="content-section">
                <div class="form-row">
                    <div class="form-row" style="flex-grow: 1;">
                        <div class="form-group small-input" style="flex-grow: 1;">
                            <label>Optómetra</label>
                            <p class="form-control-static">
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
                    <div class="alerta">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div>
                        <label>Alternativa deseada</label>
                        <input type="text" name="alternativa_deseada" value="{{ old('alternativa_deseada', $plantilla->alternativa_deseada ?? '') }}">
                        @error('alternativa_deseada')
                        <div class="alerta">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label>Dominancia ocular</label>
                        <input type="text" name="dominancia_ocular" value="{{ old('dominancia_ocular', $plantilla->dominancia_ocular ?? '') }}">
                        @error('dominancia_ocular')
                        <div class="alerta">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h3>Agudeza Visual (AVSC)</h3>

                <div class="SelectAgudeza">
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

                    <label for="" class="SubTitle_op">AVSC</label>

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
                    <div class="alerta">{{ $message }}</div>
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

                <h3>Medicamentos</h3>

                <div class="form-group">
                    <label>Medicamento principal</label>
                    <select name="medicamento_principal" class="form-control">
                        <option value="">-- Selecciona medicamento --</option>
                        <option value="Acetaminofén" {{ old('medicamento_principal', $plantilla->medicamento_principal ?? '') == 'Acetaminofén' ? 'selected' : '' }}>Acetaminofén</option>
                        <option value="Ibuprofeno" {{ old('medicamento_principal', $plantilla->medicamento_principal ?? '') == 'Ibuprofeno' ? 'selected' : '' }}>Ibuprofeno</option>
                        <option value="Diclofenaco" {{ old('medicamento_principal', $plantilla->medicamento_principal ?? '') == 'Diclofenaco' ? 'selected' : '' }}>Diclofenaco</option>
                        <option value="Amoxicilina" {{ old('medicamento_principal', $plantilla->medicamento_principal ?? '') == 'Amoxicilina' ? 'selected' : '' }}>Amoxicilina</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Otros medicamentos</label>
                    <textarea name="otros_medicamentos" placeholder="Escribe otros medicamentos...">{{ old('otros_medicamentos', $plantilla->otros_medicamentos ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Notas del medicamento</label>
                    <textarea name="notas_medicamento" placeholder="Ejemplo: dosis, frecuencia, vía de administración...">{{ old('notas_medicamento', $plantilla->notas_medicamento ?? '') }}</textarea>
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
            </div>

            <div id="catalogos-content" class="content-section hidden">
                <div class="catalogo-section">
                    <h3>Búsqueda de Catálogos</h3>
                    <p class="catalogo-descripcion" style="text-align: center; color: #666; margin-bottom: 20px;">
                        Maestro para listar diagnósticos, procedimientos y alergias
                    </p>

                    <div id="js-error-container"></div>

                    <div class="search-card">
                        <label>Término Diagnóstico</label>
                        <input type="text" id="termino_diag">
                        <button type="button" id="btnDiag" class="btn-buscar">Buscar 🔍</button>
                    </div>

                    <div class="results-header">Resultados Diagnósticos</div>
                    <ul id="listaDiag" class="results-list"></ul>

                    <div class="results-header" style="margin-top: 20px;">Seleccionados Diagnósticos</div>

                    @error('items_tipos')
                    <div class="alerta">{{ $message }}</div>
                    @enderror

                    <div id="selDiag" style="display: flex; flex-direction: column; gap: 10px;"></div>

                    <hr>

                    <div class="search-card">
                        <label>Término Procedimiento</label>
                        <input type="text" id="termino_proc">
                        <button type="button" id="btnProc" class="btn-buscar">Buscar 🔍</button>
                    </div>

                    <div class="results-header">Resultados Procedimientos</div>
                    <ul id="listaProc" class="results-list"></ul>

                    <div class="results-header" style="margin-top: 20px;">Seleccionados Procedimientos</div>
                    <div id="selProc" style="display: flex; flex-direction: column; gap: 10px;"></div>

                    <hr>

                    <div class="search-card">
                        <label>Término Alergia</label>
                        <input type="text" id="termino_aler">
                        <button type="button" id="btnAler" class="btn-buscar">Buscar 🔍</button>
                    </div>

                    <div class="results-header">Resultados Alergias</div>
                    <ul id="listaAler" class="results-list"></ul>

                    <div class="results-header" style="margin-top: 20px;">Seleccionados Alergias</div>

                    <div id="selAler" style="display: flex; flex-direction: column; gap: 10px;">

                        @foreach($alergiasPrevias as $alergia)
                        <div class="item-seleccionado-wrapper">
                            <input type="text" readonly value="{{ $alergia->nombre }}" class="form-control item-visual">
                            <input type="hidden" name="items_ids[]" value="{{ $alergia->id }}">
                            <input type="hidden" name="items_tipos[]" value="alergia">
                            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>
                        </div>
                        @endforeach

                    </div>

                </div>
            </div>
            <div class="boton-guardar">
                <button type="submit">Guardar Consulta</button>
            </div>
        </form>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    }
                });
            });

            const navButtons = document.querySelectorAll('.nav-button');
            const contentSections = document.querySelectorAll('.content-section');
            const initialActiveButton = document.querySelector('.nav-button.active');

            if (initialActiveButton) {
                const initialTarget = document.getElementById(initialActiveButton.getAttribute('data-target'));
                if (initialTarget) initialTarget.classList.remove('hidden');
            }

            navButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    contentSections.forEach(section => section.classList.add('hidden'));
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    document.getElementById(targetId).classList.remove('hidden');
                    this.classList.add('active');
                });
            });

            function actualizarEstadoBusqueda() {
                
            }

            function crearBuscador(inputId, botonId, listaId, contenedorId, ruta) {
                const input = document.getElementById(inputId);
                const boton = document.getElementById(botonId);
                const lista = document.getElementById(listaId);
                const contenedor = document.getElementById(contenedorId);

                const esDiagnostico = contenedorId === 'selDiag';
                const esProcedimiento = contenedorId === 'selProc';
                const esAlergia = contenedorId === 'selAler';

                if (input) {
                    input.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            boton.click();
                        }
                    });
                }

                boton.addEventListener('click', async function() {
                    const termino = input ? input.value.trim() : '';
                    if (!termino) return;

                    lista.innerHTML = '<li class="msg-loading">Buscando...</li>';
                    document.getElementById('js-error-container').innerHTML = '';


                    try {
                        const response = await fetch(ruta + '?termino=' + encodeURIComponent(termino), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error();

                        const data = await response.json();
                        lista.innerHTML = '';

                        if (!Array.isArray(data) || data.length === 0) {
                            lista.innerHTML = '<li class="msg-vacio">No se encontraron resultados.</li>';
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.classList.add('list-item');

                            const tipo = item.tipo ? '[' + item.tipo + '] ' : '';
                            const texto = tipo + item.nombre + (item.codigo ? ' (' + item.codigo + ')' : '');

                            li.innerHTML = `
                        <div class="item-info">${texto}</div>
                        <button type="button" class="btn-agregar">Agregar</button>
                    `;

                            li.querySelector('.btn-agregar').addEventListener('click', () => {

                                const existentes = Array.from(contenedor.querySelectorAll('input[name="items_ids[]"]'));
                                document.getElementById('js-error-container').innerHTML = '';


                                if (esDiagnostico) {
                                    if (existentes.length >= 1) {
                                        showCustomError('Solo se puede seleccionar un diagnóstico.');
                                        return;
                                    }
                                }

                                if (esProcedimiento) {
                                    const idYaExiste = existentes.some(input => input.value == item.id);
                                    if (idYaExiste) {
                                        showCustomError('El procedimiento ya fue agregado.');
                                        return;
                                    }
                                }

                                if (esAlergia) {
                                    const idYaExiste = existentes.some(input => input.value == item.id);
                                    if (idYaExiste) {
                                        showCustomError('La alergia ya fue agregada.');
                                        return;
                                    }
                                }

                                const wrapper = document.createElement('div');
                                wrapper.className = 'item-seleccionado-wrapper';
                                wrapper.innerHTML = `
                                    <input type="text" readonly value="${texto}" class="form-control item-visual">
                                    <input type="hidden" name="items_ids[]" value="${item.id}">
                                    <input type="hidden" name="items_tipos[]" value="${item.tipo}">
                                    <button type="button" class="btn-remove">×</button>
                                `;

                                wrapper.querySelector('.btn-remove').onclick = function() {
                                    wrapper.remove();
                                    actualizarEstadoBusqueda();
                                };

                                contenedor.appendChild(wrapper);
                                actualizarEstadoBusqueda();
                            });

                            lista.appendChild(li);
                        });

                    } catch (err) {
                        lista.innerHTML = '<li class="msg-error">Error al buscar. Intente nuevamente.</li>';
                    }
                });
            }

            function showCustomError(message) {
                const errorContainer = document.getElementById('js-error-container');
                if (!errorContainer) {
                    return;
                }

                errorContainer.innerHTML = `
        <div class="alerta" style="margin-bottom: 5px;">${message}</div>
    `;
                setTimeout(() => {
                    errorContainer.innerHTML = '';
                }, 4000);
            }


            crearBuscador('termino_diag', 'btnDiag', 'listaDiag', 'selDiag', '{{ route("catalogos.buscarDiagnosticos") }}');
            crearBuscador('termino_proc', 'btnProc', 'listaProc', 'selProc', '{{ route("catalogos.buscarProcedimientos") }}');
            crearBuscador('termino_aler', 'btnAler', 'listaAler', 'selAler', '{{ route("catalogos.buscarAlergias") }}');
        });
    </script>

    <style>
        .optometria-scope .container-custom {
            max-width: 950px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .optometria-scope .titulo {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 24px;
        }

        .optometria-scope input[type="text"],
        .optometria-scope input[type="number"],
        .optometria-scope textarea,
        .optometria-scope select.form-control,
        .optometria-scope input[type="date"] {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            width: 100%;
            transition: all 0.2s ease;
            font-family: inherit;
            font-size: 14px;
            color: #374151;
            background-color: #fff;
        }

        .optometria-scope input:focus,
        .optometria-scope textarea:focus,
        .optometria-scope select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        .optometria-scope textarea {
            resize: vertical;
            min-height: 80px;
        }

        .optometria-scope .small-input input[type="text"] {
            max-width: 200px;
        }

        .optometria-scope .form-control-static {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            background-color: #f0f0f0;
            color: #333;
            font-weight: bold;
        }

        .optometria-scope .nav-simple {
            display: flex;
            margin-bottom: 0;
            border-bottom: 1px solid #e5e7eb;
            gap: 2px;
        }

        .optometria-scope .nav-button {
            padding: 12px 24px;
            border: none;
            background-color: transparent;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.2s;
            border-radius: 8px 8px 0 0;
            position: relative;
            top: 1px;
        }

        .optometria-scope .nav-button:hover {
            color: #1f2937;
            background-color: #f3f4f6;
        }

        .optometria-scope .nav-button.active {
            color: #007bff;
            border: 1px solid #e5e7eb;
            border-bottom: 1px solid #fff;
            background-color: #fff;
            font-weight: 600;
        }

        .optometria-scope .content-section {
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 30px;
            border-radius: 0 0 12px 12px;
            background-color: #fff;
        }

        .optometria-scope .content-section.hidden {
            display: none;
        }

        .optometria-scope .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .optometria-scope .form-row {
            display: flex;
            gap: 25px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .optometria-scope .checkbox-right {
            flex-direction: row;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .optometria-scope .checkbox-right input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .optometria-scope .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: #374151;
        }

        .optometria-scope .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .optometria-scope h3 {
            margin: 35px 0 20px;
            font-size: 18px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .optometria-scope .SelectAgudeza {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .optometria-scope .AgudezaVisual {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            min-width: 100px;
        }

        .optometria-scope .Box_Agudeza {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .optometria-scope .agudeza-select {
            width: 80px;
            text-align: center;
            font-weight: bold;
        }

        .optometria-scope .color-box {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.1s;
        }

        .optometria-scope .color-box:hover {
            transform: scale(1.1);
            border-color: #9ca3af;
        }

        .optometria-scope .SubTitle_op {
            width: 100%;
            text-align: center;
            font-weight: 800;
            color: #9ca3af;
            margin: 15px 0;
            letter-spacing: 1px;
        }

        .optometria-scope .search-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .optometria-scope .search-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            align-items: end;
        }

        .optometria-scope .btn-buscar {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 11px 24px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            width: 100%;
            justify-content: center;
        }

        .optometria-scope .btn-buscar:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .optometria-scope .results-header {
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .optometria-scope .results-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
            background: #fff;
        }

        .optometria-scope .list-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .optometria-scope .list-item:last-child {
            border-bottom: none;
        }

        .optometria-scope .list-item:hover {
            background-color: #f9fafb;
        }

        .optometria-scope .item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .optometria-scope .btn-agregar {
            background-color: #198754;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }

        .optometria-scope .btn-agregar:hover {
            background-color: #157347;
        }

        .optometria-scope .msg-vacio,
        .optometria-scope .msg-error,
        .optometria-scope .msg-loading {
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        .optometria-scope .msg-error {
            color: #dc2626;
        }

        .optometria-scope .boton-guardar {
            text-align: center;
            margin-top: 40px;
        }

        .optometria-scope .boton-guardar button {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 14px 40px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .optometria-scope .boton-guardar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }

        .optometria-scope .alerta {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 5px;
        }

        .optometria-scope .item-seleccionado-wrapper {
            display: flex;
            gap: 5px;
            margin-bottom: 8px;
        }

        .optometria-scope .item-visual {
            flex-grow: 1;
            background-color: #f8f9fa;
        }

        .optometria-scope .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 30px;
            font-size: 18px;
        }
    </style>
    @endsection