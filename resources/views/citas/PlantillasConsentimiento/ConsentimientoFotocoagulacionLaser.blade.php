@extends('layouts.app')

@section('content')
<div class="container-consentimiento">
    <div class="card-consentimiento">

        <form action="{{ route('consentimientos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paciente_id" value="{{ $paciente_id }}">
            <input type="hidden" name="cita_id" value="{{ $cita_id }}">
            

            <h5 class="titulo-principal">
                Plantilla Consentimiento — Fotocoagulación Láser
            </h5>

            <p class="descripcion">
                Tratamiento con láser que permite sellar vasos sanguíneos anormales o reducir inflamación en la retina,
                usado comúnmente en retinopatía diabética o desgarros retinianos.
            </p>

            <label class="form-label titulo">¿Quién firma?</label>

            <div class="mb-3 option">
                <label class="form-check d-flex align-items-center gap-2">
                    <input class="form-check-input" type="radio" name="tipo_firmante" value="paciente" required>
                    <span>Paciente</span>
                </label>

                <label class="form-check d-flex align-items-center gap-2">
                    <input class="form-check-input" type="radio" name="tipo_firmante" value="acompanante">
                    <span>Acompañante</span>
                </label>
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

            <div class="mb-4">
                <label class="form-label">Firma (imagen)</label>
                <input type="file" name="imagen_firma" class="form-control" accept="image/jpeg,image/png" required>
            </div>

            <div class="text-center mt-4 botones">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
            </div>

        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="tipo_firmante"]');
        const datosAcompanante = document.getElementById('datos_acompanante');
        const fechaFirma = document.getElementById('fecha_firma');

        fechaFirma.value = new Date().toLocaleDateString('es-CO', {
            year: 'numeric', month: '2-digit', day: '2-digit'
        });

        radios.forEach(r => {
            r.addEventListener('change', () => {
                datosAcompanante.style.display = r.value === 'acompanante' ? 'block' : 'none';
            });
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
        padding: 30px;
    }

    .card-consentimiento {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 550px;
        padding: 2.7rem;
        text-align: center;
    }

    .titulo-principal {
        color: #0d6efd;
        font-weight: 700;
        margin-bottom: 0.8rem;
    }

    .descripcion {
        font-size: 0.85rem;
        color: #555;
        margin-bottom: 1.8rem;
        line-height: 1.4rem;
    }

    .titulo {
        font-weight: 700;
        text-align: center;
        margin-bottom: 0.6rem;
        font-size: 1rem;
    }

    .form-label {
        font-weight: 600;
        color: #212529;
        font-size: 0.9rem;
        text-align: left;
    }

    .form-control {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        background-color: #fafafa;
        border: 1px solid #d1d1d1;
        transition: .2s;
    }

    .form-control:focus {
        border-color: #0d6efd;
        background: #fff;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.2);
    }

    .option {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1.3rem;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .botones {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
</style>

@endsection
