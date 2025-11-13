@php($ocultarMenu = true)

@if (empty($ocultarMenu))
    @include('layouts.navigation')
@endif

<div class="catalogo-container">
  <div class="catalogo-card">
    <h4 class="catalogo-titulo">Catálogos Clínicos Oftalmológicos</h4>
    <p class="catalogo-descripcion">
      Maestro para listar diagnósticos, procedimientos y alergias
    </p>

    <div id="formBuscarCatalogo" class="catalogo-form">
      <div class="campo">
        <input type="text" id="termino" name="termino" placeholder="Buscar por nombre..." required>
      </div>

      <div class="campo">
        <button type="button" id="btnBuscarCatalogo">Buscar 🔍</button>
      </div>
    </div>

    <div class="resultados">
      <h6>Resultados</h6>
      <ul id="resultados"></ul>
    </div>

    <div class="seleccionados">
      <h6>Seleccionados</h6>
      <div id="contenedorSeleccionados"></div>
    </div>
  </div>
</div>

<style>
  .catalogo-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    background-color: #f4f6f9;
    min-height: 10;
  }

  .catalogo-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 30px;
    max-width: 600px;
    width: 100%;
    text-align: center;
    transition: transform 0.2s ease;
  }

  .catalogo-card:hover {
    transform: translateY(-3px);
  }

  .catalogo-titulo {
    margin-bottom: 10px;
    color: #0d6efd;
    font-weight: 700;
  }

  .catalogo-descripcion {
    color: #555;
    font-size: 0.95rem;
    margin-bottom: 25px;
  }

  .catalogo-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .catalogo-form .campo {
    display: flex;
    flex-direction: column;
  }

  .catalogo-form input {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 0.95rem;
    transition: border 0.2s ease, box-shadow 0.2s ease;
  }

  .catalogo-form input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
  }

  .catalogo-form button {
    background-color: #0d6efd;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 0;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s ease;
  }

  .catalogo-form button:hover {
    background-color: #0b5ed7;
  }

  .resultados {
    margin-top: 30px;
    text-align: left;
  }

  .resultados h6 {
    font-weight: 700;
    margin-bottom: 8px;
  }

  .resultados ul {
    list-style: none;
    padding: 0;
    margin: 0;
    border-top: 1px solid #eee;
  }

  .resultados li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .resultados li:hover {
    background-color: #f8f9fa;
  }

  .resultados button {
    background-color: #198754;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 5px 10px;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .resultados button:hover {
    background-color: #157347;
  }

  .seleccionados {
    margin-top: 30px;
    text-align: left;
  }

  .seleccionados h6 {
    font-weight: 700;
    margin-bottom: 10px;
  }

  .seleccionados input {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px 12px;
    background-color: #f8f9fa;
    cursor: not-allowed;
    margin-bottom: 8px;
  }
</style>

<script>
  const btnBuscar = document.getElementById('btnBuscarCatalogo');
  const lista = document.getElementById('resultados');
  const contenedorSeleccionados = document.getElementById('contenedorSeleccionados');

  btnBuscar.addEventListener('click', async () => {
    const termino = document.getElementById('termino').value.trim();
    if (!termino) return;

    const response = await fetch(`{{ route('catalogos.buscar') }}?termino=${encodeURIComponent(termino)}`);
    const data = await response.json();

    lista.innerHTML = '';

    if (data.length === 0) {
      lista.innerHTML = '<li class="text-muted">No se encontraron resultados.</li>';
      return;
    }

    data.forEach(item => {
      const li = document.createElement('li');
      const tipo = item.tipo ? `<span style="color:#0d6efd;">[${item.tipo}]</span> ` : '';
      const texto = `${tipo}<strong>${item.nombre}</strong>` + (item.codigo ? ` <span style="color:#888;">(${item.codigo})</span>` : '');
      li.innerHTML = `<span>${texto}</span><button type="button" class="agregar-btn">Agregar</button>`;

      li.querySelector('.agregar-btn').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'text';
        input.readOnly = true;
        input.value = item.nombre + (item.codigo ? ` (${item.codigo})` : '');
        contenedorSeleccionados.appendChild(input);
      });

      lista.appendChild(li);
    });
  });
</script>
