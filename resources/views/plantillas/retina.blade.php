@extends('layouts.app')

@section('title', 'Consulta de Retina')

@section('content')
<div class="contenedor-principal">
    <form id="retinaForm" action="{{ route('retina.store', ['cita' => $cita->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="retina-container">
            <div class="columna imagen">
                <h3>Ojo Izquierdo</h3>
                <input type="file" name="imagen_ojo_izquierdo" accept="image/*" id="inputIzq">
                <canvas id="canvasIzq" width="300" height="300"></canvas>
            </div>

            <div class="columna info">
                <h2>Datos de la Consulta</h2>
                <div class="campo">
                    <label>Diagnóstico</label>
                    <input type="text" name="diagnostico" value="{{ $plantilla->diagnostico ?? '' }}">
                </div>

                <div class="campo">
                    <label>Tratamiento</label>
                    <input type="text" name="tratamiento" value="{{ $plantilla->tratamiento ?? '' }}">
                </div>

                <div class="campo">
                    <label>Observaciones</label>
                    <textarea name="observaciones">{{ $plantilla->observaciones ?? '' }}</textarea>
                </div>

                <div class="botones-dibujo">
                    <button type="button" onclick="toggleDrawMode('izq')">✏️ Dibujar Izquierdo</button>
                    <button type="button" onclick="clearCanvas('izq')">🧹 Limpiar Izquierdo</button>
                    <button type="button" onclick="toggleDrawMode('der')">✏️ Dibujar Derecho</button>
                    <button type="button" onclick="clearCanvas('der')">🧹 Limpiar Derecho</button>
                </div>

                <div class="boton-guardar">
                    <button type="submit">Guardar</button>
                </div>
            </div>

            <div class="columna imagen">
                <h3>Ojo Derecho</h3>
                <input type="file" name="imagen_ojo_derecho" accept="image/*" id="inputDer">
                <canvas id="canvasDer" width="300" height="300"></canvas>
            </div>
        </div>
    </form>
</div>

<style>
    .retina-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
    }

    .columna {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .botones-dibujo button {
        margin: 5px;
        padding: 6px 10px;
        border: none;
        background: #2563eb;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    canvas {
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
<script>
    const canvasIzq = new fabric.Canvas('canvasIzq');
    const canvasDer = new fabric.Canvas('canvasDer');

    let drawModeIzq = false;
    let drawModeDer = false;

    document.getElementById('inputIzq').addEventListener('change', e => loadImage(e, canvasIzq));
    document.getElementById('inputDer').addEventListener('change', e => loadImage(e, canvasDer));

    function loadImage(e, canvas) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = f => {
            fabric.Image.fromURL(f.target.result, img => {
                canvas.clear();
                img.scaleToWidth(canvas.width);
                canvas.add(img);
                canvas.sendToBack(img);
            });
        };
        reader.readAsDataURL(file);
    }

    function toggleDrawMode(side) {
        if (side === 'izq') {
            drawModeIzq = !drawModeIzq;
            canvasIzq.isDrawingMode = drawModeIzq;
        } else {
            drawModeDer = !drawModeDer;
            canvasDer.isDrawingMode = drawModeDer;
        }
    }

    function clearCanvas(side) {
        if (side === 'izq') canvasIzq.clear();
        else canvasDer.clear();
    }

    document.getElementById('retinaForm').addEventListener('submit', e => {
        e.preventDefault();
        const form = e.target;

        const imgIzq = canvasIzq.toDataURL('image/png');
        const imgDer = canvasDer.toDataURL('image/png');

        const inputHiddenIzq = document.createElement('input');
        inputHiddenIzq.type = 'hidden';
        inputHiddenIzq.name = 'imagen_editada_izq';
        inputHiddenIzq.value = imgIzq;

        const inputHiddenDer = document.createElement('input');
        inputHiddenDer.type = 'hidden';
        inputHiddenDer.name = 'imagen_editada_der';
        inputHiddenDer.value = imgDer;

        form.appendChild(inputHiddenIzq);
        form.appendChild(inputHiddenDer);

        form.submit();
    });
</script>
@endsection