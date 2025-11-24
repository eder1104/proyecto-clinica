@extends('layouts.app')

@section('content')
<div class="container-consentimiento">
    <div class="card-consentimiento">
        <form action="{{ route('consentimientos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente_id }}">
            <input type="hidden" name="cita_id" value="{{ $cita_id }}">
            <input type="hidden" name="plantilla_id" value="{{ $plantilla_id }}">

            <h5 class="titulo-principal">Plantilla Consentimiento — Cirugía de Retina</h5>

            <p class="descripcion">
                Procedimiento quirúrgico realizado para corregir desprendimientos de retina, agujeros maculares,
                hemorragias vítreas u otras patologías que afectan la visión y requieren intervención especializada.
            </p>

            <label class="form-label titulo text-center">¿Quién firma?</label>

            <div class="options-row">
                <label class="option-item">
                    <input type="radio" name="tipo_firmante" value="paciente" required>
                    Paciente
                </label>

                <label class="option-item">
                    <input type="radio" name="tipo_firmante" value="acompanante">
                    Acompañante
                </label>
            </div>

            <div id="datos_acompanante" class="acompanante-box" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Nombre del acompañante</label>
                    <input type="text" name="nombre_acompanante" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellido del acompañante</label>
                    <input type="text" name="apellido_acompanante" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cédula del acompañante</label>
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

            <div class="footer-buttons">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paciente = document.querySelector('input[value="paciente"]');
    const acompanante = document.querySelector('input[value="acompanante"]');
    const datos = document.getElementById('datos_acompanante');

    paciente.addEventListener('change', () => datos.style.display = 'none');
    acompanante.addEventListener('change', () => datos.style.display = 'block');

    const hoy = new Date();
    document.getElementById('fecha_firma').value = hoy.toLocaleDateString('es-CO', {
        year: 'numeric', month: '2-digit', day: '2-digit'
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
        text-align: left;
    }

    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        padding: 0.7rem 1rem;
        font-size: 1rem;
        background-color: #fafafa;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #0d6efd;
        background-color: #fff;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.2);
    }

    .options-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1.2rem;
    }

    .option-item {
        font-weight: 600;
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .footer-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 1.2rem;
    }
</style>
@endsection
