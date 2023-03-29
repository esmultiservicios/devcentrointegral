<script>
$(document).ready(function(){
	if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		$('#formulario_facturacion #invoiceItem #price_0').attr('readonly', false);
	}else{
		$('#formulario_facturacion #invoiceItem #price_0').attr('readonly', true);
	}
});

function reportePDF(agenda_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 3 || getUsuarioSistema() == 4 || getUsuarioSistema() == 5 || getUsuarioSistema() == 8 || getUsuarioSistema() == 9){
	    window.open('<?php echo SERVERURL; ?>php/citas/tickets.php?agenda_id='+agenda_id);
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

function sendEmailReprogramación(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/mail/correo_reprogramaciones.php';
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+agenda_id,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

function getMonth(){
	const hoy = new Date()
	return hoy.toLocaleString('default', { month: 'long' });
}

function getUsuarioSistema(){
    var url = '<?php echo SERVERURL; ?>php/sesion/sistema_tipo_usuario.php';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          usuario = data;			  		  		  			  
		}
	});
	return usuario;
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip({
	  trigger: "hover"
  })
});

/*
###########################################################################################################################################################
###########################################################################################################################################################
###########################################################################################################################################################
*/
/*															INICIO FACTURACIÓN				   															 */
//INICIO BUSQUEDA PACIENTES
$('#formulario_facturacion #buscar_paciente').on('click', function(e){
	e.preventDefault();
	 listar_pacientes_buscar();
	 $('#modal_busqueda_pacientes').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});
//FIN BUSQUEDA PACIENTES

//INICIO BUSQUEDA SERVICIOS
$('#formulario_facturacion #buscar_servicios').on('click', function(e){
	e.preventDefault();
	listar_servicios_factura_buscar();
	$('#modal_busqueda_servicios').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});
//FIN BUSQUEDA SERVICIOS

//INICIO BUSQUEDA PRODUCTOS FACTURA
$(document).ready(function(){
    $("#formulario_facturacion #invoiceItem").on('click', '.buscar_producto', function() {
		  listar_productos_facturas_buscar();
		  var row_index = $(this).closest("tr").index();
		  var col_index = $(this).closest("td").index();
		  
		  $('#formulario_busqueda_productos_facturas #row').val(row_index);
		  $('#formulario_busqueda_productos_facturas #col').val(col_index);		  
		  $('#modal_busqueda_productos_facturas').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		  });
	});
});
//FIN BUSQUEDA PRODUCTOS FACTURA

$(document).ready(function(){
    $("#formulario_facturacion #invoiceItem").on('blur', '.buscar_cantidad', function() {
		var row_index = $(this).closest("tr").index();
		var col_index = $(this).closest("td").index();

		var impuesto_venta = parseFloat($('#formulario_facturacion #invoiceItem #isv_'+ row_index).val());
		var cantidad = parseFloat($('#formulario_facturacion #invoiceItem #quantity_'+ row_index).val());
		var precio = parseFloat($('#formulario_facturacion #invoiceItem #price_'+ row_index).val());
		var total = parseFloat($('#formulario_facturacion #invoiceItem #total_'+ row_index).val());

		var isv = 0;
		var isv_total = 0;
		var porcentaje_isv = 0;
		var porcentaje_calculo = 0;
		var isv_neto = 0;
		
		if(impuesto_venta == 1){
			porcentaje_isv = parseFloat(getPorcentajeISV() / 100);
			if(total == "" || total == 0){
				porcentaje_calculo = (parseFloat(precio) * parseFloat(cantidad) * porcentaje_isv).toFixed(2);			
				isv_neto = parseFloat(porcentaje_calculo).toFixed(2);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}else{	
				isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
				porcentaje_calculo = (parseFloat(precio) * parseFloat(cantidad) * porcentaje_isv).toFixed(2);
				isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}
		}

		calculateTotal();
	});
});

$(document).ready(function(){
    $("#formulario_facturacion #invoiceItem").on('keyup', '.buscar_cantidad', function() {
		var row_index = $(this).closest("tr").index();
		var col_index = $(this).closest("td").index();

		var impuesto_venta = parseFloat($('#formulario_facturacion #invoiceItem #isv_'+ row_index).val());
		var cantidad = parseFloat($('#formulario_facturacion #invoiceItem #quantity_'+ row_index).val());
		var precio = parseFloat($('#formulario_facturacion #invoiceItem #price_'+ row_index).val());
		var total = parseFloat($('#formulario_facturacion #invoiceItem #total_'+ row_index).val());

		var isv = 0;
		var isv_total = 0;
		var porcentaje_isv = 0;
		var porcentaje_calculo = 0;
		var isv_neto = 0;
		
		if(impuesto_venta == 1){
			porcentaje_isv = parseFloat(getPorcentajeISV() / 100);
			if(total == "" || total == 0){
				porcentaje_calculo = (parseFloat(precio) * parseFloat(cantidad) * porcentaje_isv).toFixed(2);			
				isv_neto = parseFloat(porcentaje_calculo).toFixed(2);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}else{	
				isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
				porcentaje_calculo = (parseFloat(precio) * parseFloat(cantidad) * porcentaje_isv).toFixed(2);
				isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
				$('#formulario_facturacion #invoiceItem #valor_isv_'+ row_index).val(porcentaje_calculo);
			}
		}

		calculateTotal();
	});
});
//FIN FORMULARIOS

//INICIO FUNCIONES PARA LLENAR DATOS EN LA TABLA
var listar_pacientes_buscar = function(){
	var table_pacientes_buscar = $("#dataTablePacientes").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/facturacion/getPacientesTabla.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"paciente"},
			{"data":"identidad"},
			{"data":"expediente"},
			{"data":"email"}			
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_pacientes_buscar.search('').draw();
	$('#buscar').focus();
	
	view_pacientes_busqueda_dataTable("#dataTablePacientes tbody", table_pacientes_buscar);
}

var view_pacientes_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_facturacion #pacientes_id').val(data.pacientes_id);
		$('#formulario_facturacion #cliente_nombre').val(data.paciente);
		$('#modal_busqueda_pacientes').modal('hide');
	});
}

var listar_servicios_factura_buscar = function(){
	var table_servicios_factura_buscar = $("#dataTableServicios").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/facturacion/getServiciosTabla.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"nombre"},		
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_servicios_factura_buscar.search('').draw();
	$('#buscar').focus();
	
	view_servicios_busqueda_dataTable("#dataTableServicios tbody", table_servicios_factura_buscar);
}

var view_servicios_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_facturacion #servicio_id').val(data.servicio_id);
		$('#modal_busqueda_servicios').modal('hide');
	});
}

var listar_productos_facturas_buscar = function(){
	var table_productos_buscar = $("#dataTableProductosFacturas").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/facturacion/getProductosFacturaTabla.php"
		},
		"columns":[
			{"defaultContent":"<button class='editar btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"producto"},
			{"data":"descripcion"},
			{"data":"concentracion"},	
			{"data":"medida"},			
			{"data":"cantidad"},
			{"data":"precio_venta"}			
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_productos_buscar.search('').draw();
	$('#buscar').focus();
	
	editar_productos_busqueda_dataTable("#dataTableProductosFacturas tbody", table_productos_buscar);
}

var editar_productos_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.editar");		
	$(tbody).on("click", "button.editar", function(e){
		e.preventDefault();
		if($("#formulario_facturacion #cliente_nombre").val() != ""){	
			var data = table.row( $(this).parents("tr") ).data();
			var row = $('#formulario_busqueda_productos_facturas #row').val();
			
			if (data.categoria == "Servicio"){
				$('#formulario_facturacion #invoiceItem #productName_'+ row).val(data.producto);
			}else{
				$('#formulario_facturacion #invoiceItem #productName_'+ row).val(data.producto + ' ' + data.concentracion + ' ' + data.medida);
			}

			$('#formulario_facturacion #invoiceItem #productoID_'+ row).val(data.productos_id);
			$('#formulario_facturacion #invoiceItem #price_'+ row).val(data.precio_venta);
			$('#formulario_facturacion #invoiceItem #isv_'+ row).val(data.impuesto_venta);
			$('#formulario_facturacion #invoiceItem #discount_'+ row).val(0);
			$('#formulario_facturacion #invoiceItem #quantity_'+ row).val(1);								
			$('#formulario_facturacion #invoiceItem #quantity_'+ row).focus();
			
			var isv = 0;
			var isv_total = 0;
			var porcentaje_isv = 0;
			var porcentaje_calculo = 0;
			var isv_neto = 0;
		
			if(data.impuesto_venta == 1){
				porcentaje_isv = parseFloat(getPorcentajeISV() / 100);
				if($('#formulario_facturacion #taxAmount').val() == "" || $('#formulario_facturacion #taxAmount').val() == 0){
					porcentaje_calculo = (parseFloat(data.precio_venta) * porcentaje_isv).toFixed(2);			
					isv_neto = porcentaje_calculo;
					$('#formulario_facturacion #taxAmount').val(porcentaje_calculo);
					$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
				}else{				
					isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
					porcentaje_calculo = (parseFloat(data.precio_venta) * porcentaje_isv).toFixed(2);
					isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
					$('#formulario_facturacion #taxAmount').val(isv_neto);	
					$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
				}
			}
			
			calculateTotal();
			addRow();
			$('#modal_busqueda_productos_facturas').modal('hide');
		}else{
			swal({
				title: "Error", 
				text: "Lo sentimos no se puede seleccionar un producto, por favor seleccione un cliente antes de poder continuar",
				type: "error", 
				confirmButtonClass: "btn-danger"
			});				
		}
	});
}
//FIN FUNCIONES PARA LLENAR DATOS EN LA TABLA

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/servicios.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario_facturacion #servicio_id').html("");
			$('#formulario_facturacion #servicio_id').html(data);			
		}			
     });	
}

$(document).ready(function(){
    $("#modal_busqueda_pacientes").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_pacientes #buscar').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_colaboradores").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_coloboradores #buscar').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_productos_facturas").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_productos_facturas #buscar').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_servicios").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_servicios #buscar').focus();
    });
});

/*INICIO AUTO COMPLETAR*/
/*INICIO SUGGESTION PRODUCTO*/
$("#formulario_facturacion #invoiceItem").on('click', '.producto', function() {
	var row = $(this).closest("tr").index();
	var col = $(this).closest("td").index();
	
    $('#formulario_facturacion #productName_'+ row).on('keyup', function() {
	   if($("#formulario_facturacion #cliente_nombre").val() != ""){		
		   if($('#formulario_facturacion #invoiceItem #productName_'+ row).val() != ""){
				 var key = $(this).val();		
				 var dataString = 'key='+key;
				 var url = '<?php echo SERVERURL; ?>php/productos/autocompletarProductos.php';
		
				$.ajax({
				   type: "POST",
				   url: url,
				   data: dataString,
				   success: function(data) {
					  //Escribimos las sugerencias que nos manda la consulta
					  $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeIn(1000).html(data);
					  //Al hacer click en algua de las sugerencias
					  $('.suggest-element').on('click', function(){
							//Obtenemos la id unica de la sugerencia pulsada
							var producto_id = $(this).attr('id');					
							
							//Editamos el valor del input con data de la sugerencia pulsada							
							$('#formulario_facturacion #invoiceItem #productName_'+ row).val($('#'+producto_id).attr('data'));
							$('#formulario_facturacion #invoiceItem #quantity_'+ row).val(1);
							$('#formulario_facturacion #invoiceItem #quantity_'+ row).focus();
							//Hacemos desaparecer el resto de sugerencias
							$('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeOut(1000);
							addRow();	

							//OBTENEMOS DATOS DEL PRODUCTO
							var url = '<?php echo SERVERURL; ?>php/productos/editarProductos.php';		
								
							$.ajax({
								type: "POST",
								url: url,
								data: "productos_id=" + producto_id,
								async: true,
								success: function(data){
									var datos = eval(data);
									$('#formulario_facturacion #invoiceItem #productoID_'+ row).val(producto_id);
									$('#formulario_facturacion #invoiceItem #price_'+ row).val(datos[7]);
									$('#formulario_facturacion #invoiceItem #isv_'+ row).val(datos[15]);
									$('#formulario_facturacion #invoiceItem #discount_'+ row).val(0);
									$('#formulario_facturacion #invoiceItem #quantity_'+ row).val(1);								
									$('#formulario_facturacion #invoiceItem #quantity_'+ row).focus();									
									var isv = 0;
									var isv_total = 0;
									var porcentaje_isv = 0;
									var porcentaje_calculo = 0;
									var isv_neto = 0;
								
									if(datos[15] == 1){
										porcentaje_isv = parseFloat(getPorcentajeISV() / 100);
										if($('#formulario_facturacion #taxAmount').val() == "" || $('#formulario_facturacion #taxAmount').val() == 0){
											porcentaje_calculo = (parseFloat(datos[7]) * porcentaje_isv).toFixed(2);			
											isv_neto = porcentaje_calculo;
											$('#formulario_facturacion #taxAmount').val(porcentaje_calculo);
											$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
										}else{				
											isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
											porcentaje_calculo = (parseFloat(datos[7]) * porcentaje_isv).toFixed(2);
											isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
											$('#formulario_facturacion #taxAmount').val(isv_neto);	
											$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
										}
									}									
									calculateTotal();
								}			
							 });	
													
							return false;
					 });
				  }
			   });   
		   }else{
			   $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeIn(1000).html("");
			   $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeOut(1000);
		   }
	   }else{
			swal({
				title: "Error", 
				text: "Lo sentimos no se puede efectuar la búsqueda, por favor seleccione un cliente antes de poder continuar",
				type: "error", 
				confirmButtonClass: "btn-danger"
			});		   
	   }
	 });		

	//OCULTAR EL SUGGESTION
    $('#formulario_facturacion #invoiceItem #productName_'+ row).on('blur', function() {
	   $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeOut(1000);
    });		

    $('#formulario_facturacion #invoiceItem #productName_'+ row).on('click', function(e) {
	   if($("#formulario_facturacion #cliente_nombre").val() != ""){
		   if($('#formulario_facturacion #invoiceItem #productName_1').val() != ""){
				 var key = $(this).val();		
				 var dataString = 'key='+key;
				 var url = '<?php echo SERVERURL; ?>php/productos/autocompletarProductos.php';
		
				$.ajax({
				   type: "POST",
				   url: url,
				   data: dataString,
				   success: function(data) {
					  //Escribimos las sugerencias que nos manda la consulta
					  $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeIn(1000).html(data);
					  //Al hacer click en algua de las sugerencias
					  $('.suggest-element').on('click', function(){
							//Obtenemos la id unica de la sugerencia pulsada
							var producto_id = $(this).attr('id');
							 
							//Editamos el valor del input con data de la sugerencia pulsada
							$('#formulario_facturacion #invoiceItem #productName_'+ row).val($('#'+producto_id).attr('data'));
							$('#formulario_facturacion #invoiceItem #quantity_'+ row).val(1);
							$('#formulario_facturacion #invoiceItem #quantity_'+ row).focus();
							//Hacemos desaparecer el resto de sugerencias
							$('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeOut(1000);
							addRow();

							//OBTENEMOS DATOS DEL PRODUCTO
							var url = '<?php echo SERVERURL; ?>php/productos/editarProductos.php';		
								
							$.ajax({
								type: "POST",
								url: url,
								data: "productos_id=" + producto_id,
								async: true,
								success: function(data){
									var datos = eval(data);
									$('#formulario_facturacion #invoiceItem #productoID_'+ row).val(producto_id);
									$('#formulario_facturacion #invoiceItem #price_'+ row).val(datos[7]);
									$('#formulario_facturacion #invoiceItem #isv_'+ row).val(datos[15]);
									$('#formulario_facturacion #invoiceItem #discount_'+ row).val(0);
									$('#formulario_facturacion #invoiceItem #quantity_'+ row).val(1);								
									$('#formulario_facturacion #invoiceItem #quantity_'+ row).focus();									
									var isv = 0;
									var isv_total = 0;
									var porcentaje_isv = 0;
									var porcentaje_calculo = 0;
									var isv_neto = 0;
								
									if(datos[15] == 1){
										porcentaje_isv = parseFloat(getPorcentajeISV() / 100);
										if($('#formulario_facturacion #taxAmount').val() == "" || $('#formulario_facturacion #taxAmount').val() == 0){
											porcentaje_calculo = (parseFloat(datos[7]) * porcentaje_isv).toFixed(2);			
											isv_neto = porcentaje_calculo;
											$('#formulario_facturacion #taxAmount').val(porcentaje_calculo);
											$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
										}else{				
											isv_total = parseFloat($('#formulario_facturacion #taxAmount').val());
											porcentaje_calculo = (parseFloat(datos[7]) * porcentaje_isv).toFixed(2);
											isv_neto = parseFloat(isv_total) + parseFloat(porcentaje_calculo);
											$('#formulario_facturacion #taxAmount').val(isv_neto);	
											$('#formulario_facturacion #invoiceItem #valor_isv_'+ row).val(porcentaje_calculo);
										}
									}											
									calculateTotal();
								}			
							 });
													
							return false;
					 });
				  }
			   });   
		   }else{
			   $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeIn(1000).html("");
			   $('#formulario_facturacion #invoiceItem #suggestions_producto_'+ row).fadeOut(1000);
		   }
	   }else{
			swal({
				title: "Error", 
				text: "Lo sentimos no se puede efectuar la búsqueda, por favor seleccione un cliente antes de poder continuar",
				type: "error", 
				confirmButtonClass: "btn-danger"
			});		   
	   }
	});		
});
/*FIN SUGGESTION PRODUCTO*/
/*FIN AUTO COMPLETAR*/

//INICIO BOTOES RECETA MEDICA
$('#formulario_facturacion #bt_add').on('click', function(e){
	e.preventDefault();
});

$('#formulario_facturacion #bt_del').on('click', function(e){
	e.preventDefault();
});
//FIN BOTONES RECETA MEDICA
/*														 	FIN FACTURACIÓN				   															 	*/
/*
###########################################################################################################################################################
###########################################################################################################################################################
###########################################################################################################################################################
*/

//REFRESCAR LA SESION CADA CIERTO TIEMPO PARA QUE NO EXPIRE
document.addEventListener("DOMContentLoaded", function(){
    // Invocamos cada 5 segundos ;)
    const milisegundos = 5 *1000;
    setInterval(function(){
        // No esperamos la respuesta de la petición porque no nos importa
        fetch("<?php echo SERVERURL; ?>php/signin_out/refrescar.php");
    },milisegundos);
});

function getPorcentajeISV(){
    var url = '<?php echo SERVERURL; ?>php/productos/getIsv.php';
	var isv;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
		  var datos = eval(data);
          isv = datos[0];			  		  		  			  
		}
	});
	return isv;	
}

$(document).ready(function(){
	getTempUsers();
	getActiveUsers();
	getTotalAusencias()
	getTotalAtenciones();
	getPendientesAtencion();
	getPendientesPreclinica();
	getPendientesFacturas();
	getTotalProductos();
	
	setInterval('getTempUsers()',2000);
	setInterval('getActiveUsers()',2000);
	setInterval('getTotalAusencias()',2000);
	setInterval('getTotalAtenciones()',2000);
	setInterval('getPendientesAtencion()',2000);
	setInterval('getPendientesPreclinica()',2000);
	setInterval('getPendientesFacturas()',2000);
	setInterval('getTotalProductos()',2000);

	listar_secuencia_fiscales_dashboard();
	
	$(window).scrollTop(0);
});

//DATOS MAIN
function getTempUsers(){
    var url = '<?php echo SERVERURL; ?>php/main/getTemporales.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_temporales').html(data);  		  		  			  
		}
	});	
}

function getActiveUsers(){
    var url = '<?php echo SERVERURL; ?>php/main/getActivos.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_activos').html(data);  		  		  			  
		}
	});	
}

function getTotalAusencias(){
    var url = '<?php echo SERVERURL; ?>php/main/totalAusencias.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_ausencias').html(data);  		  		  			  
		}
	});	
}

function getTotalAtenciones(){
    var url = '<?php echo SERVERURL; ?>php/main/getTotalAtenciones.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_atenciones').html(data);  		  		  			  
		}
	});	
}

function getPendientesAtencion(){
    var url = '<?php echo SERVERURL; ?>php/main/pendienteAtenciones.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_prendiente_atenciones').html(data);  		  		  			  
		}
	});	
}

function getPendientesPreclinica(){
    var url = '<?php echo SERVERURL; ?>php/main/pendientePreclinica.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_pendiente_preclinica').html(data);  		  		  			  
		}
	});	
}

function getPendientesFacturas(){
    var url = '<?php echo SERVERURL; ?>php/main/facturasPendientes.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_facturas_pendientes').html(data);  		  		  			  
		}
	});	
}

function getTotalProductos(){
    var url = '<?php echo SERVERURL; ?>php/main/totalProductos.php';
	$.ajax({
	    type:'POST',
		url:url,
		success: function(data){
           	$('#main_productos').html(data);  		  		  			  
		}
	});	
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function today(){
    var hoy = new Date();
    return convertDate(hoy);	
}

function getMonth(){
	const hoy = new Date()
	return hoy.toLocaleString('default', { month: 'long' });
}

function convertDateFormat(string) {
  if(string == null || string == ""){
    var hoy = new Date();
    string = convertDate(hoy);	  
  }
}

var listar_secuencia_fiscales_dashboard = function(){
	var table_secuencia_fiscales_dashboard  = $("#dataTableSecuenciaDashboard").DataTable({
		"destroy":true,
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/main/llenarDataTableDocumentosFiscalesDashboard.php"
		},
		"columns":[
			{"data":"empresa"},
			{"data":"documento"},
			{"data":"inicio"},
			{"data":"fin"},
			{"data":"siguiente"},
			{"data":"fecha"}
		],
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,
		"columnDefs": [
		  { width: "40.66%", targets: 0 },
		  { width: "12.66%", targets: 1 },
		  { width: "12.66%", targets: 2 },
		  { width: "12.66%", targets: 3 },
		  { width: "8.66%", targets: 4 },
		  { width: "12.66%", targets: 5 }
		],		
		"buttons":[
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Documentos Fiscales',
				className: 'table_actualizar btn btn-secondary ocultar',
				action: 	function(){
					listar_secuencia_fiscales_dashboard();
				}
			},
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				orientation: 'landscape',
				pageSize: 'LETTER',						
				title: 'Reporte Documentos Fiscales',
				messageBottom: 'Fecha de Reporte: ' + convertDateFormat(today()),
				className: 'table_reportes btn btn-success ocultar',
				exportOptions: {
						columns: [0,1,2,3,4,5]
				},				
			},
			{
				extend:    'pdf',
				text:      '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				orientation: 'landscape',
				pageSize: 'LETTER',						
				title: 'Reporte Documentos Fiscales',
				messageBottom: 'Fecha de Reporte: ' + convertDateFormat(today()),
				className: 'table_reportes btn btn-danger ocultar',
				exportOptions: {
						columns: [0,1,2,3,4,5]
				},				
				customize: function ( doc ) {
					doc.content.splice( 1, 0, {
						margin: [ 0, 0, 0, 12 ],
						alignment: 'left',
						image: imagen,//esta se encuenta en el archivo main.js
						width:100,
                        height:45
					} );
				}
			}
		],
	});
	table_secuencia_fiscales_dashboard.search('').draw();
	$('#buscar').focus();
}
</script>