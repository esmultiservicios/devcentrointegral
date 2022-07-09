<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modalEmpresa").on('shown.bs.modal', function(){
        $(this).find('#formularioEmpresa #empresa').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	//LLAMADA A LAS FUNCIONES
	funciones();	
	
	//INICIO ABRIR VENTANA MODAL PARA EL REGISTRO DE DESCUENTOS
	$('#form_main #nuevo_registro').on('click',function(){
		funciones();
		limpiar();
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){		
			 $('#reg').show();
			 $('#edi').hide();

			 $('#formularioAseguradora').attr({ 'data-form': 'save' });
			 $('#formularioAseguradora').attr({ 'action': '<?php echo SERVERURL; ?>php/aseguradora/agregarAseguradora.php' });			

             $('#formularioAseguradora #pro').val("Registro");			 
			 $('#modalAseguradora').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});
			return false;
		}else{
			swal({
				title: "Acceso Denegado", 
				text: "No tiene permisos para ejecutar esta acción",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});			 
		}			
	});
	//FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE DESCUENTOS
	
    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	$('#form_main #bs_regis').on('keyup',function(){
	  pagination(1);
	}); 

	$('#form_main #profesional').on('change',function(){
	  pagination(1);
	});
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});
//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/


/***************************************************************************************************************************************************************************/
//INICIO FUNCIONES
function modal_eliminar(aseguradora_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){	
		swal({
		  title: "¿Esta seguro?",
		  text: "¿Desea remover este usuario este registro?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",
		  confirmButtonText: "¡Sí, removerlo!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			eliminarRegistro(aseguradora_id, inputValue);
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

function eliminarRegistro(aseguradora_id, inputValue){
	var url = '<?php echo SERVERURL; ?>php/aseguradora/eliminar.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:'aseguradora_id='+aseguradora_id+'&comentario='+inputValue,
		success: function(registro){
			if(registro == 1){ 			   
				swal({
					title: "Success", 
					text: "Registro eliminado correctamente",
					type: "success",
					timer: 3000, //timeOut for auto-close
				});
				pagination(1);
				getEmpresa();
				return false;				
			}else if(registro == 2){
				swal({
					title: "Error", 
					text: "Error, no se puede eliminar este registro",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});
				return false;				
			}else if(registro == 3){
				swal({
					title: "Error", 
					text: "Lo sentimos este registro cuenta con información almacenada, no se puede eliminar",
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
//FIN FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES QUE NO ESTAN ALMACENADOS EN LA AGENDA

function editarRegistro(aseguradora_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){	
		$('#formularioAseguradora')[0].reset();		
		var url = '<?php echo SERVERURL; ?>php/aseguradora/editar.php';

			$.ajax({
			type:'POST',
			url:url,
			data:'aseguradora_id='+aseguradora_id,
			success: function(valores){
				var array = eval(valores);
				$('#reg').hide();
				$('#edi').show();
				$('#formularioAseguradora #pro').val('Registro');
                $('#formularioAseguradora #aseguradora_id').val(aseguradora_id);
				$('#formularioAseguradora #aseguradora').val(array[0]);			
                $('#formularioAseguradora #rtn_aseguradora').val(array[1]);
				
				$('#formularioAseguradora').attr({ 'data-form': 'update' });
			 	$('#formularioAseguradora').attr({ 'action': '<?php echo SERVERURL; ?>php/aseguradora/modificarAseguradora.php' });	

				$('#modalAseguradora').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
				return false;
			}
		});
		return false;
	}else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});						 
	}		
}

//INICIO FUNCION PARA OBTENER LOS COLABORADORES	
function funciones(){
    pagination(1);
	limpiar();
}

function limpiar(){
	$('#formularioEmpresa #pro').val("Registro");
	$('#formularioEmpresa #aseguradora').val("");
	$('#formularioEmpresa #rtn_aseguradora').val("");
}

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/aseguradora/paginar.php';
	var dato = '';
	var empresa = '';
	
    if($('#form_main #empresa').val() == "" || $('#form_main #empresa').val() == null){
		empresa = 0;
	}else{
		empresa = $('#form_main #empresa').val();
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
		data:'partida='+partida+'&dato='+dato+'&empresa='+empresa,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE REGISTROS

$(document).ready(function(){
    $("#modalAseguradora").on('shown.bs.modal', function(){
        $(this).find('#formularioAseguradora #aseguradora').focus();
    });
});
</script>