<script>
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	$('.footer').show();
	$('.footer1').hide();
	getTotalFacturasDisponibles();
	setInterval('pagination(1)',22000);

	//LLAMADA A LAS FUNCIONES
	funciones();

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	$('#form_main #bs_regis').on('keyup',function(){
	  pagination(1);
	});

	$('#form_main #fecha_b').on('change',function(){
	  pagination(1);
	});

	$('#form_main #fecha_f').on('change',function(){
	  pagination(1);
	});

	$('#form_main #clientes').on('change',function(){
	  pagination(1);
	});

	$('#form_main #profesional').on('change',function(){
	  pagination(1);
	});

	$('#form_main #estado').on('change',function(){
	  pagination(1);
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

//INICIO FUNCION COBRAR
function pay(facturas_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		$('#formulario_facturacion')[0].reset();
		$("#formulario_facturacion #invoiceItem > tbody").empty();//limpia solo los registros del body
		var url = '<?php echo SERVERURL; ?>php/facturacion/editarFactura.php';
			$.ajax({
			type:'POST',
			url:url,
			data:'facturas_id='+facturas_id,
			success: function(valores){
				var datos = eval(valores);
				$('#formulario_facturacion #facturas_id').val(facturas_id);

				$('#formulario_facturacion #pacientes_id').val(datos[0]);
				$('#formulario_facturacion #pacientes_id').selectpicker('refresh');

				$('#formulario_facturacion #colaborador_id').val(datos[3]);
				$('#formulario_facturacion #colaborador_id').selectpicker('refresh');

				$('#formulario_facturacion #servicio_id').val(datos[5]);
				$('#formulario_facturacion #servicio_id').selectpicker('refresh');

				$('#formulario_facturacion #notes').val(datos[6]);

				$('#formulario_facturacion #fecha').attr("readonly", true);
				$('#formulario_facturacion #validar').attr("disabled", false);
				$('#formulario_facturacion #addRows').attr("disabled", false);
				$('#formulario_facturacion #removeRows').attr("disabled", false);
				$('#formulario_facturacion #validar').show();
				$('#formulario_facturacion #editar').hide();
				$('#formulario_facturacion #eliminar').hide();

				$('#formulario_facturacion #validar').show();
			    $('#formulario_facturacion #guardar').show();
			    $('#formulario_facturacion #guardar1').hide();

				$('#main_facturacion').hide();
				$('#label_acciones_factura').html("Factura");
				$('#facturacion').show();

				$('.footer').hide();
				$('.footer1').show();

				return false;
			}
		});

		var url = '<?php echo SERVERURL; ?>php/facturacion/editarFacturaDetalles.php';
		var isv_valor = 0.0;

		$.ajax({
			type:'POST',
			url:url,
			data:'facturas_id='+facturas_id,
			success:function(data){
				var datos = eval(data);
				for(var fila=0; fila < datos.length; fila++){
					var facturas_detalle_id = datos[fila]["facturas_detalle_id"];
					var productoID = datos[fila]["productos_id"];
					var productName = datos[fila]["producto"];
					var quantity = datos[fila]["cantidad"];
					var price = datos[fila]["precio"];
					var discount = datos[fila]["descuento"];
					var isv = datos[fila]["isv_valor"];
					var producto_isv = datos[fila]["producto_isv"];
					isv_valor = parseFloat(isv_valor) + parseFloat(datos[fila]["isv_valor"]);
					llenarTablaFactura(fila);
					$('#formulario_facturacion #invoiceItem #facturas_detalle_id_'+ fila).val(facturas_detalle_id);
					$('#formulario_facturacion #invoiceItem #productoID_'+ fila).val(productoID);
					$('#formulario_facturacion #invoiceItem #productName_'+ fila).val(productName);
					$('#formulario_facturacion #invoiceItem #quantity_'+ fila).val(quantity);
					$('#formulario_facturacion #invoiceItem #price_'+ fila).val(price);
					$('#formulario_facturacion #invoiceItem #discount_'+ fila).val(discount);
					$('#formulario_facturacion #invoiceItem #valor_isv_'+ fila).val(isv);
					$('#formulario_facturacion #invoiceItem #isv_'+ fila).val(data.producto_isv);
				}
				$('#formulario_facturacion #taxAmount').val(isv_valor);
				calculateTotal();
			}
		});
		return false;
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true
		});
	}

	//MOSTRAMOS EL FORMULARIO PARA EL METODO DE PAGO
}
//FIN FUNCION COBRAR

//INICIO FUNCION PARA OBTENER LAS FUNCIONES
function funciones(){
  pagination(1);
	getClientes();
	getProfesionales();
	getEstado();
	getPacientes();

	getServicio();
	getBanco();
	getPacientesFacturas();
	getColaboradoresFactura();
	getServicioFactura();

	getAseguradora();
	getFactEmpresas();
}
//FIN FUNCION PARA OBTENER LAS FUNCIONES

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/facturacion/paginar.php';

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

//INICIO FUNCION PARA OBTENER LOS PACIENTES
function getPacientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formularioFactura #paciente').html("");
			$('#formularioFactura #paciente').html(data);
        }
     });
}
//FIN FUNCION PARA OBTENER LOS PACIENTES

function getColaboradorConsulta(){
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

//INICIO FUNCION PARA OBTENER LOS BANCOS DISPONIBLES
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getEstado.php';

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

//INICIO FUNCION PARA OBTENER LOS BANCOS DISPONIBLES
function getBanco(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getBanco.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
					$('#formTransferenciaBill #bk_nm').html("");
					$('#formTransferenciaBill #bk_nm').html(data);

					$('#formChequeBill #bk_nm_chk').html("");
					$('#formChequeBill #bk_nm_chk').html(data);
        }
     });
}
//FIN FUNCION PARA OBTENER LOS BANCOS DISPONIBLES

//INICIO ENVIAR FACTURA POR CORREO ELECTRONICO
function mailBill(facturas_id){
	swal({
		title: "¿Estas seguro?",
		text: "¿Desea enviar este numero de factura: # " + getNumeroFactura(facturas_id) + "?",
		icon: "info",
		buttons: {
			cancel: {
			text: "Cancelar",
			visible: true
			},
			confirm: {
			text: "¡Sí, enviar la factura!",
			className: "btn-primary"
			}
		},
		closeOnClickOutside: false
	}).then((willConfirm) => {
		if (willConfirm === true) {
			sendMail(facturas_id);
		}
	});
}
//FIN ENVIAR FACTURA POR CORREO ELECTRONICO

//INICIO IMPRIMIR FACTURACION
function printBill(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/generaFactura.php?facturas_id='+facturas_id;
    window.open(url);
}
//FIN IMPRIMIR FACTURACION

function sendMail(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/correo_facturas.php';
	var bill = '';

	$.ajax({
	   type:'POST',
	   url:url,
	   async: false,
	   data:'facturas_id='+facturas_id,
	   success:function(data){
	      bill = data;
	      if(bill == 1){
				swal({
					title: "Success",
					text: "La factura ha sido enviada por correo satisfactoriamente",
					icon: "success",
				});
		  }
	  }
	});
	return bill;
}

function getNumeroFactura(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getNoFactura.php';
	var noFactura = '';

	$.ajax({
	   type:'POST',
	   url:url,
	   async: false,
	   data:'facturas_id='+facturas_id,
	   success:function(data){
			var datos = eval(data);
			noFactura = datos[0];
	  }
	});
	return noFactura;
}

function getNumeroNombrePaciente(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getNombrePaciente.php';
	var noFactura = '';

	$.ajax({
	   type:'POST',
	   url:url,
	   async: false,
	   data:'facturas_id='+facturas_id,
	   success:function(data){
			var datos = eval(data);
			noFactura = datos[0];
	  }
	});
	return noFactura;
}
//FIN ENVIAR FACTURA POR CORREO ELECTRONICO
//FIN FUNCIONES

/*
###########################################################################################################################################################
###########################################################################################################################################################
###########################################################################################################################################################
*/
/*															INICIO FACTURACIÓN				   															 */
//INICIOS FORMULARIOS
$('#acciones_atras').on('click', function(e){
	 e.preventDefault();
	 if($('#formulario_facturacion #pacientes_id').val() != "" || $('#formulario_facturacion #colaborador_id').val() != ""){
		swal({
			title: "Tiene datos en la factura",
			text: "¿Esta seguro que desea volver, recuerde que tiene información en la factura la perderá?",
			icon: "warning",
			buttons: {
				cancel: {
				text: "Cancelar",
				visible: true
				},
				confirm: {
				text: "¡Si, deseo volver!",
				className: "btn-warning"
				}
			},
			closeOnClickOutside: false
		}).then((willConfirm) => {
			if (willConfirm === true) {
				$('#main_facturacion').show();
				$('#label_acciones_factura').html("");
				$('#facturacion').hide();
				$('#acciones_atras').addClass("breadcrumb-item active");
				$('#acciones_factura').removeClass("active");
				$('#formulario_facturacion')[0].reset();
				swal.close();
				$('.footer').show();
				$('.footer1').hide();
			}
		});
	 }else{
		 $('#main_facturacion').show();
		 $('#label_acciones_factura').html("");
		 $('#facturacion').hide();
		 $('#acciones_atras').addClass("breadcrumb-item active");
		 $('#acciones_factura').removeClass("active");
		 $('.footer').show();
     	 $('.footer1').hide();
	 }
});

$('#main_facturacion #factura').on('click', function(e){
	e.preventDefault();
	formFactura();
});

function modal_pagos(){
	$('#modal_pagos').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});
}

function formFactura(){
	 $('#formulario_facturacion')[0].reset();
	 $('#main_facturacion').hide();
	 $('#facturacion').show();
	 $('#label_acciones_volver').html("Facturación");
	 $('#acciones_atras').removeClass("active");
	 $('#acciones_factura').addClass("active");
	 $('#label_acciones_factura').html("Factura");
	 $('#formulario_facturacion #fecha').attr('disabled', false);
	 limpiarTabla();
	 $('.footer').hide();
     $('.footer1').show();
	 cleanFooterValueBill();
	 $('#formulario_facturacion #validar').show();
	 $('#formulario_facturacion #guardar').hide();
	 $('#formulario_facturacion #guardar1').show();;
}

$(document).ready(function() {
	$('#label_acciones_volver').html("Facturación");
	$('#acciones_atras').addClass("active");
	$('#label_acciones_factura').html("");
	$('.footer').show();
    $('.footer1').hide();
});
//FIN BUSQUEDA PACIENTES

//INICIO MODAL PAGOS
function pago(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/editarPago.php';

	$.ajax({
		type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		success: function(valores){
			var datos = eval(valores);
			$('#formEfectivoBill .border-right a:eq(0) a').tab('show');
			$("#customer-name-bill").html("<b>Cliente:</b> " + datos[0]);
		    $("#customer_bill_pay").val(datos[2]);
			$('#bill-pay').html("L. " + parseFloat(datos[2]).toFixed(2));

			//EFECTIVO
			$('#formEfectivoBill')[0].reset();
			$('#formEfectivoBill #monto_efectivo').val(datos[2]);
			$('#formEfectivoBill #factura_id_efectivo').val(facturas_id);
			$('#formEfectivoBill #pago_efectivo').attr('disabled', true);

			//TARJETA
			$('#formTarjetaBill')[0].reset();
			$('#formTarjetaBill #monto_efectivo').val(datos[2]);
			$('#formTarjetaBill #factura_id_tarjeta').val(facturas_id);
			$('#formTarjetaBill #pago_efectivo').attr('disabled', true);

			//MIXTO
			$('#formMixtoBill')[0].reset();
			$('#formMixtoBill #monto_efectivo_mixto').val(datos[2]);
			$('#formMixtoBill #factura_id_mixto').val(facturas_id);
			$('#formMixtoBill #pago_efectivo_mixto').attr('disabled', true);

			//TRANSFERENCIA
			$('#formTransferenciaBill')[0].reset();
			$('#formTransferenciaBill #monto_efectivo').val(datos[2]);
			$('#formTransferenciaBill #factura_id_transferencia').val(facturas_id);
			$('#formTransferenciaBill #pago_efectivo').attr('disabled', true);

			//CHEQUES
			$('#formChequeBill')[0].reset();
			$('#formChequeBill #monto_efectivo').val(datos[2]);
			$('#formChequeBill #factura_id_cheque').val(facturas_id);
			$('#formChequeBill #pago_efectivo').attr('disabled', true);

			$('#modal_pagos').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});

			return false;
		}
	});
}

$(document).ready(function(){
	$("#tab1").on("click", function(){
		$("#modal_pagos").on('shown.bs.modal', function(){
           $(this).find('#formTarjetaBill #efectivo_bill').focus();
		});
	});

	$("#tab2").on("click", function(){
		$("#modal_pagos").on('shown.bs.modal', function(){
           $(this).find('#formTarjetaBill #cr_bill').focus();
		});
	});

	$("#tab3").on("click", function(){
		$("#modal_pagos").on('shown.bs.modal', function(){
           $(this).find('#formTarjetaBill #bk_nm').focus();
		});
	});

	$("#tab4").on("click", function(){
		$("#modal_pagos").on('shown.bs.modal', function(){
           $(this).find('#formChequeBill #bk_nm_chk').focus();
		});
	});

	$("#tab5").on("click", function(){
		$("#modal_pagos").on('shown.bs.modal', function(){
           $(this).find('#formMixtoBill #efectivo_bill_mixto').focus();
		});
	});
});

$(document).ready(function(){
	$('#formTarjetaBill #cr_bill').inputmask("9999");
});

$(document).ready(function(){
	$('#formTarjetaBill #exp').inputmask("99/99");
});

$(document).ready(function(){
	$('#formTarjetaBill #cvcpwd').inputmask("999999");
});

// MIXTO
$(document).ready(function(){
	$('#formMixtoBill #cr_bill_mixto').inputmask("9999");
});

$(document).ready(function(){
	$('#formMixtoBill #exp_mixto').inputmask("99/99");
});

$(document).ready(function(){
	$('#formMixtoBill #cvcpwd_mixto').inputmask("999999");
});


$(document).ready(function(){
	$("#formEfectivoBill #efectivo_bill").on("keyup", function(){
		var efectivo = parseFloat($("#formEfectivoBill #efectivo_bill").val()).toFixed(2);
		var monto = parseFloat($("#formEfectivoBill #monto_efectivo").val()).toFixed(2);

		var total = efectivo - monto;

		if(Math.floor(efectivo*100) >= Math.floor(monto*100)){
			$('#formEfectivoBill #cambio_efectivo').val(parseFloat(total).toFixed(2));
			$('#formEfectivoBill #pago_efectivo').attr('disabled', false);
		}else{
			$('#formEfectivoBill #cambio_efectivo').val(parseFloat(0).toFixed(2));
			$('#formEfectivoBill #pago_efectivo').attr('disabled', true);
		}
	});

	//MIXTO
	$("#formMixtoBill #efectivo_bill_mixto").on("keyup", function(){
		var efectivo = parseFloat($("#formMixtoBill #efectivo_bill_mixto").val()).toFixed(2);
		var monto = parseFloat($("#formMixtoBill #monto_efectivo_mixto").val()).toFixed(2);

		var total = efectivo - monto;

		if(Math.floor(efectivo*100) >= Math.floor(monto*100)){
			$('#formMixtoBill #pago_efectivo_mixto').attr('disabled', true);
			$('#formMixtoBill #monto_tarjeta').val(parseFloat(0).toFixed(2));
			$('#formMixtoBill #monto_tarjeta').attr('disabled', true);
		}else{
			var tarjeta = monto - efectivo;
			$('#formMixtoBill #monto_tarjeta').val(parseFloat(tarjeta).toFixed(2))
			$('#formMixtoBill #cambio_efectivo_mixto').val(parseFloat(0).toFixed(2));
			$('#formMixtoBill #pago_efectivo_mixto').attr('disabled', false);
		}
	});
});
//FIN MODAL PAGOS

//INCIO ELIMINAR FACTURA BORRADOR
function deleteBill(facturas_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		swal({
			title: "¿Estas seguro?",
			text: "¿Desea eliminar la factura para el paciente: " + getNumeroNombrePaciente(facturas_id) + "?",
			icon: "warning",
			buttons: {
				cancel: {
				text: "Cancelar",
				visible: true
				},
				confirm: {
				text: "¡Si, deseo volver!",
				className: "btn-warning"
				}
			},
			closeOnClickOutside: false
		}).then((willConfirm) => {
			if (willConfirm === true) {
				eliminarFacturaBorrador(facturas_id);
			}
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true
		});
	}
}

function eliminarFacturaBorrador(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/eliminar.php';
	$.ajax({
		type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		success: function(registro){
			if(registro == 1){
				swal({
					title: "Success",
					text: "Registro eliminado correctamente",
					icon: "success",
					timer: 3000,
				});
				pagination(1);
			   return false;
			}else if(registro == 2){
				swal({
					title: "Error al eliminar el registro, por favor intentelo de nuevo o verifique que no tenga información almacenada",
					text: "No tiene permisos para ejecutar esta acción",
					icon: "error",
					dangerMode: true
				});
			    return false;
			}else{
				swal({
					title: "No se puede procesar su solicitud, por favor intentelo de nuevo mas tarde",
					text: "No tiene permisos para ejecutar esta acción",
					icon: "error",
					dangerMode: true
				});
			    return false;
			}
  		}
	});
	return false;
}

function volver(){
	$('#main_facturacion').show();
	$('#label_acciones_factura').html("");
	$('#facturacion').hide();
	$('#acciones_atras').addClass("breadcrumb-item active");
	$('#acciones_factura').removeClass("active");
	$('.footer').show();
	$('.footer1').hide();
}

function cierreCaja(){
	$('#formularioCierreCaja #pro').val("Cierre de Caja");

	$('#modalCierreCaja').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});

	$('#formularioCierreCaja').attr({ 'data-form': 'save' });
	$('#formularioCierreCaja').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addPago.php' });
}

$('#form_main #cierre').on('click', function(e){
	e.preventDefault();
	cierreCaja();
});

$('#generarCierreCaja').on('click', function(e){
	e.preventDefault();
	var fecha = $('#formularioCierreCaja #fechaCierreCaja').val();
	var url = '<?php echo SERVERURL; ?>php/facturacion/generaCierreCaja.php?fecha='+fecha;
    window.open(url);
	$('#modalCierreCaja').modal('hide');
});
//FIN ELIMINAR FACTURA BORRADOR

//INICIO CONTROLES MODAL PAGO
$(".menu-toggle1").on("click", function(e){
	e.preventDefault();
	$(".menu-toggle1").hide();
	$(".menu-toggle2").show();
});

$(".menu-toggle2").on("click", function(e){
	e.preventDefault();
	$(".menu-toggle2").hide();
	$(".menu-toggle1").show();
});

//Menu Toggle Script
$("#menu-toggle1").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});

$("#menu-toggle2").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
});

$(document).ready(function(){
	$(".menu-toggle2").hide();
	$("#tab1").addClass("active1");
	$("#sidebar-wrapper").toggleClass("toggled");

	//Menu Toggle Script
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});

	// For highlighting activated tabs
	$("#tab1").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab1").addClass("active1");
		$("#tab1").removeClass("bg-light");
	});

	$("#tab2").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab2").addClass("active1");
		$("#tab2").removeClass("bg-light");
	});

	$("#tab3").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab3").addClass("active1");
		$("#tab3").removeClass("bg-light");
	});

	$("#tab4").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab4").addClass("active1");
		$("#tab4").removeClass("bg-light");
	});

	$("#tab5").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab5").addClass("active1");
		$("#tab5").removeClass("bg-light");
	});
})
//FIN CONTROLES MODAL PAGO
/*														 	FIN FACTURACIÓN				   															 	*/
/*
###########################################################################################################################################################
###########################################################################################################################################################
###########################################################################################################################################################
*/

function getTotalFacturasDisponibles(){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getTotalFacturasDisponibles.php';

	$.ajax({
	   type:'POST',
	   url:url,
	   async: false,
	   success:function(registro){
			var valores = eval(registro);
			var mensaje = "";
			if(valores[0] >=10 && valores[0] <= 30){
				mensaje = "Total Facturas disponibles: " + valores[0];
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-warning");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-danger");
				$("#mensajeFacturas").attr("disabled", true);
				$("#formulario_facturacion #validar").attr("disabled", false);
				$("#formulario_facturacion #guardar").attr("disabled", false);
				$("#formulario_facturacion #guardar1").attr("disabled", false);
			}else if(valores[0] >=0 && valores[0] <= 9){
				mensaje = "Total Facturas disponibles: " + valores[0];
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
				$("#mensajeFacturas").attr("disabled", true);
				$("#formulario_facturacion #validar").attr("disabled", false);
				$("#formulario_facturacion #guardar").attr("disabled", false);
				$("#formulario_facturacion #guardar1").attr("disabled", false);
			}
			else{
				mensaje = "";
				$("#formulario_facturacion #validar").attr("disabled", false);
				$("#formulario_facturacion #guardar").attr("disabled", false);
				$("#formulario_facturacion #guardar1").attr("disabled", false);
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
			}

			if(valores[0] ==0){
				mensaje = "Total Facturas disponibles: " + valores[0];
				mensaje += "<br/>Solo esta factura puede realizar";
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
				$("#mensajeFacturas").attr("disabled", true);
				$("#formulario_facturacion #validar").attr("disabled", false);
				$("#formulario_facturacion #guardar").attr("disabled", false);
				$("#formulario_facturacion #guardar1").attr("disabled", false);
			}

			if(valores[0] < 0){
				mensaje = "No puede seguir facturando";
				$("#formulario_facturacion #validar").attr("disabled", true);
				$("#formulario_facturacion #guardar").attr("disabled", true);
				$("#formulario_facturacion #guardar1").attr("disabled", true);
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
			}

			if(valores[1] == 1){
				mensaje += "<br/>Su fecha límite es: " + valores[2];
				$("#formulario_facturacion #validar").attr("disabled", false);
				$("#formulario_facturacion #guardar").attr("disabled", false);
				$("#formulario_facturacion #guardar1").attr("disabled", false);
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-warning");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-danger");
			}

			if(valores[1] == 0){
				mensaje += "<br/>Su fecha limite de facturación es hoy";
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
			}

			if(valores[1] < 0){
				mensaje += "<br/>Ya alcanzo su fecha límite";
				$("#formulario_facturacion #validar").attr("disabled", true);
				$("#formulario_facturacion #guardar").attr("disabled", true);
				$("#formulario_facturacion #guardar1").attr("disabled", true);
				$("#mensajeFacturas").html(mensaje).addClass("alert alert-danger");
				$("#mensajeFacturas").html(mensaje).removeClass("alert alert-warning");
			}
	   }
	});
}

setInterval('getTotalFacturasDisponibles()',1000);

//BOTON COBRAR
$('#formulario_facturacion #validar').on('click', function(e){
	$('#formulario_facturacion').attr({ 'data-form': 'save' });
	$('#formulario_facturacion').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addFactura.php' });
	$("#formulario_facturacion").submit();
});

//BOTON GUARDAR Y COBRAR LUEGO
$('#formulario_facturacion #guardar').on('click', function(e){
	$('#formulario_facturacion').attr({ 'data-form': 'save' });
	$('#formulario_facturacion').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addFacturaporUsuarioGuardar.php' });
	$("#formulario_facturacion").submit();
});

//BOTON SOLO GUARDAR
$('#formulario_facturacion #guardar1').on('click', function(e){
	$('#formulario_facturacion').attr({ 'data-form': 'save' });
	$('#formulario_facturacion').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php' });
	$("#formulario_facturacion").submit();
});

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

var listar_facturas = function(){
	var estado = "";
    var paciente = "";

    if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
        estado = 1;
    } else {
        estado = $('#form_main #estado').val();
    }

    if ($('#form_main #tipo').val() == "" || $('#form_main #tipo').val() == null) {
        paciente = 1;
    } else {
        paciente = $('#form_main #tipo').val();
    }
	
	var table_pacientes  = $("#dataTablePacientesMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/pacientes/llenarDataTablePacientes.php",
            "data": function(d) {
                d.estado = estado;
                d.paciente = paciente;
            }		
		},		
		"columns":[
			{"data": "paciente"},
			{
				"data": "expediente_",
				"render": function(data, type, row) {
					return '<a href="#" class="showExpedienteLink">' + data + '</a>';
				}
			},
			{"data": "identidad"},
			{"data": "edad"},			
			{"data": "telefono1"},
			{"data": "identidad"},
			{"data": "edad"},			
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item showExpediente" href="#"><i class="fas fa-eye fa-lg"></i> Información del Paciente</a>' +
							'<a class="dropdown-item addExpediente" href="#"><i class="fas fa-plus fa-lg"></i> Agregar Expediente</a>' +
							'<a class="dropdown-item addIdentidad" href="#"><i class="fas fa-edit fa-lg"></i> Editar Identidad Paciente</a>' +
							'<a class="dropdown-item editar" href="#"><i class="fas fa-user-edit fa-lg"></i> Editar Paciente</a>' +
							'<a class="dropdown-item delete" href="#"><i class="fas fa-trash fa-lg"></i> Eliminar Paciente</a>' +
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
					listar_pacientes();
				}
			},		
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Crear Pacientes',
				titleAttr: 'Agregar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					addPacientes();
				}
			},	
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Crear Profesion',
				titleAttr: 'Agregar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					addProfesion();
				}
			},		
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Pacientes',
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
				title: 'Reporte Pacientes',
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
				title: 'Reporte Productos',
				className: 'btn btn-secondary',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
			}
		]		
	});	 
	table_pacientes.search('').draw();
	$('#buscar').focus();
	
	//show_expediente_link_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	//show_expediente_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	//add_expediente_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	//add_identidad_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	//edit_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	//delete_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
}
</script>
