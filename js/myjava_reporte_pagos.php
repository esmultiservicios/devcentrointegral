<script>
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	pagination(1);
	getColaborador();
	getEstado();
	getTipoPago();
	
	$('#form_main #bs_regis').on('keyup',function(){
	  pagination(1);
	});

	$('#form_main #fecha_b').on('change',function(){
	  pagination(1);
	});

	$('#form_main #fecha_f').on('change',function(){
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

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/reporte_pagos/paginar.php';
    var fechai = $('#form_main #fecha_b').val();
	var fechaf = $('#form_main #fecha_f').val();
	var dato = '';
	var profesional = '';
	var estado = '';
	
    if($('#form_main #profesional').val() == "" || $('#form_main #profesional').val() == null){
		profesional = '';
	}else{
		profesional = $('#form_main #profesional').val();
	}
	
    if($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null){
		estado = 1;
	}else{
		estado = $('#form_main #estado').val();
	}	
	
	if($('#form_main #bs_regis').val() == "" || $('#form_main #bs_regis').val() == null){
		dato = '';
	}else{
		dato = $('#form_main #bs_regis').val();
	}

	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:'partida='+partida+'&fechai='+fechai+'&fechaf='+fechaf+'&dato='+dato+'&profesional='+profesional+'&estado='+estado,
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
		}			
     });	
}	
//FIN FUNCION PARA OBTENER LOS PROFESIONALES

$('#form_main #reporte').on('click', function(e){
    e.preventDefault();
    reporteEXCEL();
});

//INICIO REPORTE DE FACTURACION
function reporteEXCEL(){
	var profesional = '';
	var desde = $('#form_main #fecha_b').val();
	var hasta = $('#form_main #fecha_f').val();
	var url = '';
	var dato = '';
		
    if($('#form_main #profesional').val() == "" || $('#form_main #profesional').val() == null){
		profesional = '';
	}else{
		profesional = $('#form_main #profesional').val();
	}
	
	if($('#form_main #bs_regis').val() == "" || $('#form_main #bs_regis').val() == null){
		dato = '';
	}else{
		dato = $('#form_main #bs_regis').val();
	}
	
	url = '<?php echo SERVERURL; ?>php/reporte_pagos/reporte.php?desde='+desde+'&hasta='+hasta+'&profesional='+profesional+'&dato='+dato;

	window.open(url);
}


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
				
        }
     });		
}
</script>