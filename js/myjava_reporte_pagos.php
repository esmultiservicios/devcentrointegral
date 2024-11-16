<script>
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	listar_reporte_pagos();
	getColaborador();
	getEstado();
	getTipoPago();
	getClientes();
	getProfesionales();

	$('#form_main #bs_regis').on('keyup',function(){
		listar_reporte_pagos();
	});

	$('#form_main #fecha_b').on('change',function(){
		listar_reporte_pagos();
	});

	$('#form_main #fecha_f').on('change',function(){
		listar_reporte_pagos();
	});

	$('#form_main #profesional').on('change',function(){
		listar_reporte_pagos();
	});

	$('#form_main #clientes').on('change',function(){
		listar_reporte_pagos();
	});

	$('#form_main #estado').on('change',function(){
		listar_reporte_pagos();
	});
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});
//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

/***************************************************************************************************************************************************************************/
//INICIO FUNCIONES

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

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/reporte_pagos/paginar.php';

	var fechai = $('#form_main #fecha_b').val();
  var fechaf = $('#form_main #fecha_f').val();
  var dato =  $('#form_main #bs_regis').val()
  var clientes = $('#form_main #clientes').val();
	var profesional = $('#form_main #profesional').val();
  var estado = '';

  if($('#form_main #estado').val() == ""){
    estado = 1;
  }else{
    estado = $('#form_main #estado').val();
  }

	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:'partida='+partida+'&fechai='+fechai+'&fechaf='+fechaf+'&dato='+dato+'&clientes='+clientes+'&profesional='+profesional+'&estado='+estado,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE REGISTROS


//INICIO FUNCION PARA OBTENER LOS BANCOS DISPONIBLES
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/reporte_pagos/getEstado.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #estado').html("");
			$('#form_main #estado').html(data);
			$('#form_main #estado').selectpicker('refresh');
        }
     });
}
//FIN FUNCION PARA OBTENER LOS BANCOS DISPONIBLES

//INICIO FUNCION PARA OBTENER LOS PROFESIONALES
function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/reporte_pagos/getProfesional.php';

	$.ajax({
        type: "POST",
        url: url,
        success: function(data){
		    $('#form_main #profesional').html("");
			$('#form_main #profesional').html(data);
			$('#form_main #profesional').selectpicker('refresh');
		}
     });
}
//FIN FUNCION PARA OBTENER LOS PROFESIONALES


//INICIO REPORTE DE FACTURACION
function invoicesDetails(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_pagos/detallesPago.php';

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

function editarRegistro(pagos_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		var url = '<?php echo SERVERURL; ?>php/reporte_pagos/editar.php';

		$.ajax({
			type:'POST',
			url:url,
			data:'pagos_id='+pagos_id,
			success: function(valores){
				var datos = eval(valores);
				$('#reg_reporte_pagos').show();
				$('#formulario_reporte_pagos #pro').val('Edicion');
				$('#formulario_reporte_pagos #pagos_id').val(pagos_id);
				$('#formulario_reporte_pagos #fecha_reporte_pago').val(datos[0]);
				$('#formulario_reporte_pagos #paciente_reporte_pago').val(datos[1]);
				$('#formulario_reporte_pagos #factura_reporte_pago').val(datos[2]);
				$('#formulario_reporte_pagos #tipo_pago_reporte').val(datos[3]);
				$('#formulario_reporte_pagos #paciente_reporte_efectivo').val(datos[4]);
				$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(datos[5]);
				$('#formulario_reporte_pagos #tipo_pago_importe').val(datos[6]);

				$('#formulario_reporte_pagos #paciente_reporte_efectivo').attr("readonly", true);
				$('#formulario_reporte_pagos #factura_reporte_tarjeta').attr("readonly", true);
				$('#formulario_reporte_pagos #tipo_pago_importe').attr("readonly", true);

				if(datos[3] == 6){
					$('#formulario_reporte_pagos #paciente_reporte_efectivo').attr("readonly", false);
					$('#formulario_reporte_pagos #factura_reporte_tarjeta').attr("readonly", false);
				}

				//DESHABILITAR OBJETOS
				$('#formulario_reporte_pagos #paciente_reporte_pago').attr("readonly", true);
				$('#formulario_reporte_pagos #factura_reporte_pago').attr("readonly", true);
				$('#formulario_reporte_pagos #fecha_reporte_pago').attr("disabled", true);

				$('#formulario_reporte_pagos').attr({ 'data-form': 'update' });
				$('#formulario_reporte_pagos').attr({ 'action': '<?php echo SERVERURL; ?>php/reporte_pagos/modificar.php' });

				$('#modal_editar_pagos').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
				return false;
			}
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
	}
}

$('#formulario_reporte_pagos #tipo_pago_reporte').on('change',function(){
	  if($('#formulario_reporte_pagos #tipo_pago_reporte').val() == 6){
		$('#formulario_reporte_pagos #paciente_reporte_efectivo').attr("readonly", false);
		$('#formulario_reporte_pagos #paciente_reporte_efectivo').focus();
	  }else{
		$('#reg_reporte_pagos').attr('disabled', false);
	  }
});

$('#formulario_reporte_pagos #paciente_reporte_efectivo').on('keyup',function(){
	var importe = $('#formulario_reporte_pagos #tipo_pago_importe').val();

	if(Math.floor($('#formulario_reporte_pagos #paciente_reporte_efectivo').val()*100) < Math.floor(importe*100)){
		var total = parseInt(importe) - parseInt($('#formulario_reporte_pagos #paciente_reporte_efectivo').val());
		if(total > 0){
			$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(total);
			$('#reg_reporte_pagos').attr('disabled', false);
		}else{
			$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(0);
			$('#reg_reporte_pagos').attr('disabled', true);
		}
	}else if(Math.floor($('#formulario_reporte_pagos #paciente_reporte_efectivo').val()*100) >= Math.floor(importe*100)){
		$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(0);
		$('#reg_reporte_pagos').attr('disabled', true);
	}else if($('#formulario_reporte_pagos #paciente_reporte_efectivo').val() == ""){
		$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(0);
		$('#reg_reporte_pagos').attr('disabled', true);
	}else{
		$('#formulario_reporte_pagos #factura_reporte_tarjeta').val(0);
		$('#reg_reporte_pagos').attr('disabled', true);
	}
});

function getTipoPago(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getTipoPago.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_reporte_pagos #tipo_pago_reporte').html("");
			$('#formulario_reporte_pagos #tipo_pago_reporte').html(data);
			$('#formulario_reporte_pagos #tipo_pago_reporte').selectpicker('refresh');
        }
     });
}

function getClientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main #clientes').html("");
			$('#form_main #clientes').html(data);
			$('#form_main #clientes').selectpicker('refresh');
		}
	});
}

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getColaborador.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main #profesional').html("");
			$('#form_main #profesional').html(data);
			$('#form_main #profesional').selectpicker('refresh');
		}
     });
}

var listar_reporte_pagos = function(){
	var fechai = $('#form_main #fecha_b').val();
	var fechaf = $('#form_main #fecha_f').val();
	var clientes = $('#form_main #clientes').val();
	var profesional = $('#form_main #profesional').val();
	var estado = '';

	if($('#form_main #estado').val() == ""){
		estado = 1;
	}else{
		estado = $('#form_main #estado').val();
	}

	var table_reporte_pagos  = $("#dataTableReportePagosMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/reporte_pagos/llenarDataTableReportePagos.php",
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
				"data": "fecha_pago",
				"render": function(data, type, row) {
					return '<a href="#" class="showDetallesPago">' + data + '</a>';
				}
			},
			{"data": "paciente"},
			{"data": "identidad"},
			{"data": "numero"},			
			{"data": "importe"},
			{"data": "efectivo"},
			{"data": "tarjeta"},			
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item editarPago" href="#"><i class="fas fa-eye fa-lg"></i> Editar Pago</a>' +
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
				titleAttr: 'Actualizar Pago',
				className: 'btn btn-info',
				action: 	function(){
					listar_pacientes();
				}
			},					
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Pago',
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
				title: 'Reporte Pago',
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
				title: 'Reporte Pago',
				className: 'btn btn-secondary',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
			}
		]		
	});	 
	table_reporte_pagos.search('').draw();
	$('#buscar').focus();
	
	invoice_details_pay_dataTable("#dataTableReportePagosMain tbody", table_reporte_pagos);
	edit_pay_dataTable("#dataTableReportePagosMain tbody", table_reporte_pagos);
}

var invoice_details_pay_dataTable = function(tbody, table){
	$(tbody).off("click", "a.showDetallesPago");
	$(tbody).on("click", "a.showDetallesPago", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		invoicesDetails(data.facturas_id);
	});
}

var edit_pay_dataTable = function(tbody, table){
	$(tbody).off("click", "a.editarPago");
	$(tbody).on("click", "a.editarPago", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		editarRegistro(data.pagos_id);
	});
}
</script>