<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#eliminar").on('shown.bs.modal', function(){
        $(this).find('#form_eliminar #motivo').focus();
    });
});

$(document).ready(function(){
    $("#cobros").on('shown.bs.modal', function(){
        $(this).find('#formCobros #comentario').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	//LLAMADA A LAS FUNCIONES
	funciones();
	//FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE LAS FACTURAS

	//INICIO PARA EL REGISTRO DE COBROS A PROFESIONALES
	$('#formCobros #generar').on('click', function(e){
		 if ($('#formCobros #comentario').val() != ""){
			e.preventDefault();
			agregarCobros();
			return false;
		 }else{
			swal({
				title: "Error",
				text: "Hay registros en blanco, por favor corregir",
				icon: "error",
				dangerMode: true
			});
			return false;
		 }
	});
	//FIN PARA EL REGISTRO DE COBROS A PROFESIONALES

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
  $('#form_main_facturacion_reportes #estado').on('change',function(){
    listar_recetas();
  });

  $('#form_main_facturacion_reportes #clientes').on('change',function(){
    listar_recetas();
  });

  $('#form_main_facturacion_reportes #profesional').on('change',function(){
    listar_recetas();
  });

  $('#form_main_facturacion_reportes #fecha_b').on('change',function(){
    listar_recetas();
  });

  $('#form_main_facturacion_reportes #fecha_f').on('change',function(){
    listar_recetas();
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});

function funciones(){
  getEstado();
  getClientes();
  getProfesionales();
  listar_recetas();
}

//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

$('#form_eliminar #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){
	e.preventDefault();
	if($('#form_eliminar #motivo').val() != ""){
		rollback();
	}else{
		swal({
			title: "Error",
			text: "Hay registros en blanco, por favor corregir",
			icon: "error",
			dangerMode: true
		});
		return false;
	}
}else{
	swal({
		title: "Acceso Denegado",
		text: "No tiene permisos para ejecutar esta acción",
		icon: "error",
		dangerMode: true
	});
}
});

//INICIO OBTENER COLABORADOR CONSULTA
function getColaboradorConsulta(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){
		  var datos = eval(data);
          colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN OBTENER COLABORADOR CONSULTA

//INICIO ROLLBACK
async function modal_rollback(receta_id, pacientes_id) {
    try {
        const nombrePaciente = await consultarNombre(pacientes_id); // Espera a que se resuelva la promesa
        const numeroReceta = await getNumeroReceta(receta_id); // Espera a que se resuelva la promesa
        
        swal({
            title: "¿Esta seguro?",
            text: "¿Desea anular la factura para este registro: Paciente: " + nombrePaciente + ". Receta N°: " + numeroReceta + "?",
            content: {
                element: "input",
                attributes: {
                    placeholder: "Comentario",
                    type: "text",
                },
            },
            icon: "warning",
            buttons: {
                cancel: "Cancelar",
                confirm: {
                    text: "¡Sí, anular la receta!",
                    closeModal: false,
                },
            },
        }).then((value) => {
            if (value === null || value.trim() === "") {
                swal("¡Necesita escribir algo!", { icon: "error" });
                return false;
            }
            rollback(receta_id, value);
        });
    } catch (error) {
        console.log(error); // Si alguna promesa falla, se captura el error
    }
}

async function rollback(receta_id, comentario) {
    try {
        // Espera a que se resuelva la promesa de getFechaReceta
        var fecha = await getFechaReceta(receta_id);
        
        var hoy = new Date();
        var fecha_actual = convertDate(hoy);

        var url = '<?php echo SERVERURL; ?>php/recetas/rollback.php';

        if (fecha <= fecha_actual) {
            $.ajax({
                type: 'POST',
                url: url,
				data:{
					receta_id: receta_id,
					comentario: comentario
				},
                success: function(registro) {
                    if (registro == 1) {
                        listar_recetas();
                        swal({
                            title: "Success",
                            text: "Receta anulada correctamente",
                            icon: "success",
                        });
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: "Error",
                            text: "Error al anular la receta",
                            icon: "error",
                            dangerMode: true
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Error al receta ya esta anulada, no se puede procesar esta solicituda",
                            icon: "error",
                            dangerMode: true
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al ejecutar esta acción",
                            icon: "error",
                            dangerMode: true
                        });
                    }
                }
            });
            return false;
        } else {
            swal({
                title: "Error",
                text: "No se puede ejecutar esta acción fuera de esta fecha",
                icon: "error",
                dangerMode: true
            });
        }
    } catch (error) {
        console.log(error); // Captura cualquier error que pueda ocurrir durante la ejecución de la promesa
    }
}

function consultarNombre(pacientes_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';

        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				pacientes_id: pacientes_id 

			},
            success: function(data) {
                resolve(data); // Resolución con el dato recibido
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechazo si hay error
            }
        });
    });
}

function getNumeroReceta(receta_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/recetas/getNumeroReceta.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				receta_id: receta_id 

			},
            success: function(data) {
                var datos = JSON.parse(data); // Convierte el JSON a objeto
                resolve(datos[0]); // Devuelve el primer elemento del array
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechazo si hay error
            }
        });
    });
}

function getFechaReceta(receta_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/recetas/getFechaReceta.php';

        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				receta_id: receta_id 
			},
            success: function(data) {
                var datos = eval(data); // Evalúa la respuesta y extrae la fecha
                resolve(datos[0]); // Resuelve la promesa con la fecha
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechaza la promesa si ocurre un error
            }
        });
    });
}
//INICIO ROLLBACK

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}
/******************************************************************************************************************************************************************************/
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/recetas/getEstado.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_facturacion_reportes #estado').html("");
			$('#form_main_facturacion_reportes #estado').html(data);
       	 	$('#form_main_facturacion_reportes #estado').selectpicker('refresh');
        }
     });
}

function getClientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_facturacion_reportes #clientes').html("");
			$('#form_main_facturacion_reportes #clientes').html(data);
			$('#form_main_facturacion_reportes #clientes').selectpicker('refresh');
		}
     });
}

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getColaborador.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_facturacion_reportes #profesional').html("");
			$('#form_main_facturacion_reportes #profesional').html(data);
			$('#form_main_facturacion_reportes #profesional').selectpicker('refresh');
		}
     });
}

var listar_recetas = function(){
	var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
	var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();  
	var clientes = $('#form_main_facturacion_reportes #clientes').val() || '';
	var profesional = $('#form_main_facturacion_reportes #profesional').val() || '';
	var estado = $('#form_main_facturacion_reportes #estado').val() || 1;

	var table_reporte_recetas  = $("#dataTableRecetasMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/recetas/llenarDataTableRecetas.php",
            "data": function(d) {
                d.fechai = fechai;
                d.fechaf = fechaf;
                d.clientes = clientes;
                d.profesional = profesional;			
				d.estado = estado;
            }	
		},		
		"columns":[	
			{"data": "receta_numero"},
			{"data": "fecha"},				
			{"data": "identidad"},			
			{"data": "paciente"},	
			{"data": "receta_id"},
			{"data": "producto_nombre"},
			{"data": "cantidad"},	
			{"data": "descripcion"},
			{"data": "descripcion"},								
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item printReceta" href="#"><i class="fas fa-print fa-lg"></i> Imprimir</a>' +
							'<a class="dropdown-item deleteReceta" href="#"><i class="fa-solid fa-ban fa-lg"></i> Anular</a>' +
						'</div>' +
					'</div>'
			}
		],
		"lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,			
		"buttons":[		
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Recetas',
				className: 'btn btn-info',
				action: 	function(){
					listar_recetas();
				}
			}			
		]		
	});	 
	table_reporte_recetas.search('').draw();
	$('#buscar').focus();
	
	print_recetas_dataTable("#dataTableRecetasMain tbody", table_reporte_recetas);
	delete_recetas_dataTable("#dataTableRecetasMain tbody", table_reporte_recetas);	
}

var print_recetas_dataTable = function(tbody, table){
	$(tbody).off("click", "a.printReceta");
	$(tbody).on("click", "a.printReceta", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
			
		getRecetaReporte(data.receta_id);	
	});
}

var delete_recetas_dataTable = function(tbody, table){
	$(tbody).off("click", "a.deleteReceta");
	$(tbody).on("click", "a.deleteReceta", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_rollback(data.receta_id, data.pacientes_id)
	});
}

function getRecetaReporte(receta_id) {
    var url = "<?php echo SERVERURLWINDOWS; ?>";

    // Crear un formulario dinámico
    var form = document.createElement("form");
    form.method = "POST";
    form.action = url;

    // Añadir los parámetros al formulario

	/*
		"type": "Receta",
		"type": "Factura_media",
		"type": "Reporte_facturas",		
	*/

    var params = {
        "id": receta_id,
        "type": "Receta",
        "db": "<?php echo DB; ?>"
    };

    for (var key in params) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    }

    // Abrir una nueva ventana
    var newWindow = window.open("", "_blank");

    // Asegurarse de que la nueva ventana esté lista
    newWindow.document.body.appendChild(form);
    
    // Enviar el formulario a la nueva ventana
    form.submit();
}
</script>