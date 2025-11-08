<div class="catalogo-buscador" data-tipo-catalogo="{{ $tipo_catalogo ?? 'diagnostico' }}">
    <label class="form-label mb-2">Buscar {{ $titulo_catalogo ?? 'Elemento' }}:</label>

    <div class="input-group mb-3">
        <input type="text" class="form-control catalogo-input-termino" placeholder="Buscar por nombre o código">
        <button class="btn btn-primary catalogo-btn-buscar" type="button">Buscar</button>
    </div>

    <div class="catalogo-resultados-container mt-3">
        <table class="table table-sm table-hover d-none catalogo-tabla-resultados">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <p class="catalogo-no-resultados d-none text-muted">No se encontraron resultados.</p>
    </div>

    <div class="catalogo-seleccionados mt-3 border p-2 rounded d-none">
        <p class="mb-1 fw-bold">Seleccionados:</p>
        <ul class="list-group list-group-flush catalogo-lista-seleccionados">

        </ul>
    </div>

    <input type="hidden" name="{{ $nombre_input ?? 'catalogo_ids' }}" class="catalogo-input-ids">
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catalogoBuscadores = document.querySelectorAll('.catalogo-buscador');

        catalogoBuscadores.forEach(buscador => {
            const tipoCatalogo = buscador.dataset.tipoCatalogo;
            const inputTermino = buscador.querySelector('.catalogo-input-termino');
            const btnBuscar = buscador.querySelector('.catalogo-btn-buscar');
            const tablaResultados = buscador.querySelector('.catalogo-tabla-resultados');
            const tbody = tablaResultados.querySelector('tbody');
            const inputHidden = buscador.querySelector('.catalogo-input-ids');
            const listaSeleccionados = buscador.querySelector('.catalogo-lista-seleccionados');
            const resultadosContainer = buscador.querySelector('.catalogo-resultados-container');
            const seleccionadosContainer = buscador.querySelector('.catalogo-seleccionados');
            const pNoResultados = buscador.querySelector('.catalogo-no-resultados');

            let selectedItems = JSON.parse(inputHidden.value || '[]');

            function renderSeleccionados() {
                listaSeleccionados.innerHTML = '';
                if (selectedItems.length > 0) {
                    seleccionadosContainer.classList.remove('d-none');

                    selectedItems.forEach(item => {
                        const li = document.createElement('li');
                        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center', 'py-1');
                        li.innerHTML = `
                        <span>${item.nombre} ${item.codigo ? `(${item.codigo})` : ''}</span>
                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-seleccion" data-id="${item.id}">×</button>
                    `;
                        listaSeleccionados.appendChild(li);
                    });
                } else {
                    seleccionadosContainer.classList.add('d-none');
                }
                inputHidden.value = JSON.stringify(selectedItems.map(item => item.id));
            }

            async function buscarElementos() {
                const termino = inputTermino.value.trim();
                if (termino.length < 3) return;

                pNoResultados.classList.add('d-none');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">Buscando...</td></tr>';
                tablaResultados.classList.remove('d-none');

                try {
                    const url = `/catalogos/buscar?tipo=${tipoCatalogo}&termino=${termino}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        pNoResultados.classList.remove('d-none');
                        tablaResultados.classList.add('d-none');
                        return;
                    }

                    data.forEach(item => {
                        const fila = document.createElement('tr');

                        const isSelected = selectedItems.some(si => si.id === item.id);
                        const btnHtml = isSelected ?
                            '<button type="button" class="btn btn-sm btn-secondary" disabled>Añadido</button>' :
                            `<button type="button" class="btn btn-sm btn-success btn-seleccionar" data-id="${item.id}" data-nombre="${item.nombre}" data-codigo="${item.codigo ?? ''}">Añadir</button>`;

                        fila.innerHTML = `
                        <td>${item.nombre}</td>
                        <td>${item.codigo ?? 'N/A'}</td>
                        <td>${btnHtml}</td>
                    `;
                        tbody.appendChild(fila);
                    });

                    tablaResultados.classList.remove('d-none');
                } catch (error) {

                    tbody.innerHTML = '<tr><td colspan="3" class="text-danger text-center">Error al cargar datos.</td></tr>';
                }
            }

            btnBuscar.addEventListener('click', buscarElementos);
            inputTermino.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarElementos();
                }
            });

            resultadosContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-seleccionar')) {
                    const btn = e.target;
                    const newItem = {
                        id: parseInt(btn.dataset.id),
                        nombre: btn.dataset.nombre,
                        codigo: btn.dataset.codigo
                    };

                    if (tipoCatalogo === 'diagnostico') {
                        selectedItems = [newItem];
                    } else {
                        selectedItems.push(newItem);
                    }

                    renderSeleccionados();
                    buscarElementos();
                }
            });

            listaSeleccionados.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-eliminar-seleccion')) {
                    const idToRemove = parseInt(e.target.dataset.id);
                    selectedItems = selectedItems.filter(item => item.id !== idToRemove);
                    renderSeleccionados();

                    if (!tablaResultados.classList.contains('d-none')) {
                        buscarElementos();
                    }
                }
            });

            renderSeleccionados();
        });
    });
</script>
@endpush