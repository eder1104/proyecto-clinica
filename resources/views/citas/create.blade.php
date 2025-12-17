<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Crear Cita</h2>
    </x-slot>

    <div class="main-container">
        <div class="card-container">

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Errores encontrados:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('citas.store') }}" method="POST" id="form-cita">
                @csrf

                <div class="form-group">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Hora</label>
                    <select name="hora_inicio" id="hora_inicio" class="form-select hora-select">
                        @php
                        $times = [];
                        for ($h = 8; $h < 18; $h++) {
                            for ($m=0; $m < 60; $m +=20) {
                            $value=sprintf('%02d:%02d', $h, $m);
                            $ampm=date('g:i A', strtotime($value));
                            $times[]=['value'=> $value, 'label' => "$value ($ampm)"];
                            }
                        }
                        @endphp

                        <option value="">-- Seleccione la hora --</option>

                        @foreach ($times as $t)
                        <option value="{{ $t['value'] }}">
                            {{ $t['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="display:none;">
                    <label class="form-label">Hora de fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de cita</label>
                    <select name="tipo_cita_id" id="tipo_cita_id" class="form-select">
                        <option value="">-- Seleccione el tipo de cita --</option>
                        @foreach ($tipos_citas as $id => $nombre)
                        <option value="{{ $id }}" {{ old('tipo_cita_id') == $id ? 'selected' : '' }} data-es-examen="{{ \Illuminate\Support\Str::slug($nombre) === 'examenes' ? '1' : '0' }}">
                            {{ $nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="campo-examenes" style="display:none;">
                    <label class="form-label">Tipo de Examen</label>
                    <select name="tipo_examen" id="tipo_examen" class="form-select">
                        <option value="">-- Seleccione un examen --</option>
                        <option value="inyeccion_intravitrea" {{ old('tipo_examen') == 'inyeccion_intravitrea' ? 'selected' : '' }}>Inyección intravítrea</option>
                        <option value="fotocoagulacion_laser" {{ old('tipo_examen') == 'fotocoagulacion_laser' ? 'selected' : '' }}>Fotocoagulación láser</option>
                        <option value="cirugia_retina" {{ old('tipo_examen') == 'cirugia_retina' ? 'selected' : '' }}>Cirugía de retina</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Motivo de consulta</label>
                    <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Paciente</label>
                    <div class="patient-grid">
                        <div>
                            <label class="sub-label">Tipo de Documento</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select">
                                <option value="CC">C.C.</option>
                                <option value="TI">T.I.</option>
                                <option value="CE">C.E.</option>
                            </select>
                        </div>
                        <div>
                            <label class="sub-label">Número de Documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" placeholder="Número de documento" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Nombres</label>
                            <input type="text" name="nombres" id="nombres" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Email</label>
                            <input type="email" name="email" id="email" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-input">
                        </div>
                        <div>
                            <label class="sub-label">Sexo</label>
                            <select name="sexo" id="sexo" class="form-select">
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="paciente_id" id="paciente_id">
                </div>

                <div class="form-actions">
                    <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>

            </form>
        </div>
    </div>

    <style>
        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        .main-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .card-container {
            background-color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .alert {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 0.25rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .alert ul {
            list-style-type: disc;
            list-style-position: inside;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            color: #374151;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .sub-label {
            display: block;
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
        }

        .form-input, .form-select {
            display: block;
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 0.5rem;
            margin-top: 0.25rem;
            box-sizing: border-box;
        }

        .form-input:focus, .form-select:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .patient-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .patient-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-secondary {
            background-color: #9ca3af;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #6b7280;
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pacienteIdInput = document.getElementById('paciente_id');
            const formCita = document.getElementById('form-cita');
            const tipoCita = document.getElementById('tipo_cita_id');
            const campoExamenes = document.getElementById('campo-examenes');

            const obtenerDatosPaciente = () => ({
                tipo_documento: document.getElementById('tipo_documento').value,
                documento: document.getElementById('numero_documento').value,
                nombres: document.getElementById('nombres').value,
                apellidos: document.getElementById('apellidos').value,
                telefono: document.getElementById('telefono').value,
                direccion: document.getElementById('direccion').value,
                email: document.getElementById('email').value,
                fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                sexo: document.getElementById('sexo').value
            });

            const actualizarCamposPaciente = (data) => {
                pacienteIdInput.value = data.id;
                document.getElementById('tipo_documento').value = data.tipo_documento;
                document.getElementById('numero_documento').value = data.documento;
                document.getElementById('nombres').value = data.nombres;
                document.getElementById('apellidos').value = data.apellidos;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('direccion').value = data.direccion;
                document.getElementById('email').value = data.email;
                document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento ?? '';
                document.getElementById('sexo').value = data.sexo ?? '';
            };

            const limpiarCamposCompletos = () => {
                pacienteIdInput.value = '';
                document.getElementById('tipo_documento').value = 'CC';
                document.getElementById('numero_documento').value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('apellidos').value = '';
                document.getElementById('telefono').value = '';
                document.getElementById('direccion').value = '';
                document.getElementById('email').value = '';
                document.getElementById('fecha_nacimiento').value = '';
                document.getElementById('sexo').value = 'M';
            };

            const limpiarResultados = () => {
                pacienteIdInput.value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('apellidos').value = '';
                document.getElementById('telefono').value = '';
                document.getElementById('direccion').value = '';
                document.getElementById('email').value = '';
                document.getElementById('fecha_nacimiento').value = '';
                document.getElementById('sexo').value = 'M';
            };

            const numeroInput = document.getElementById('numero_documento');
            const tipoInput = document.getElementById('tipo_documento');

            let timeout = null;
            numeroInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(async () => {
                    const numero = numeroInput.value.trim();
                    const tipo = tipoInput.value;
                    if (!numero) {
                        limpiarCamposCompletos();
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('pacientes.buscar') }}?tipo=${tipo}&numero=${numero}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        const data = await response.json();
                        if (!response.ok || !data.id) {
                            limpiarResultados();
                            return;
                        }
                        actualizarCamposPaciente(data);
                    } catch {
                        limpiarResultados();
                    }
                }, 500);
            });

            formCita.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!pacienteIdInput.value) {
                    alert('Debe ingresar un paciente válido antes de guardar la cita.');
                    return;
                }

                const pacienteId = pacienteIdInput.value;
                const datosPaciente = obtenerDatosPaciente();

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`{{ route('pacientes.actualizarApi', ['id' => 'ID_REEMPLAZAR']) }}`.replace('ID_REEMPLAZAR', pacienteId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(datosPaciente)
                    });
                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        alert('Error al actualizar paciente: ' + (err?.mensaje || `HTTP ${response.status}`));
                        return;
                    }
                    formCita.submit();
                } catch {
                    alert('Error de conexión al actualizar paciente.');
                }
            });

            const toggleExamenes = () => {
                const selectedOption = tipoCita.options[tipoCita.selectedIndex];
                if (selectedOption) {
                    const esExamen = selectedOption.dataset.esExamen === '1';
                    campoExamenes.style.display = esExamen ? 'block' : 'none';
                    if (!esExamen) {
                        document.getElementById('tipo_examen').value = '';
                    }
                }
            };

            tipoCita.addEventListener('change', toggleExamenes);
            toggleExamenes();

            const horaInicio = document.getElementById('hora_inicio');
            const horaFin = document.getElementById('hora_fin');

            horaInicio.addEventListener('change', () => {
                const value = horaInicio.value;
                if (!value) return;

                let [h, m] = value.split(':').map(Number);

                m += 20;
                if (m >= 60) {
                    m -= 60;
                    h++;
                    if (h >= 24) h = 0;
                }

                horaFin.value = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
            });

        });
    </script>

</x-app-layout>