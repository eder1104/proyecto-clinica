@extends('layouts.app')

@section('content')
<div class="catalogo-container">
  <div class="catalogo-card">
    <h4 class="catalogo-titulo"> Catálogos Clínicos Oftalmológicos</h4>
    <p class="catalogo-descripcion">
      maestro para listar disgnosticos procedimientos y alergias
    </p>

    <form id="formBuscarCatalogo" method="GET" action="{{ route('catalogos.buscar') }}" class="catalogo-form">
      <div class="campo">
        <select name="tipo" required>
          <option value="">Seleccionar tipo...</option>
          <option value="diagnostico">Diagnóstico</option>
          <option value="procedimiento">Procedimiento</option>
          <option value="alergia">Alergia</option>
        </select>
      </div>

      <div class="campo">
        <input type="text" name="termino" placeholder="Buscar término..." required>
      </div>

      <div class="campo">
        <button type="submit">Buscar 🔍</button>
      </div>
    </form>

    <div class="resultados">
      <h6>Resultados</h6>
      <ul id="resultados"></ul>
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
  min-height: 80vh;
}

.catalogo-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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

/* --- Formulario --- */
.catalogo-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.catalogo-form .campo {
  display: flex;
  flex-direction: column;
}
.catalogo-form select,
.catalogo-form input {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 0.95rem;
  transition: border 0.2s ease, box-shadow 0.2s ease;
}
.catalogo-form select:focus,
.catalogo-form input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.2);
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

/* --- Resultados --- */
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
  transition: background 0.2s ease;
}
.resultados li:hover {
  background-color: #f8f9fa;
}
</style>

<script>
document.getElementById('formBuscarCatalogo').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const params = new URLSearchParams(new FormData(form));

  const response = await fetch(form.action + '?' + params.toString());
  const data = await response.json();

  const lista = document.getElementById('resultados');
  lista.innerHTML = '';

  if (data.length === 0) {
    lista.innerHTML = '<li class="text-muted">No se encontraron resultados.</li>';
    return;
  }

  data.forEach(item => {
    const li = document.createElement('li');
    li.innerHTML = `<strong>${item.nombre}</strong>` + (item.codigo ? ` <span style="color:#888;">(${item.codigo})</span>` : '');
    lista.appendChild(li);
  });
});
</script>
@endsection
