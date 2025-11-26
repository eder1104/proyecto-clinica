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
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    });
</script>
<style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        min-height: 100vh;
    }

    .container-consentimiento {
        display: flex;
        justify-content: center;
        padding: 20px;
    }

    .card-consentimiento {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 600px;
        padding: 3rem;
        text-align: left;
        transition: all 0.3s ease;
    }

    .card-consentimiento:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .titulo-principal {
        color: #0d6efd;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 0.5px;
    }

    .descripcion {
        color: #6c757d;
        font-size: 0.95rem;
        text-align: center;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    .titulo {
        display: block;
        width: 100%;
        text-align: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: #343a40;
    }

    .options-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-weight: 500;
        color: #495057;
    }

    .option-item input[type="radio"] {
        accent-color: #0d6efd;
        transform: scale(1.2);
    }

    .acompanante-box {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        margin-bottom: 1.5rem;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mb-3 {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #343a40;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        width: 100%;
        background-color: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .form-control[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .footer-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        border-top: 1px solid #eee;
        padding-top: 1.5rem;
    }

    .btn {
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background-color: transparent;
    }

    .btn-outline-secondary:hover {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
</style>
@endsection