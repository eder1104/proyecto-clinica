document.addEventListener('DOMContentLoaded', function() {
    const userDropdownBtn = document.querySelector('nav .sm\\:ms-6 button');
    const userDropdownContent = document.querySelector('nav .sm\\:ms-6 .absolute');

    if (userDropdownBtn && userDropdownContent) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = window.getComputedStyle(userDropdownContent).display === 'none';
            userDropdownContent.style.display = isHidden ? 'block' : 'none';
            userDropdownContent.style.opacity = isHidden ? '1' : '0';
        });

        document.addEventListener('click', function(e) {
            if (!userDropdownBtn.contains(e.target)) {
                userDropdownContent.style.display = 'none';
                userDropdownContent.style.opacity = '0';
            }
        });
    }
});

window.pacientesEncontrados = [];

window.mostrarSeccion = function (seccion, limpiar = false) {
    const divImportar = document.getElementById('seccion_importar');
    const divRegistro = document.getElementById('seccion_registro');
    const divResultados = document.querySelector('.contenedor_principal');

    divImportar.style.display = (seccion === 'importar') ? 'block' : 'none';
    divRegistro.style.display = (seccion === 'registro') ? 'block' : 'none';

    if (seccion === 'registro') {
        if (limpiar) {
            limpiarFormulario();
            divResultados.innerHTML = '';
        }
    }
}

window.limpiarFormulario = function () {
    const form = document.getElementById('frm_paciente');
    form.reset();

    const urlStore = document.getElementById('url_store').value;
    form.action = urlStore;

    const methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }

    document.getElementById('btn_guardar').value = "Registrar";
    document.getElementById('txt_paciente_hc').value = '';
    document.getElementById('cmb_plan').innerHTML = '<option value="">--Seleccione--</option>';
}

window.trim_cadena = function (input) {
    if (input && input.value) {
        input.value = input.value.trim();
    }
}

window.getPlanes = function (convenioId, tipo) {
    const selectPlan = (tipo === 'import') ? document.getElementById('cmb_plan_import') : document.getElementById('cmb_plan');
    const urlBase = document.getElementById('url_obtener_planes').value;

    if (!convenioId) {
        selectPlan.innerHTML = '<option value="">--Seleccione--</option>';
        return;
    }

    fetch(`${urlBase}/${convenioId}/planes`)
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">-- Seleccione el plan --</option>';
            data.forEach(plan => {
                options += `<option value="${plan.id}">${plan.nombre}</option>`;
            });
            selectPlan.innerHTML = options;
        })
        .catch(error => console.error('Error:', error));
}

window.buscar_paciente = function () {
    let termino = document.getElementById('txt_paciente_hc').value.trim();
    const contenedor = document.querySelector('.contenedor_principal');
    const urlBuscar = document.getElementById('url_buscar').value;

    contenedor.innerHTML = '<p style="padding:10px;">Buscando coincidencias...</p>';

    fetch(`${urlBuscar}?search=${termino}`)
        .then(response => response.json())
        .then(data => {
            window.pacientesEncontrados = data;

            if (data.length === 1) {
                seleccionarPaciente(0);
            } else if (data.length > 1) {
                document.getElementById('seccion_registro').style.display = 'none';

                let html = `
                    <div style="margin: 20px 0;">
                        <h3>Resultados de la búsqueda:</h3>
                        <table border="1" style="width:100%; border-collapse: collapse; background:white; font-size: 12px;">
                            <thead style="background: #e0e0e0; font-weight:bold;">
                                <tr>
                                    <th style="padding:8px;">Documento</th>
                                    <th style="padding:8px;">Nombre Completo</th>
                                    <th style="padding:8px;">Teléfono</th>
                                    <th style="padding:8px;">Email</th>
                                    <th style="padding:8px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.forEach((p, index) => {
                    html += `
                        <tr>
                            <td style="padding:8px; text-align:center;">${p.documento}</td>
                            <td style="padding:8px;">${p.nombres} ${p.apellidos}</td>
                            <td style="padding:8px; text-align:center;">${p.telefono || '-'}</td>
                            <td style="padding:8px;">${p.email || '-'}</td>
                            <td style="padding:8px; text-align:center;">
                                <input type="button" class="btnPrincipal" 
                                       value="Seleccionar" 
                                       onclick="seleccionarPaciente(${index})"
                                       style="cursor:pointer; padding: 5px 10px;">
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                contenedor.innerHTML = html;

            } else {
                contenedor.innerHTML = '<p style="padding:10px; color:red; font-weight:bold;">No se encontraron pacientes con ese criterio.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenedor.innerHTML = '<p style="color:red;">Error en la búsqueda</p>';
        });
}

window.seleccionarPaciente = function (index) {
    const paciente = window.pacientesEncontrados[index];
    if (paciente) {
        cargarDatosPaciente(paciente);
        document.querySelector('.contenedor_principal').innerHTML = '';
    }
}

window.validarImportarPacientes = function () {
    const fileInput = document.getElementById('fileISS');
    const convenioSelect = document.getElementById('cmb_convenio_import');
    const planSelect = document.getElementById('cmb_plan_import');
    const url = document.getElementById('url_importar').value;
    const token = document.getElementById('token_csrf').value;

    if (!convenioSelect.value) {
        alert("Seleccione un convenio");
        return;
    }

    if (!planSelect.value) {
        alert("Seleccione un plan para la importación");
        return;
    }

    if (fileInput.files.length === 0) {
        alert("Seleccione un archivo .csv");
        return;
    }

    let formData = new FormData();
    formData.append('fileISS', fileInput.files[0]);
    formData.append('cmb_convenio', convenioSelect.value);
    formData.append('cmb_plan', planSelect.value);
    formData.append('_token', token);

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.res == 1) {
                alert(data.mensaje);
                location.reload();
            } else {
                alert("Error: " + (data.error || "Ocurrió un error desconocido"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ocurrió un error al procesar el archivo");
        });
}

window.cargarDatosPaciente = function (paciente) {
    mostrarSeccion('registro', false);

    document.getElementById('btn_guardar').value = "Actualizar Datos";

    const form = document.getElementById('frm_paciente');
    form.action = `/legacy/pacientes/${paciente.id}`;

    let methodInput = document.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    } else {
        methodInput.value = 'PUT';
    }

    document.getElementById('cmb_tipo_documento').value = paciente.tipo_documento || "";
    document.getElementById('txt_numero_documento').value = paciente.documento || "";

    const nombresArr = (paciente.nombres || "").split(" ");
    document.getElementById('txt_nombre_1').value = nombresArr[0] || "";
    document.getElementById('txt_nombre_2').value = nombresArr.slice(1).join(" ") || "";

    const apellidosArr = (paciente.apellidos || "").split(" ");
    document.getElementById('txt_apellido_1').value = apellidosArr[0] || "";
    document.getElementById('txt_apellido_2').value = apellidosArr.slice(1).join(" ") || "";

    document.getElementById('txt_fecha_nacimiento').value = paciente.fecha_nacimiento ? paciente.fecha_nacimiento.split(' ')[0] : "";

    let sexoVal = "";
    if (paciente.sexo === 'F') sexoVal = "1";
    if (paciente.sexo === 'M') sexoVal = "2";
    document.getElementById('cmb_sexo').value = sexoVal;

    document.getElementById('txt_telefono').value = paciente.telefono || "";
    document.getElementById('txt_email').value = paciente.email || "";
    document.getElementById('txt_direccion').value = paciente.direccion || "";

    document.getElementById('cmb_pais_nac').value = paciente.pais_nacimiento_cod || "";
    document.getElementById('cmb_pais_res').value = paciente.pais_residencia_cod || "1";
    document.getElementById('txt_estado_residencia').value = paciente.estado_residencia || "";
    document.getElementById('txt_municipio_residencia').value = paciente.municipio_residencia || "";
    document.getElementById('cmb_zona_residencia').value = paciente.zona_residencia || "";

    if (paciente.convenio_id) {
        document.getElementById('cmb_convenio').value = paciente.convenio_id;
        getPlanes(paciente.convenio_id, 'registro');
        setTimeout(() => {
            if (paciente.plan_id) {
                document.getElementById('cmb_plan').value = paciente.plan_id;
            }
        }, 500);
    }

    document.getElementById('cmb_tipoUsuario').value = paciente.tipo_usuario || "";
    document.getElementById('cmb_rango').value = paciente.rango || "";
    document.getElementById('cmb_estado_aseguradora').value = paciente.estado_aseguradora || "";
    document.getElementById('cmb_exento_cuota').value = (paciente.exento_cuota == 1 || paciente.exento_cuota == 'Si') ? "1" : "2";

    document.getElementById('txt_observ_paciente').value = paciente.observaciones || "";
}