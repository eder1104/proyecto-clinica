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
                            <td colspan="3">
                                <h4>Panel de Control</h4>
                            </td>
                        </tr>
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
                                    onclick="mostrarSeccion('registro');">
                            </td>
                            <td>
                                <input type="button" class="btnPrincipal" value="Importar"
                                    onclick="mostrarSeccion('importar');">
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

                    <div style="text-align: left;margin-bottom: 40px;">
                        <ul>
                            <li>Los siguientes planes de convenios han sido parametrizados:
                                <ul>
                                    <li><strong>Nueva EPS</strong>, resolución 14. del 19/12/2018</li>
                                    <li><strong>Avanzar FOS</strong>, resolución 29. del 12/09/2018</li>
                                </ul>
                            </li>
                        </ul>
                    </div>

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
                                    <select class="select" style="width: 200px;" id="cmb_convenio" name="cmb_convenio"
                                        onchange="getPlanes(this.value);">
                                        <option value="">Seleccione el Convenio</option>
                                        <option value="62">ALLIANZ SAN GIL</option>
                                        <option value="59">CONVENIO UT - SAN GIL</option>
                                        <option value="66">COOPERATIVA DE CAFICULTORES</option>
                                        <option value="61">ECOPETROL - SAN GIL</option>
                                        <option value="63">FAMISANAR UT</option>
                                        <option value="49">FOSCAL - AVANZAR UT</option>
                                        <option value="48">FOSCAL - NUEVA EPS UT</option>
                                        <option value="58">PARTICULAR - SAN GIL</option>
                                        <option value="64">PARTICULAR UT</option>
                                        <option value="60">SURAMERICANA - SAN GIL</option>
                                        <option value="65">UIS UT</option>
                                    </select>
                                </td>
                                <td style="text-align: left;">
                                    <div id="div_plan">
                                        <select class="select" style="width: 200px;" id="cmb_plan" name="cmb_plan">
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

                    <div id="resultadoImportarISS"></div>
                    <div id="d_barra_progreso_adj" class="div_barra_progreso" style="display:none; margin:auto; width:50%">
                        <div id="d_barra_progreso_adj_int" class="div_barra_progreso_int" style="width:1%;">1%</div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div id="seccion_registro" style="display: none;">
            <hr class="section-divider">

            <form id="patientForm" action="{{ route('pacientes.store') }}" method="POST">
                @csrf

                <div class="search-section">
                    <div class="form-group">
                        <label>Tipo de Identificación<span class="required">*</span></label>
                        <select id="tipoId" name="tipo_id" required>
                            <option value="">Seleccione el tipo</option>
                            <option value="cc">Cédula de Ciudadanía</option>
                            <option value="ti">Tarjeta de Identidad</option>
                            <option value="ce">Cédula de Extranjería</option>
                            <option value="pa">Pasaporte</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. de identificación<span class="required">*</span></label>
                        <input type="text" id="numeroId" name="numero_id" required>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn-primary" onclick="buscarPaciente()">Buscar paciente</button>
                    </div>
                </div>

                <hr class="section-divider">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Primer nombre<span class="required">*</span></label>
                        <input type="text" id="primerNombre" name="primer_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Segundo nombre</label>
                        <input type="text" id="segundoNombre" name="segundo_nombre">
                    </div>
                    <div class="form-group">
                        <label>Primer apellido<span class="required">*</span></label>
                        <input type="text" id="primerApellido" name="primer_apellido" required>
                    </div>
                    <div class="form-group">
                        <label>Segundo apellido</label>
                        <input type="text" id="segundoApellido" name="segundo_apellido">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Género<span class="required">*</span></label>
                        <select id="genero" name="genero" required>
                            <option value="">Seleccione el sexo</option>
                            <option value="m">Masculino</option>
                            <option value="f">Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de nacimiento<span class="required">*</span></label>
                        <input type="date" id="fechaNacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Sangre<span class="required">*</span></label>
                        <select id="tipoSangre" name="tipo_sangre" required>
                            <option value="">Seleccione el tipo</option>
                            <option value="a+">A+</option>
                            <option value="a-">A-</option>
                            <option value="b+">B+</option>
                            <option value="b-">B-</option>
                            <option value="ab+">AB+</option>
                            <option value="ab-">AB-</option>
                            <option value="o+">O+</option>
                            <option value="o-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Factor RH<span class="required">*</span></label>
                        <select id="factorRH" name="factor_rh" required>
                            <option value="">Seleccione el factor</option>
                            <option value="+">Positivo (+)</option>
                            <option value="-">Negativo (-)</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>País de nacimiento<span class="required">*</span></label>
                        <select id="paisNacimiento" name="pais_nacimiento" required>
                            <option value="co">Colombia</option>
                            <option value="ve">Venezuela</option>
                            <option value="ec">Ecuador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Departamento de nacimiento<span class="required">*</span></label>
                        <select id="deptoNacimiento" name="depto_nacimiento" required>
                            <option value="santander">Santander</option>
                            <option value="antioquia">Antioquia</option>
                            <option value="cundinamarca">Cundinamarca</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Municipio de nacimiento<span class="required">*</span></label>
                        <select id="municipioNacimiento" name="municipio_nacimiento" required>
                            <option value="">Seleccione el municipio</option>
                            <option value="bucaramanga">Bucaramanga</option>
                            <option value="floridablanca">Floridablanca</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>País de residencia<span class="required">*</span></label>
                        <select id="paisResidencia" name="pais_residencia" required>
                            <option value="co">Colombia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Departamento de residencia<span class="required">*</span></label>
                        <select id="deptoResidencia" name="depto_residencia" required>
                            <option value="santander">Santander</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Municipio de residencia<span class="required">*</span></label>
                        <select id="municipioResidencia" name="municipio_residencia" required>
                            <option value="">Seleccione el municipio</option>
                            <option value="bucaramanga">Bucaramanga</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group span-2">
                        <label>Dirección<span class="required">*</span></label>
                        <input type="text" id="direccion" name="direccion" required>
                    </div>
                    <div class="form-group">
                        <label>Zona<span class="required">*</span></label>
                        <select id="zona" name="zona" required>
                            <option value="urbana">Urbana</option>
                            <option value="rural">Rural</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Teléfono 1<span class="required">*</span></label>
                        <input type="text" id="telefono1" name="telefono1" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Teléfono 2</label>
                        <input type="text" id="telefono2" name="telefono2">
                    </div>
                    <div class="form-group">
                        <label>e-mail<span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Profesión<span class="required">*</span></label>
                        <input type="text" id="profesion" name="profesion" required>
                    </div>
                    <div class="form-group">
                        <label>Estado civil<span class="required">*</span></label>
                        <select id="estadoCivil" name="estado_civil" required>
                            <option value="">Seleccione el estado civil</option>
                            <option value="soltero">Soltero(a)</option>
                            <option value="casado">Casado(a)</option>
                            <option value="union">Unión libre</option>
                            <option value="viudo">Viudo(a)</option>
                            <option value="divorciado">Divorciado(a)</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Etnia</label>
                        <select id="etnia" name="etnia">
                            <option value="ninguna">Ninguna</option>
                            <option value="indigena">Indígena</option>
                            <option value="afro">Afrocolombiano</option>
                            <option value="rom">ROM</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de hijos<span class="required">*</span></label>
                        <select id="numeroHijos" name="numero_hijos" required>
                            <option value="">Seleccione</option>
                            @for ($i = 0; $i <= 25; $i++)
                            <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de hijas<span class="required">*</span></label>
                        <select id="numeroHijas" name="numero_hijas" required>
                            <option value="">Seleccione</option>
                            @for ($i = 0; $i <= 25; $i++)
                            <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de hermanos<span class="required">*</span></label>
                        <select id="numeroHermanos" name="numero_hermanos" required>
                            <option value="">Seleccione</option>
                            @for ($i = 0; $i <= 25; $i++)
                            <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Número de hermanas<span class="required">*</span></label>
                        <select id="numeroHermanas" name="numero_hermanas" required>
                            <option value="">Seleccione</option>
                            @for ($i = 0; $i <= 25; $i++)
                            <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Desplazado</label>
                        <select id="desplazado" name="desplazado">
                            <option value="no">No</option>
                            <option value="si">Sí</option>
                        </select>
                    </div>
                </div>

                <hr class="section-divider">

                <div class="button-group">
                    <button type="button" class="btn-primary">Abrir cámara</button>
                    <button type="button" class="btn-secondary">Datos adicionales</button>
                    <button type="submit" class="btn-primary" style="background-color: #10b981;">Guardar paciente</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarSeccion(seccion) {
            const divImportar = document.getElementById('seccion_importar');
            const divRegistro = document.getElementById('seccion_registro');

            if (seccion === 'importar') {
                divImportar.style.display = 'block';
                divRegistro.style.display = 'none';
            } else if (seccion === 'registro') {
                divImportar.style.display = 'none';
                divRegistro.style.display = 'block';
            }
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.span-2 {
            grid-column: span 2;
        }

        .form-group.span-3 {
            grid-column: span 3;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
        }

        label .required {
            color: #ef4444;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            color: #1f2937;
            background-color: white;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        button,
        .btnPrincipal {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btnPrincipal {
            background-color: #0ea5e9;
            color: white;
        }

        .btn-primary,
        .btn-secondary {
            background-color: #0ea5e9;
            color: white;
        }

        .btn-primary:hover,
        .btn-secondary:hover,
        .btnPrincipal:hover {
            background-color: #0284c7;
        }

        .search-section {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 16px;
            align-items: end;
            margin-bottom: 32px;
        }

        .section-divider {
            margin: 32px 0;
            border: 0;
            border-top: 1px solid #e5e7eb;
        }

        .div_barra_progreso {
            border: 1px solid #ccc;
            background: #eee;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .div_barra_progreso_int {
            background: #4caf50;
            height: 100%;
            text-align: center;
            color: white;
            font-size: 12px;
            line-height: 20px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.span-2 {
                grid-column: span 1;
            }

            .search-section {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 20px;
            }
        }
    </style>
@endsection