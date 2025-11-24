<div class="catalogo-container">
  <div class="catalogo-card">
    <h4 class="catalogo-titulo">Gestión Clínica</h4>
    <p class="catalogo-descripcion">
      Buscador unificado de diagnósticos, procedimientos y alergias.
    </p>

    <div class="catalogo-form">
      @php
      $diagnosticoExiste = isset($historia) && $historia->diagnostico;
      @endphp
      <div class="campo">
        <input type="text" id="termino"
          placeholder="{{ $diagnosticoExiste ? 'Diagnóstico ya seleccionado' : 'Escribe para buscar...' }}"
          autocomplete="off"
          {{ $diagnosticoExiste ? 'disabled' : '' }}>
      </div>
      <div class="campo">
        <button type="button" id="btnBuscarCatalogo" {{ $diagnosticoExiste ? 'disabled' : '' }}>
          {{ $diagnosticoExiste ? 'Buscar 🚫' : 'Buscar 🔍' }}
        </button>
      </div>
    </div>

    <div class="resultados">
      <h6>Resultados</h6>
      <ul id="resultados"></ul>
    </div>

    <div class="seleccionados">
      <h6>Elementos Seleccionados</h6>
      <div id="contenedorData">
        <input type="hidden" id="paciente_id_val" name="paciente_id" value="{{ $paciente->id ?? ($historia->paciente_id ?? '') }}">
        <input type="hidden" id="historia_id_val" name="historia_id" value="{{ $historia->id ?? '' }}">

        <div id="contenedorSeleccionados">

          <h6 style="margin-top:10px; color:#6610f2;">Diagnósticos</h6>
          <div id="section-diagnosticos">
            @if(isset($historia) && $historia->diagnostico)
            <div class="item-seleccionado">
              <input type="text" readonly value="[Diagnóstico] {{ $historia->diagnostico->nombre }}">
              <input type="hidden" name="items_ids[]" value="{{ $historia->diagnostico->id }}">
              <input type="hidden" name="items_tipos[]" value="diagnostico">
              <button type="button" class="btn-remover" onclick="this.parentElement.remove(); actualizarEstadoBusqueda();">×</button>
            </div>
            @endif
          </div>

          <h6 style="margin-top:15px; color:#0d6efd;">Procedimientos</h6>
          <div id="section-procedimientos">
            @if(isset($historia))
            @foreach($historia->procedimientos as $proc)
            <div class="item-seleccionado">
              <input type="text" readonly value="[Procedimiento] {{ $proc->nombre }}">
              <input type="hidden" name="items_ids[]" value="{{ $proc->id }}">
              <input type="hidden" name="items_tipos[]" value="procedimiento">
              <button type="button" class="btn-remover" onclick="this.parentElement.remove(); actualizarEstadoBusqueda();">×</button>
            </div>
            @endforeach
            @endif
          </div>

          <h6 style="margin-top:15px; color:#198754;">Alergias</h6>
          <div id="section-alergias">
            @if(isset($paciente) && $paciente->alergias)
            @foreach($paciente->alergias as $alergia)
            <div class="item-seleccionado">
              <input type="text" readonly value="[Alergia] {{ $alergia->nombre }}">
              <input type="hidden" name="items_ids[]" value="{{ $alergia->id }}">
              <input type="hidden" name="items_tipos[]" value="alergia">
              <button type="button" class="btn-remover" onclick="this.parentElement.remove(); actualizarEstadoBusqueda();">×</button>
            </div>
            @endforeach
            @endif
          </div>

        </div>
      </div>
    </div>

    <div class="footer-acciones">
      <button type="button" id="btnGuardarClinica" class="btn-guardar">Guardar Consulta 💾</button>
    </div>
  </div>
</div>

<script>
  const btnBuscar = document.getElementById('btnBuscarCatalogo');
  const btnGuardar = document.getElementById('btnGuardarClinica');
  const inputTermino = document.getElementById('termino');
  const lista = document.getElementById('resultados');
  const contenedorSeleccionados = document.getElementById('contenedorSeleccionados');

  function actualizarEstadoBusqueda() {
    const inputsTipos = Array.from(contenedorSeleccionados.querySelectorAll('input[name="items_tipos[]"]'));
    const tieneDiagnostico = inputsTipos.some(input => input.value === 'diagnostico');

    if (tieneDiagnostico) {
      inputTermino.disabled = true;
      inputTermino.placeholder = 'Diagnóstico ya seleccionado';
      btnBuscar.disabled = true;
      btnBuscar.innerText = 'Buscar 🚫';
      lista.innerHTML = '';
    } else {
      inputTermino.disabled = false;
      inputTermino.placeholder = 'Escribe para buscar...';
      btnBuscar.disabled = false;
      btnBuscar.innerText = 'Buscar 🔍';
    }
  }

  document.querySelectorAll('.btn-remover').forEach(btn => {
    btn.addEventListener('click', function() {
      this.parentElement.remove();
      actualizarEstadoBusqueda();
    });
  });

  actualizarEstadoBusqueda();

  inputTermino.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') btnBuscar.click();
  });

  btnBuscar.addEventListener('click', async () => {
    const termino = inputTermino.value.trim();
    if (!termino) return;

    lista.innerHTML = '<li style="text-align:center; color:#888;">Buscando...</li>';

    try {
      const response = await fetch(`{{ route('catalogos.buscar') }}?termino=${encodeURIComponent(termino)}`);
      const data = await response.json();

      lista.innerHTML = '';

      if (data.length === 0) {
        lista.innerHTML = '<li style="text-align:center; color:#888;">No se encontraron resultados.</li>';
        return;
      }

      data.forEach(item => {
        const li = document.createElement('li');

        let color = '#6c757d';
        if (item.tipo === 'procedimiento') color = '#0d6efd';
        if (item.tipo === 'diagnostico') color = '#6610f2';
        if (item.tipo === 'alergia') color = '#198754';

        const labelHtml = `<span style="color:${color}; font-weight:bold; font-size:0.85em;">[${item.label}]</span>`;
        const codigoHtml = item.codigo ? ` <span style="color:#999;">(${item.codigo})</span>` : '';

        li.innerHTML = `
        <div>${labelHtml} <strong>${item.nombre}</strong>${codigoHtml}</div>
        <button type="button" class="agregar-btn">Agregar</button>
      `;

        const btnAgregar = li.querySelector('.agregar-btn');

        btnAgregar.addEventListener('click', () => {
          if (item.tipo === 'diagnostico') {
            const existe = Array.from(contenedorSeleccionados.querySelectorAll('input[name="items_tipos[]"]'))
              .some(input => input.value === 'diagnostico');
            if (existe) {
              alert('Solo puedes seleccionar un diagnóstico principal.');
              return;
            }
          }

          let destino = null;
          if (item.tipo === 'diagnostico') destino = document.getElementById('section-diagnosticos');
          if (item.tipo === 'procedimiento') destino = document.getElementById('section-procedimientos');
          if (item.tipo === 'alergia') destino = document.getElementById('section-alergias');

          const wrapper = document.createElement('div');
          wrapper.className = 'item-seleccionado';

          const inputVisual = document.createElement('input');
          inputVisual.type = 'text';
          inputVisual.readOnly = true;
          inputVisual.value = `[${item.label}] ${item.nombre}`;

          const inputId = document.createElement('input');
          inputId.type = 'hidden';
          inputId.name = 'items_ids[]';
          inputId.value = item.id;

          const inputTipo = document.createElement('input');
          inputTipo.type = 'hidden';
          inputTipo.name = 'items_tipos[]';
          inputTipo.value = item.tipo;

          const btnRemove = document.createElement('button');
          btnRemove.type = 'button';
          btnRemove.className = 'btn-remover';
          btnRemove.innerText = '×';
          btnRemove.onclick = function() {
            wrapper.remove();
            actualizarEstadoBusqueda();
          };

          wrapper.appendChild(inputVisual);
          wrapper.appendChild(inputId);
          wrapper.appendChild(inputTipo);
          wrapper.appendChild(btnRemove);

          destino.appendChild(wrapper);

          btnAgregar.innerText = 'Agregado';
          btnAgregar.disabled = true;
          btnAgregar.style.backgroundColor = '#ccc';

          actualizarEstadoBusqueda();
        });

        lista.appendChild(li);
      });

    } catch (error) {
      console.error(error);
      lista.innerHTML = '<li style="color:red;">Error de conexión.</li>';
    }
  });

  btnGuardar.addEventListener('click', async () => {
    const pacienteId = document.getElementById('paciente_id_val').value;
    const historiaId = document.getElementById('historia_id_val').value;

    const itemsIds = Array.from(document.querySelectorAll('input[name="items_ids[]"]')).map(el => el.value);
    const itemsTipos = Array.from(document.querySelectorAll('input[name="items_tipos[]"]')).map(el => el.value);

    if (!pacienteId || !historiaId) {
      alert('Falta información del paciente o historia clínica.');
      return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('paciente_id', pacienteId);
    formData.append('historia_id', historiaId);

    itemsIds.forEach(id => formData.append('items_ids[]', id));
    itemsTipos.forEach(tipo => formData.append('items_tipos[]', tipo));

    btnGuardar.disabled = true;
    btnGuardar.innerText = 'Guardando...';

    try {
      const response = await fetch('{{ route("catalogos.guardar") }}', {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        alert('Datos guardados correctamente');
        window.location.reload();
      } else {
        alert('Error al guardar los datos');
        btnGuardar.disabled = false;
        btnGuardar.innerText = 'Guardar Consulta 💾';
      }
    } catch (error) {
      console.error(error);
      alert('Error de red');
      btnGuardar.disabled = false;
      btnGuardar.innerText = 'Guardar Consulta 💾';
    }
  });
</script>

<style>
  .catalogo-container {
    display: flex;
    justify-content: center;
    padding: 10px;
    background-color: #f4f6f9;
    height: auto;
  }
  .catalogo-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 20px;
    width: 100%;
    max-width: 100%;
    display: flex;
    flex-direction: column;
  }
  .catalogo-titulo {
    color: #0d6efd;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
  }
  .catalogo-descripcion {
    text-align: center;
    color: #555;
    margin-bottom: 20px;
    font-size: 0.9rem;
  }
  .catalogo-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 15px;
  }
  .catalogo-form input {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    width: 100%;
  }
  .catalogo-form button {
    background-color: #0d6efd;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px;
    cursor: pointer;
    font-weight: 600;
  }
  .catalogo-form button:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
  }
  .resultados {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 20px;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 5px;
  }
  .resultados ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .resultados li {
    padding: 8px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .seleccionados {
    border-top: 2px solid #f0f0f0;
    padding-top: 15px;
    margin-bottom: 15px;
    max-height: 150px;
    overflow-y: auto;
  }
  .item-seleccionado {
    display: flex;
    gap: 5px;
    margin-bottom: 5px;
  }
  .item-seleccionado input[type="text"] {
    flex-grow: 1;
    background: #e9ecef;
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 4px;
    color: #333;
  }
  .btn-remover {
    background-color: #dc3545;
    color: white;
    border: none;
    width: 35px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
  }
  .agregar-btn {
    background-color: #198754;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.85rem;
  }
  .footer-acciones {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #eee;
  }
  .btn-guardar {
    background-color: #0d6efd;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 8px;
    width: 100%;
    font-size: 1rem;
    cursor: pointer;
    display: block;
  }
  .btn-guardar:hover {
    background-color: #0b5ed7;
  }
  .btn-guardar:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
  }
</style>
