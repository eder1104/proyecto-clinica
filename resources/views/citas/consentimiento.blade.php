@extends('layouts.app')

@section('content')
<div class="container-consentimiento">
    <div class="card-consentimiento">
        <form action="{{ route('consentimientos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente_id }}">
            <input type="hidden" name="cita_id" value="{{ $cita_id }}">

            <h5>Registrar Consentimiento</h5>

            <div class="mb-3 d-flex justify-content-center align-items-center gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_firmante" id="firmante_paciente" value="paciente" required>
                    <label class="form-check-label" for="firmante_paciente">Paciente</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_firmante" id="firmante_acompanante" value="acompanante">
                    <label class="form-check-label" for="firmante_acompanante">Acompañante</label>
                </div>
            </div>

            <div id="datos_acompanante" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Nombre del Acompañante</label>
                    <input type="text" name="nombre_acompanante" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellido del Acompañante</label>
                    <input type="text" name="apellido_acompanante" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Cédula del Acompañante</label>
                    <input type="text" name="cedula_acompanante" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de Firma</label>
                <input type="text" name="fecha_firma" id="fecha_firma" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Firma (imagen)</label>
                <input type="file" name="imagen_firma" class="form-control" accept="image/jpeg,image/png" required>
            </div>

            <div class="text-center mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pacienteRadio = document.getElementById('firmante_paciente');
    const acompananteRadio = document.getElementById('firmante_acompanante');
    const datosAcompanante = document.getElementById('datos_acompanante');
    const fechaFirma = document.getElementById('fecha_firma');

    const hoy = new Date();
    const opciones = { year: 'numeric', month: '2-digit', day: '2-digit' };
    fechaFirma.value = hoy.toLocaleDateString('es-CO', opciones);

    pacienteRadio.addEventListener('change', function() {
        if (pacienteRadio.checked) {
            datosAcompanante.style.display = 'none';
        }
    });

    acompananteRadio.addEventListener('change', function() {
        if (acompananteRadio.checked) {
            datosAcompanante.style.display = 'block';
        }
    });
});
</script>

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    height: 100vh;
}
.container-consentimiento {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    padding: 20px;
}
.card-consentimiento {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    width: 100%;
    max-width: 500px;
    padding: 2.5rem;
    text-align: center;
    transition: all 0.3s ease;
}
.card-consentimiento:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
}
.card-consentimiento h5 {
    color: #0d6efd;
    font-weight: 700;
    margin-bottom: 1.8rem;
    letter-spacing: 0.4px;
}
.form-label {
    font-weight: 600;
    color: #212529;
    font-size: 0.9rem;
    margin-bottom: 0.4rem;
    display: block;
    text-align: left;
}
.form-control,
.form-select {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    padding: 0.7rem 1rem;
    font-size: 1rem;
    width: 100%;
    background-color: #fafafa;
    transition: all 0.2s ease;
}
.form-control:focus,
.form-select:focus {
    border-color: #0d6efd;
    background-color: #fff;
    box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.2);
}
.btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1.2rem;
    font-weight: 600;
    transition: all 0.2s ease;
}
.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
.btn-outline-secondary:hover {
    background-color: #f1f3f5;
    color: #212529;
}
</style>
@endsection
