@extends('layouts.legacy-hc')

@section('content')
    <div class="container">
        <h1 class="title">Buscar paciente</h1>

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
                                    placeholder="Buscar por nombre, documento o teléfono del paciente"
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
                                    <input type="button" value="Importar" onclick="validarImportarPacientes();"
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

    <input type="hidden" id="url_importar" value="{{ route('legacy.pacientes.importar') }}">
    <input type="hidden" id="token_csrf" value="{{ csrf_token() }}">
@endsection