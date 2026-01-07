window.pacientesEncontrados=[];window.mostrarSeccion=function(e,n=!1){console.log(`--> Mostrando sección: ${e}`);const t=document.getElementById("seccion_importar"),c=document.getElementById("seccion_registro"),l=document.querySelector(".contenedor_principal");t.style.display=e==="importar"?"block":"none",c.style.display=e==="registro"?"block":"none",e==="registro"&&n&&(limpiarFormulario(),l.innerHTML="")};window.limpiarFormulario=function(){document.getElementById("frm_paciente").reset(),document.getElementById("txt_paciente_hc").value="",document.getElementById("cmb_plan").innerHTML='<option value="">--Seleccione--</option>'};window.trim_cadena=function(e){e&&e.value&&(e.value=e.value.trim())};window.getPlanes=function(e,n){};window.buscar_paciente=function(){console.log("PASO 1: Función buscar_paciente ejecutada.");let e=document.getElementById("txt_paciente_hc").value.trim();console.log("PASO 2: Término capturado:",e);const n=document.querySelector(".contenedor_principal");if(console.log("PASO 3: Contenedor HTML encontrado:",n),!n){alert("ERROR CRÍTICO: No se encuentra el div con clase .contenedor_principal en el HTML");return}const t=document.getElementById("url_buscar"),c=t?t.value:"/legacy/buscar";if(console.log("PASO 4: URL de búsqueda:",c),e.length<3){console.warn("Aviso: Término muy corto."),alert("Ingrese al menos 3 caracteres");return}n.innerHTML='<p style="padding:10px; font-weight:bold; color:blue;">🔍 Buscando en el servidor...</p>';const l=`${c}?search=${e}`;console.log("PASO 5: Iniciando Fetch a:",l),fetch(l).then(o=>(console.log("PASO 6: Respuesta del servidor recibida. Status:",o.status),o.json())).then(o=>{if(console.log("PASO 7: Datos convertidos a JSON:",o),window.pacientesEncontrados=o,o.length>0){console.log("PASO 8: Se encontraron resultados. Construyendo tabla..."),document.getElementById("seccion_registro").style.display="none";let i=`
                    <div style="margin-top: 20px;">
                        <h3 style="margin-bottom: 10px;">Resultados encontrados (${o.length}):</h3>
                        <table border="1" style="width:100%; border-collapse: collapse; background:white; font-size:12px;">
                            <thead style="background: #e0e0e0;">
                                <tr>
                                    <th style="padding:8px;">Documento</th>
                                    <th style="padding:8px;">Nombre</th>
                                    <th style="padding:8px;">Teléfono</th>
                                    <th style="padding:8px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                `;o.forEach((r,a)=>{i+=`
                        <tr>
                            <td style="padding:5px; text-align:center;">${r.documento}</td>
                            <td style="padding:5px;">${r.nombres} ${r.apellidos}</td>
                            <td style="padding:5px; text-align:center;">${r.telefono||"-"}</td>
                            <td style="padding:5px; text-align:center;">
                                <button type="button" class="btnPrincipal" 
                                       onclick="console.log('Click en indice ${a}'); selectingPaciente(${a})"
                                       style="cursor:pointer; padding: 4px 10px;">
                                   Seleccionar
                                </button>
                            </td>
                        </tr>
                    `}),i+="</tbody></table></div>",n.innerHTML=i,console.log("PASO 9: HTML inyectado en el contenedor.")}else console.log("PASO 8b: El servidor devolvió un array vacío."),n.innerHTML='<p style="padding:10px; color:red; font-weight:bold;">No se encontraron pacientes.</p>'}).catch(o=>{console.error("❌ ERROR EN FETCH:",o),n.innerHTML=`<p style="color:red; padding:10px;">Error JS: ${o.message}</p>`})};window.selectingPaciente=function(e){console.log("PASO 10: Seleccionando paciente índice:",e);const n=window.pacientesEncontrados[e];n?(console.log("Paciente seleccionado:",n),cargarDatosPaciente(n),document.querySelector(".contenedor_principal").innerHTML=""):console.error("Error: No existe paciente en ese índice")};window.cargarDatosPaciente=function(e){mostrarSeccion("registro",!1),document.getElementById("txt_numero_documento").value=e.documento||"",document.getElementById("txt_nombre_1").value=(e.nombres||"").split(" ")[0]||""};
