<div class="examenes-container">
    <h2 class="titulo-seccion">Profesional que atiende: {{$users -> $nombre}}</h2>
    <!-- {{ optional($c->admisiones)->nombres ?? 'N/A' }} -->

    <div class="tabs">
        <button class="tab active" data-tab="examenes">Exámenes</button>
        <button class="tab" data-tab="diagnosticos">Diagnósticos</button>
    </div>

    <div id="examenes" class="tab-content active">
        <div class="campo">
            <label for="tipoExamen">Tipo de examen</label>
            <select id="tipoExamen">
                <option>Agudeza Visual</option>
                <option>Refracción</option>
                <option>Fondo de Ojo</option>
                <option>Biomicroscopía</option>
                <option>Test de Visión Cromática</option>
            </select>
        </div>

        <div class="campo">
            <label for="ojo">Ojo</label>
            <select id="ojo">
                <option>Ojo Derecho</option>
                <option>Ojo Izquierdo</option>
            </select>
        </div>

        <div class="campo">
            <label for="archivo">Cargar archivos (PDF)</label>
            <input type="file" id="archivo" accept="application/pdf">
        </div>

        <div class="campo">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" rows="4"></textarea>
        </div>
    </div>

    <div id="diagnosticos" class="tab-content">
        <h3 class="titulo-seccion">OJO</h3>

        <div class="campo">
            <label for="ojoDiag">Seleccione el ojo</label>
            <select id="ojoDiag">
                <option>Ojo Derecho</option>
                <option>Ojo Izquierdo</option>
            </select>
        </div>

        <table class="tabla-diagnosticos">
            <thead>
                <tr>
                    <th>Código CIEX</th>
                    <th>Diagnóstico</th>
                    <th>Ojo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select>
                            <option>H52.1 - Miopía</option>
                            <option>H52.2 - Hipermetropía</option>
                            <option>H52.3 - Astigmatismo</option>
                            <option>H52.4 - Presbicia</option>
                            <option>H53.0 - Ambliopía</option>
                            <option>H50.0 - Estrabismo</option>
                            <option>H53.1 - Deficiencia cromática</option>
                            <option>H10.0 - Conjuntivitis</option>
                            <option>H57.0 - Dolor ocular</option>
                            <option>H54.0 - Ceguera total</option>
                        </select>
                    </td>
                    <td><input type="text" placeholder="Diagnóstico específico"></td>
                    <td>
                        <select>
                            <option>Ojo Derecho</option>
                            <option>Ojo Izquierdo</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.examenes-container {
    margin-top: 2rem;
    padding: 2rem;
    background: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.titulo-seccion {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 1rem;
}

.tabs {
    display: flex;
    border-bottom: 2px solid #d1d5db;
    margin-bottom: 1.5rem;
}

.tab {
    flex: 1;
    text-align: center;
    padding: 0.75rem 1rem;
    cursor: pointer;
    background: #f3f4f6;
    border: none;
    font-weight: 500;
    color: #374151;
    transition: background 0.2s ease;
}

.tab.active {
    background: #2563eb;
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.campo {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.campo label {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.5rem;
}

.campo select,
.campo input[type="file"],
.campo textarea,
.campo input[type="text"] {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem;
    font-size: 1rem;
    outline: none;
}

.campo select:focus,
.campo textarea:focus,
.campo input:focus {
    border-color: #2563eb;
}

.tabla-diagnosticos {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.tabla-diagnosticos th,
.tabla-diagnosticos td {
    border: 1px solid #d1d5db;
    padding: 0.75rem;
    text-align: left;
}

.tabla-diagnosticos th {
    background: #f3f4f6;
    color: #111827;
    font-weight: 600;
}
</style>

<script>
const tabs = document.querySelectorAll('.tab');
const contents = document.querySelectorAll('.tab-content');
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});
</script>
