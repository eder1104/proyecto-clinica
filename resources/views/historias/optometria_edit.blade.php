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

@extends('layouts.app')

@section('title', 'Consulta de Optometría')

@section('content')
<div class="optometria-scope">
    <div class="container-custom">
        <h2 class="titulo">Plantilla de Consulta de Optometría</h2>

        <form id="optometriaForm" action="{{ route('optometria.store', ['cita' => $cita->id]) }}" method="POST">
            @csrf

            <div class="nav-simple">
                <button type="button" class="nav-button active" data-target="consulta-content">Consulta</button>
                <button type="button" class="nav-button" data-target="catalogos-content">Catálogos</button>
            </div>

            <div id="consulta-content" class="content-section">
                <div class="form-row">
                    <div class="form-row" style="flex-grow: 1;">
                        <div class="form-group small-input" style="flex-grow: 1;">
                            <label>Optómetra</label>
                            <p class="form-control-static">
                                {{ $nombreCompletoOptometra }}
                            </p>
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
                </div>

                <div class="grid-2">
                    <div>
                        <label>Alternativa deseada</label>
                        <input type="text" name="alternativa_deseada" value="{{ old('alternativa_deseada', $plantilla->alternativa_deseada ?? '') }}">
                    </div>
                    <div>
                        <label>Dominancia ocular</label>
                        <input type="text" name="dominancia_ocular" value="{{ old('dominancia_ocular', $plantilla->dominancia_ocular ?? '') }}">
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
                                    <option value="{{ $valor }}">{{ $valor }}</option>
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
                                    <option value="{{ $valor }}">{{ $valor }}</option>
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
                                    <option value="{{ $valor }}">{{ $valor }}</option>
                                @endforeach
                            </select>
                            <div class="color-box" data-input="av_cerca_od"></div>
                        </div>
                    </div>

                    <label class="SubTitle_op">AVSC</label>

                    <div class="AgudezaVisual">
                        <label>Lejos OI</label>
                        <div class="Box_Agudeza">
                            <select name="av_lejos_oi" class="form-control agudeza-select">
                                <option value=""></option>
                                @foreach($valores as $valor)
                                    <option value="{{ $valor }}">{{ $valor }}</option>
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
                                    <option value="{{ $valor }}">{{ $valor }}</option>
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
                                    <option value="{{ $valor }}">{{ $valor }}</option>
                                @endforeach
                            </select>
                            <div class="color-box" data-input="av_cerca_oi"></div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label>Observaciones optometría</label>
                    <textarea name="observaciones_optometria">{{ old('observaciones_optometria', $plantilla->observaciones_optometria ?? '') }}</textarea>
                </div>

                <h3>Fórmula y lentes</h3>
                <div class="grid-2">
                    <div>
                        <label>Tipo de lente</label>
                        <select name="tipo_lente" class="form-control">
                            <option value="">-- Selecciona tipo --</option>
                            <option value="Monofocal">Monofocal</option>
                            <option value="Bifocal">Bifocal</option>
                            <option value="Progresivo">Progresivo</option>
                        </select>
                    </div>

                    <div>
                        <label>Especificaciones del lente</label>
                        <input type="text" name="especificaciones_lente">
                    </div>

                    <div>
                        <label>Vigencia de fórmula</label>
                        <input type="date" name="vigencia_formula">
                    </div>

                    <div>
                        <label>Filtro</label>
                        <select name="filtro" class="form-control">
                            <option value="">-- Selecciona filtro --</option>
                            <option value="Antirreflejo">Antirreflejo</option>
                            <option value="Luz azul">Luz azul</option>
                            <option value="Fotocromático">Fotocromático</option>
                        </select>
                    </div>

                    <div>
                        <label>Tiempo de formulación (meses)</label>
                        <input type="number" min="0" name="tiempo_formulacion">
                    </div>

                    <div>
                        <label>Distancia pupilar (mm)</label>
                        <input type="number" step="0.5" min="0" name="distancia_pupilar">
                    </div>

                    <div>
                        <label>Cantidad de lentes</label>
                        <input type="number" min="1" name="cantidad">
                    </div>
                </div>
                
                <h3>Diagnósticos</h3>
                <div class="form-group">
                    <label>Diagnóstico principal</label>
                    <select name="diagnostico_principal" class="form-control">
                        <option value="">-- Selecciona diagnóstico --</option>
                        <option value="Miopía">Miopía</option>
                        <option value="Hipermetropía">Hipermetropía</option>
                        <option value="Astigmatismo">Astigmatismo</option>
                        <option value="Presbicia">Presbicia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Otros diagnósticos</label>
                    <textarea name="otros_diagnosticos">{{ old('otros_diagnosticos', $plantilla->otros_diagnosticos ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Datos adicionales</label>
                    <textarea name="datos_adicionales">{{ old('datos_adicionales', $plantilla->datos_adicionales ?? '') }}</textarea>
                </div>

                <div class="grid-2">
                    <div>
                        <label>Finalidad de la consulta</label>
                        <select name="finalidad_consulta" class="form-control">
                            <option value="">-- Selecciona --</option>
                            <option value="Control">Control</option>
                            <option value="Diagnóstico">Diagnóstico</option>
                            <option value="Formulación">Formulación</option>
                        </select>
                    </div>

                    <div>
                        <label>Causa / Motivo de atención</label>
                        <select name="causa_motivo_atencion" class="form-control">
                            <option value="">-- Selecciona --</option>
                            <option value="Molestias visuales">Molestias visuales</option>
                            <option value="Control rutinario">Control rutinario</option>
                            <option value="Cambio de fórmula">Cambio de fórmula</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="catalogos-content" class="content-section hidden">

                <div class="catalogo-section">
                    <h3>Diagnósticos</h3>
                    <input type="text" id="buscarDiagnosticos" placeholder="Buscar diagnósticos...">
                    <ul id="resultDiagnosticos"></ul>
                    <div id="selDiagnosticos"></div>
                </div>

                <div class="catalogo-section">
                    <h3>Procedimientos</h3>
                    <input type="text" id="buscarProcedimientos" placeholder="Buscar procedimientos...">
                    <ul id="resultProcedimientos"></ul>
                    <div id="selProcedimientos"></div>
                </div>

                <div class="catalogo-section">
                    <h3>Alergias</h3>
                    <input type="text" id="buscarAlergias" placeholder="Buscar alergias...">
                    <ul id="resultAlergias"></ul>
                    <div id="selAlergias"></div>
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

    const colors=["green","blue","red","transparent"];
    document.querySelectorAll(".color-box").forEach(box=>{
        box.addEventListener("click",()=>{
            let c=box.dataset.colorIndex?parseInt(box.dataset.colorIndex):0;
            c=(c+1)%colors.length;
            box.dataset.colorIndex=c;
            const col=colors[c];
            box.style.backgroundColor=col;
            const input=box.dataset.input;
            const field=document.querySelector(`[name="${input}"]`);
            if(field){
                field.style.color=(col==="transparent")?"black":col;
                if(field.tagName.toLowerCase()==="select"){
                    field.style.color=(col==="transparent")?"black":col;
                }
            }
        });
    });

    const navButtons=document.querySelectorAll('.nav-button');
    const sections=document.querySelectorAll('.content-section');
    const active=document.querySelector('.nav-button.active');
    if(active){
        const t=document.getElementById(active.getAttribute('data-target'));
        if(t) t.classList.remove('hidden');
    }
    navButtons.forEach(btn=>{
        btn.addEventListener('click',function(){
            const id=this.getAttribute('data-target');
            sections.forEach(s=>s.classList.add('hidden'));
            navButtons.forEach(b=>b.classList.remove('active'));
            document.getElementById(id).classList.remove('hidden');
            this.classList.add('active');
        });
    });

    function buscador(inputId,ulId,selId,url,tipo){
        const input=document.getElementById(inputId);
        const ul=document.getElementById(ulId);
        const sel=document.getElementById(selId);

        input.addEventListener('keydown',e=>{
            if(e.key==="Enter"){
                e.preventDefault();
                buscar();
            }
        });

        input.addEventListener('input',function(){
            if(this.value.trim()===""){
                ul.innerHTML="";
            }
        });

        async function buscar(){
            const t=input.value.trim();
            if(!t) return;
            ul.innerHTML='<li>Buscando...</li>';
            try{
                const r=await fetch(url+'?termino='+encodeURIComponent(t));
                if(!r.ok) throw new Error();
                const data=await r.json();
                ul.innerHTML="";
                if(!data.length){
                    ul.innerHTML='<li>No se encontraron resultados.</li>';
                    return;
                }
                data.forEach(item=>{
                    const li=document.createElement('li');
                    li.textContent=item.nombre+(item.codigo?(' ('+item.codigo+')'):'');
                    li.style.cursor="pointer";
                    li.onclick=()=>{
                        const box=document.createElement('div');
                        box.style.display='flex';
                        box.style.gap='6px';
                        box.style.margin='4px 0';

                        const v=document.createElement('input');
                        v.type='text';
                        v.readOnly=true;
                        v.style.flexGrow='1';
                        v.value=item.nombre+(item.codigo?(' ('+item.codigo+')'):'');
                        box.appendChild(v);

                        const hid=document.createElement('input');
                        hid.type='hidden';
                        hid.name='items_ids[]';
                        hid.value=item.id;
                        box.appendChild(hid);

                        const ht=document.createElement('input');
                        ht.type='hidden';
                        ht.name='items_tipos[]';
                        ht.value=tipo;
                        box.appendChild(ht);

                        const rm=document.createElement('button');
                        rm.type='button';
                        rm.textContent='×';
                        rm.style.width='28px';
                        rm.style.background='#c00';
                        rm.style.color='white';
                        rm.onclick=()=>box.remove();
                        box.appendChild(rm);

                        sel.appendChild(box);
                    };
                    ul.appendChild(li);
                });
            }catch(e){
                ul.innerHTML='<li>Error al buscar. Intente nuevamente.</li>';
            }
        }

        input.addEventListener('change',buscar);
    }

    buscador('buscarDiagnosticos','resultDiagnosticos','selDiagnosticos','{{ route("catalogos.buscarDiagnosticos") }}','diagnostico');
    buscador('buscarProcedimientos','resultProcedimientos','selProcedimientos','{{ route("catalogos.buscarProcedimientos") }}','procedimiento');
    buscador('buscarAlergias','resultAlergias','selAlergias','{{ route("catalogos.buscarAlergias") }}','alergia');

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
        transition: background 0.3s, transform 0.1s;
        font-size: 15px;
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
        transition: background-color 0.2s;
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
        transition: background 0.2s ease;
        font-size: 12px;
        font-weight: bold;
    }

    .optometria-scope .btn-agregar:hover {
        background-color: #157347;
    }

    .optometria-scope .item-seleccionado {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        gap: 5px;
    }

    .optometria-scope .item-seleccionado input[type="text"] {
        background-color: #f8f9fa;
        cursor: not-allowed;
        margin-bottom: 0;
    }

    .optometria-scope .btn-remover {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        width: 38px;
        height: 38px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }

    .optometria-scope .btn-remover:hover {
        background-color: #bb2d3b;
    }

    .optometria-scope .msg-vacio, 
    .optometria-scope .msg-error, 
    .optometria-scope .msg-loading {
        padding: 30px;
        text-align: center;
        color: #6b7280;
        font-style: italic;
    }
    
    .optometria-scope .msg-error { color: #dc2626; }

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
        transition: all 0.3s;
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
</style>
@endsection