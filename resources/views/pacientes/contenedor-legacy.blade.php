@extends('layouts.legacy-hc')

@section('content')
    <div class="container">
        <h1>Datos del paciente</h1>

        <div class="contenedor_principal">
            <div id="guardar_historia_clinica" style="width:100%;">
                <div class="contenedor_error" id="contenedor_error"></div>
                <div class="contenedor_exito" id="contenedor_exito"></div>
            </div>

            <div class="formulario" id="principal_historia_clinica"
                style="width: 100%; display: block; margin-bottom: 30px;">
                <table border="0" style="width:90%; margin: 0 auto;">
                    <tbody>
                        <tr>
                            <td style="width:70%; text-align:right;">
                                <input type="text" id="txt_paciente_hc"
                                    placeholder="Buscar por nombre, documento o teléfono..."
                                    style="width:100%; float:right; padding:8px; border:1px solid #ccc; border-radius:4px;"
                                    onblur="trim_cadena(this);">
                            </td>
                            <td style="text-align:center;">
                                <input type="button" class="btnPrincipal" value="Buscar" onclick="buscar_paciente();">
                            </td>
                            <td style="text-align:center;">
                                <input type="button" class="btnPrincipal" value="Nuevo"
                                    onclick="mostrarSeccion('registro', true);">
                            </td>
                            <td>
                                <input type="button" class="btnPrincipal" value="Importar"
                                    onclick="mostrarSeccion('importar', false);">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="seccion_importar" style="display: none;">
            <div id="d_contenedor_paciente_hc"
                style="min-height: 30px; padding: 30px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                <fieldset style="width: 90%; margin: auto; border: 1px solid #ccc; padding: 20px;">
                    <legend style="font-weight:bold;">Importar pacientes:</legend>

                    <table border="0" style="width:100%; margin: 0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: left;"><label>Convenio o aseguradora*</label></td>
                                <td style="text-align: left;"><label>Plan*</label></td>
                                <td style="text-align: left;"><label>Seleccione el archivo (.csv)*</label></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">
                                    <select class="select" style="width: 200px;" id="cmb_convenio_import"
                                        name="cmb_convenio" onchange="getPlanes(this.value, 'import');">
                                        <option value="">Seleccione el Convenio</option>
                                        @foreach($convenios as $convenio)
                                            <option value="{{ $convenio->id }}">{{ $convenio->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="text-align: left;">
                                    <div id="div_plan_import">
                                        <select class="select" style="width: 200px;" id="cmb_plan_import" name="cmb_plan">
                                            <option value="">-- Seleccione el plan --</option>
                                        </select>
                                    </div>
                                </td>
                                <td style="text-align: left;">
                                    <input type="file" id="fileISS" name="fileISS" accept=".csv">
                                </td>
                                <td>
                                    <input type="button" value="Ejecutar Importación" onclick="validarImportarPacientes();"
                                        class="btnPrincipal">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>

        <div id="seccion_registro" style="display: none;">
            <div style="min-height: 30px; padding: 30px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                <fieldset style="width: 90%; margin: auto; border: 1px solid #ccc; padding: 15px;">
                    <legend style="font-weight:bold;">Datos del paciente:</legend>

                    <form id="frm_paciente" name="frm_paciente" action="{{ route('legacy.pacientes.store') }}"
                        method="POST"> @csrf
                        <table border="0"
                            style="width: 100%; margin: auto; font-size: 10pt; border-collapse: separate; border-spacing: 5px;">
                            <tbody>
                                <tr>
                                    <td align="left" style="width:25%;"><label>Tipo de identificación*</label></td>
                                    <td align="left" style="width:25%;"><label>Número de identificación*</label></td>
                                    <td align="center" colspan="2" rowspan="2" valign="top"><b>Código de verificación <br>
                                        </b></td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_tipo_documento"
                                            name="cmb_tipo_documento">
                                            <option value="">-- Seleccione --</option>
                                            <option value="3">Registro civil</option>
                                            <option value="4">Tarjeta de identidad</option>
                                            <option value="5">Cédula de ciudadanía</option>
                                            <option value="6">Cédula de extranjería</option>
                                            <option value="7">Pasaporte</option>
                                            <option value="463">Permiso especial</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <input type="text" id="txt_numero_documento" name="txt_numero_documento"
                                            maxlength="20" style="width:100%;">
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left"><label>Primer nombre*</label></td>
                                    <td align="left"><label>Segundo nombre</label></td>
                                    <td align="left"><label>Primer apellido*</label></td>
                                    <td align="left"><label>Segundo apellido</label></td>
                                </tr>
                                <tr>
                                    <td align="left"><input type="text" id="txt_nombre_1" name="txt_nombre_1"
                                            maxlength="100" style="width:100%;"></td>
                                    <td align="left"><input type="text" id="txt_nombre_2" name="txt_nombre_2"
                                            maxlength="100" style="width:100%;"></td>
                                    <td align="left"><input type="text" id="txt_apellido_1" name="txt_apellido_1"
                                            maxlength="100" style="width:100%;"></td>
                                    <td align="left"><input type="text" id="txt_apellido_2" name="txt_apellido_2"
                                            maxlength="100" style="width:100%;"></td>
                                </tr>

                                <tr>
                                    <td align="left"><label>Género*</label></td>
                                    <td align="left"><label>Fecha de nacimiento*</label></td>
                                    <td align="left"><label>País de nacimiento*</label></td>
                                    <td align="left"><label>Teléfono*</label></td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_sexo" name="cmb_sexo">
                                            <option value="">-- Seleccione --</option>
                                            <option value="1">Femenino</option>
                                            <option value="2">Masculino</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <input type="date" class="input required" name="txt_fecha_nacimiento"
                                            id="txt_fecha_nacimiento" style="width:100%;">
                                    </td>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_pais_nac" name="cmb_pais_nac">
                                            <option value="">-- Seleccione --</option>
                                            <option value="1">Colombia</option>
                                            <option value="189">Venezuela</option>
                                            <option value="52">Ecuador</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <input type="text" id="txt_telefono" name="txt_telefono" maxlength="20"
                                            style="width:100%;">
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left"><label>País de residencia*</label></td>
                                    <td align="left"><label>Email</label></td>
                                    <td align="left" colspan="2"><label>Dirección*</label></td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_pais_res" name="cmb_pais_res">
                                            <option value="1">Colombia</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <input type="email" id="txt_email" name="txt_email" maxlength="100"
                                            style="width:100%;">
                                    </td>
                                    <td align="left" colspan="2">
                                        <input type="text" id="txt_direccion" name="txt_direccion" maxlength="200"
                                            style="width:100%;">
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left"><label>Convenio/Entidad*</label></td>
                                    <td align="left"><label>Plan*</label></td>
                                    <td align="left"><label>Rango*</label></td>
                                    <td align="left"><label>Tipo de usuario*</label></td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_convenio" name="cmb_convenio"
                                            onchange="getPlanes(this.value, 'registro');">
                                            <option value="">--Seleccione--</option>
                                            @foreach($convenios as $convenio)
                                                <option value="{{ $convenio->id }}">{{ $convenio->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_plan" name="cmb_plan">
                                            <option value="">--Seleccione--</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_rango" name="cmb_rango">
                                            <option value="0">No aplica</option>
                                            <option value="1">Rango A</option>
                                            <option value="2">Rango B</option>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <select class="select" style="width:100%;" id="cmb_tipoUsuario"
                                            name="cmb_tipoUsuario">
                                            <option value="613">Contributivo</option>
                                            <option value="616">Subsidiado</option>
                                            <option value="620">Particular</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left" colspan="4"><label>Observaciones</label></td>
                                </tr>
                                <tr>
                                    <td align="left" colspan="4">
                                        <textarea id="txt_observ_paciente" name="txt_observ_paciente"
                                            style="width:100%; height:75px;"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <br>
                        <div id="d_btn_guardar_paciente" style="text-align:center;">
                            <input type="submit" id="btn_guardar" value="Registrar" class="btnPrincipal">
                        </div>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>

    <script>
        function mostrarSeccion(seccion, limpiar = false) {
            const divImportar = document.getElementById('seccion_importar');
            const divRegistro = document.getElementById('seccion_registro');
            divImportar.style.display = (seccion === 'importar') ? 'block' : 'none';
            divRegistro.style.display = (seccion === 'registro') ? 'block' : 'none';

            if (seccion === 'registro' && limpiar) {
                limpiarFormulario();
            }
        }

        function limpiarFormulario() {
            document.getElementById('frm_paciente').reset();
            document.getElementById('txt_paciente_hc').value = '';
            document.getElementById('cmb_plan').innerHTML = '<option value="">--Seleccione--</option>';
        }

        function trim_cadena(input) {
            if (input && input.value) {
                input.value = input.value.trim();
            }
        }

        function getPlanes(convenioId, tipo) {
            const selectPlan = (tipo === 'import') ? document.getElementById('cmb_plan_import') : document.getElementById('cmb_plan');

            if (!convenioId) {
                selectPlan.innerHTML = '<option value="">--Seleccione--</option>';
                return;
            }

            fetch(`/convenios/${convenioId}/planes`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">-- Seleccione el plan --</option>';
                    data.forEach(plan => {
                        options += `<option value="${plan.id}">${plan.nombre}</option>`;
                    });
                    selectPlan.innerHTML = options;
                })
                .catch(error => console.error('Error cargando planes:', error));
        }

        function buscar_paciente() {
            let termino = document.getElementById('txt_paciente_hc').value.trim();

            if (termino.length < 3) {
                return;
            }

            fetch(`/legacy/buscar?search=${termino}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        cargarDatosPaciente(data[0]);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function cargarDatosPaciente(paciente) {
            mostrarSeccion('registro', false);

            document.getElementById('cmb_tipo_documento').value = paciente.tipo_documento || "";
            document.getElementById('txt_numero_documento').value = paciente.documento || "";

            const nombresArr = (paciente.nombres || "").split(" ");
            document.getElementById('txt_nombre_1').value = nombresArr[0] || "";
            document.getElementById('txt_nombre_2').value = nombresArr.slice(1).join(" ") || "";

            const apellidosArr = (paciente.apellidos || "").split(" ");
            document.getElementById('txt_apellido_1').value = apellidosArr[0] || "";
            document.getElementById('txt_apellido_2').value = apellidosArr.slice(1).join(" ") || "";

            document.getElementById('txt_fecha_nacimiento').value = paciente.fecha_nacimiento || "";

            let sexoVal = "";
            if (paciente.sexo === 'F') sexoVal = "1";
            if (paciente.sexo === 'M') sexoVal = "2";
            document.getElementById('cmb_sexo').value = sexoVal;

            // --- Nuevos Campos ---
            document.getElementById('txt_telefono').value = paciente.telefono || "";
            document.getElementById('txt_email').value = paciente.email || "";
            document.getElementById('txt_direccion').value = paciente.direccion || "";

            document.getElementById('cmb_pais_nac').value = paciente.pais_nacimiento_cod || "";
            document.getElementById('cmb_pais_res').value = paciente.pais_residencia_cod || "1";

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
            document.getElementById('txt_observ_paciente').value = paciente.observaciones || "";
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 32px;
            color: #1a1a1a;
        }

        .select,
        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }

        .btnPrincipal {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            background-color: #0ea5e9;
            color: white;
            transition: 0.2s;
        }

        .btnPrincipal:hover {
            background-color: #0284c7;
        }
    </style>
@endsection