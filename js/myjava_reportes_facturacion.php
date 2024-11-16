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
				type: "error",
				confirmButtonClass: 'btn-danger'
			});
			return false;
		 }
	});
	//FIN PARA EL REGISTRO DE COBROS A PROFESIONALES

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
  $('#form_main_facturacion_reportes #estado').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #clientes').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #profesional').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #fecha_b').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #fecha_f').on('change',function(){
    listar_reporte_facturacion();
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});

function cierreBill(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 4){
		if($('#form_main_facturacion_reportes #profesional').val() == "" || $('#form_main_facturacion_reportes #profesional').val() == null){
			profesional = getColaboradorConsultaID();
		}else{
			profesional = $('#form_main_facturacion_reportes #profesional').val();
		}

		$('#formCobros')[0].reset();
		$("#formCobros #generar").attr('disabled', false);
		$('#formCobros #colaborador_id').val(profesional);
		$('#formCobros #fechai').val($('#form_main_facturacion_reportes #fecha_b').val());
		$('#formCobros #fechaf').val($('#form_main_facturacion_reportes #fecha_f').val());
		$('#formCobros #profesional').val(getColaboradorNombre(profesional));
		$('#formCobros #pro').val("Registro");
		$('#cobros').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
		return false;
	}		
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
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
		return false;
	}
}else{
	swal({
		title: "Acceso Denegado",
		text: "No tiene permisos para ejecutar esta acción",
		type: "error",
		confirmButtonClass: 'btn-danger'
	});
}
});

//INICIO AGRUPAR FUNCIONES DE PACIENTES
function funciones(){
  getEstado();
  getClientes();
  getProfesionales();
  listar_reporte_facturacion();
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

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

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaboradorConsultaID(){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		success: function(valores){
			var datos = eval(valores);
			colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN FUNCION PARA OBTENER LOS COLABORADORES

//FUNCTION PARA OBTENER EL NOMBRE DEL COLABORADOR
function getColaboradorNombre(colaborador_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getColaboradorNombre.php';
    var colaborador_nombre = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		data:'colaborador_id='+colaborador_id,
		success: function(valores){
			colaborador_nombre = valores;
		}
	});
	return colaborador_nombre;
}
//FIN PARA OBTENER EL NOMBRE DEL COLABORADOR

//INICIO PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL
function agregarCobros(){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/agregarCargos.php';

	$.ajax({
		type:'POST',
		url:url,
		data:$('#formCobros').serialize(),
		success: function(registro){
			if(registro == 1){
				swal({
					title: "Success",
					text: "Valores generados correctamente",
					type: "success",
				});
				$('#formCobros #comentario').val("");
				$("#formCobros #generar").attr('disabled', true);
				listar_reporte_facturacion();
				return false;
			}else if(registro == 2){
				swal({
					title: "Error",
					text: "Error, no se puedieron generar los valores, por favor corregir",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
				return false;
			}else if(registro == 3){
				swal({
					title: "Error",
					text: "Error, este registro ya existe",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
				return false;
			}else{
				swal({
					title: "Error",
					text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
				return false;
			}
		}
	});
	return false;
}
//FIN PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL

//INICIO DETALLES DE FACTURA
function invoicesDetails(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/detallesFactura.php';

	$.ajax({
		type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		success:function(data){
		   $('#mensaje_show').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
		   });
		   $('#mensaje_mensaje_show').html(data);
		   $('#bad').hide();
		   $('#okay').show();
		}
	});
}
//FIN DETALLES DE FACTURA

//INICIO ROLLBACK
function modal_rollback(facturas_id, pacientes_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){
		swal({
		  title: "¿Esta seguro?",
		  text: "¿Desea anular la factura para este registro: Paciente: " + consultarNombre(pacientes_id) + ". Factura N°:  " + getNumeroFactura(facturas_id) + "?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",
		  confirmButtonText: "¡Sí, anular la factura!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			rollback(facturas_id, inputValue);
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
		return false;
	}
}

function rollback(facturas_id,comentario){
	var fecha = getFechaFactura(facturas_id);
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/rollback.php';

	if ( fecha <= fecha_actual){
	   $.ajax({
		  type:'POST',
		  url:url,
		  data:'facturas_id='+facturas_id+'&comentario='+comentario,
		  success: function(registro){
			  if(registro == 1){
			    listar_reporte_facturacion();
				swal({
					title: "Success",
					text: "Factura anulada correctamente",
					type: "success",
				});
			    return false;
			  }else if(registro == 2){
				swal({
					title: "Error",
					text: "Error al anular la factura",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
			    return false;
			  }else{
				swal({
					title: "Error",
					text: "Error al ejecutar esta acción",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
			  }
		  }
	   });
	   return false;
	}else{
		swal({
			title: "Error",
			text: "No se puede ejecutar esta acción fuera de esta fecha",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
	}
}

function consultarNombre(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';
	var resp;

	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){
          resp = data;
		}
	});
	return resp;
}

function getNumeroFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getNumeroFactura.php';
	var resp;

	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
          resp = data;
		}
	});
	return resp;
}
//INICIO ROLLBACK

//INICIO GET FECHA FACTURA
function getFechaFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getFechaFactura.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
		  var datos = eval(data);
		  fecha = datos[0];
		}
	});

	return fecha;
}
//FIN GET FECHA FACTURA

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function printBill(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/generaFactura.php?facturas_id='+facturas_id;
    window.open(url);
}
/******************************************************************************************************************************************************************************/
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getEstado.php';

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

var listar_reporte_facturacion = function(){
  var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
  var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();  
  var clientes = $('#form_main_facturacion_reportes #clientes').val();
  var profesional = $('#form_main_facturacion_reportes #profesional').val();
  var estado = '';

  if($('#form_main_facturacion_reportes #estado').val() == ""){
    estado = 1;
  }else{
    estado = $('#form_main_facturacion_reportes #estado').val();
  }
	
	var table_reporte_facturacion  = $("#dataTableReporteFacturacionMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/reporte_facturacion/llenarDataTableReporteFacturas.php",
            "data": function(d) {
                d.fechai = fechai;
                d.fechaf = fechaf;
                d.clientes = clientes;
                d.profesional = profesional;			
				d.estado = estado;
            }		
		},		
		"columns":[
			{
				"data": "fecha",
				"render": function(data, type, row) {
					return '<a href="#" class="showInvoiceDetail">' + data + '</a>';
				}
			},			
			{"data": "tipo_documento"},
			{"data": "identidad"},			
			{"data": "paciente"},	
			{"data": "factura"},
			{"data": "precio"},
			{"data": "isv_neto"},	
			{"data": "descuento"},
			{"data": "total"},
			{"data": "servicio"},
			{"data": "profesional"},								
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item printBill" href="#"><i class="fas fa-print fa-lg"></i> Imprimir</a>' +
							'<a class="dropdown-item closeBill" href="#"><i class="fas fa-calculator fa-lg"></i> Cierre</a>' +
							'<a class="dropdown-item deleteBill" href="#"><i class="fas fa-download fa-lg"></i> Anular</a>' +
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
				titleAttr: 'Actualizar Pacientes',
				className: 'btn btn-info',
				action: 	function(){
					listar_reporte_facturacion();
				}
			},		
			{
				text:      '<i class="fas fa-calculator fa-lg"></i> Cierre',
				titleAttr: 'Agregar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					cierreBill();
				}
			},		
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Facturación',
				className: 'btn btn-success',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },				
			},
			{
				extend: 'pdf',
				orientation: 'landscape',
				text: '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Facturación',
				className: 'btn btn-danger',
				exportOptions: {
					modifier: {
						page: 'current' // Solo exporta las filas visibles en la página actual
					},
					columns: [0, 1, 2, 3, 4, 5, 6] // Define las columnas a exportar
				},
				customize: function(doc) {
					// Asegúrate de que `imagen` contenga la cadena base64 de la imagen
					doc.content.splice(1, 0, {
						margin: [0, 0, 0, 12],
						alignment: 'left',
						image: imagen, // Usando la variable que ya tiene la imagen base64
						width: 170, // Ajusta el tamaño si es necesario
						height: 45 // Ajusta el tamaño si es necesario
					});
				}
			},
			{
				extend: 'print',
				text: '<i class="fas fa-print fa-lg"></i> Imprimir',  // Correcta colocación del icono
				titleAttr: 'Imprimir',
				title: 'Reporte Facturación',
				className: 'btn btn-secondary',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
			}
		]		
	});	 
	table_reporte_facturacion.search('').draw();
	$('#buscar').focus();
	
	show_invoice_detail_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	print_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	close_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	delete_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);	
}

var show_invoice_detail_dataTable = function(tbody, table){
	$(tbody).off("click", "a.showInvoiceDetail");
	$(tbody).on("click", "a.showInvoiceDetail", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		swal({
			title: "Información",
			text: "Esta opción se encuentra en desarrollo",
			type: "warning",
			confirmButtonClass: 'btn-warning'
		});		
		//invoicesDetails(data.pacientes_id)
	});
}

var print_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.printBill");
	$(tbody).on("click", "a.printBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		printBill(data.pacientes_id)	
	});
}

var close_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.closeBill");
	$(tbody).on("click", "a.closeBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		swal({
			title: "Información",
			text: "Esta opción se encuentra en desarrollo",
			type: "warning",
			confirmButtonClass: 'btn-warning'
		});
	});
}

var delete_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.deleteBill");
	$(tbody).on("click", "a.deleteBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_rollback(data.facturas_id, data.pacientes_id)
	});
}
</script>