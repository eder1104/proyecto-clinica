@extends('layouts.legacy-hc')

@section('content')
<div class="hc-container">
    <div class="hc-header">
        <h2>Contenedor de Pacientes - Interfaz Original</h2>
    </div>

    <div class="hc-search-bar" style="background: #e0e0e0; padding: 10px; border: 1px solid #999;">
        <label>Buscar Paciente:</label>
        <input type="text" id="inputBuscar" placeholder="Nombre o Documento..." style="width: 300px;">
        <button id="btnBuscar" class="btn-legacy">BUSCAR</button>
    </div>

    <div class="hc-content" style="margin-top: 20px;">
        <table border="1" style="width: 100%; border-collapse: collapse; background: white;">
            <thead style="background: #ccc;">
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Nombre Completo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="listaPacientes">
                <tr>
                    <td colspan="4" style="text-align: center;">Use el buscador para listar pacientes</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="modalAgendar" style="display:none; position: fixed; top: 20%; left: 30%; background: #fff; border: 2px solid #000; padding: 20px; z-index: 1000;">
        <h3>Agendar Cita (Legacy)</h3>
        <input type="hidden" id="pacienteId">
        <p>Paciente: <span id="nombrePaciente"></span></p>
        
        <label>Médico:</label>
        <select id="selectDoctor">
            @foreach($doctores as $doc)
                <option value="{{ $doc->id }}">{{ $doc->nombre }}</option>
            @endforeach
        </select><br><br>

        <label>Fecha:</label>
        <input type="date" id="fechaCita"><br><br>

        <label>Hora:</label>
        <input type="time" id="horaCita"><br><br>

        <button onclick="guardarCita()" class="btn-legacy" style="background: #4caf50; color: white;">GUARDAR</button>
        <button onclick="cerrarModal()" class="btn-legacy">CANCELAR</button>
    </div>
</div>

@vite(['resources/js/legacy/container.js', 'resources/css/legacy/container.css'])

<script>
    document.getElementById('btnBuscar').addEventListener('click', function() {
        const search = document.getElementById('inputBuscar').value;
        fetch(`/legacy/buscar?search=${search}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('listaPacientes');
                tbody.innerHTML = '';
                data.forEach(p => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${p.id}</td>
                            <td>${p.documento}</td>
                            <td>${p.nombre}</td>
                            <td>
                                <button onclick="abrirModal(${p.id}, '${p.nombre}')" style="cursor:pointer">AGENDAR</button>
                            </td>
                        </tr>`;
                });
            });
    });

    function abrirModal(id, nombre) {
        document.getElementById('pacienteId').value = id;
        document.getElementById('nombrePaciente').innerText = nombre;
        document.getElementById('modalAgendar').style.display = 'block';
    }

    function cerrarModal() {
        document.getElementById('modalAgendar').style.display = 'none';
    }

    function guardarCita() {
        const payload = {
            paciente_id: document.getElementById('pacienteId').value,
            doctor_id: document.getElementById('selectDoctor').value,
            fecha: document.getElementById('fechaCita').value,
            hora: document.getElementById('horaCita').value + ':00',
            _token: '{{ csrf_token() }}'
        };

        fetch('/legacy/agendar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.mensaje) {
                alert(data.mensaje);
                cerrarModal();
            } else {
                alert('Error: ' + (data.detalle || 'Datos inválidos'));
            }
        });
    }
</script>
@endsection