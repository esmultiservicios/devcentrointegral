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
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){		
			 $('#reg').show();
			 $('#edi').hide();

			 $('#formularioEmpresa').attr({ 'data-form': 'save' });
			 $('#formularioEmpresa').attr({ 'action': '<?php echo SERVERURL; ?>php/reg_empresas/agregarRegistro.php' });			

             $('#formularioEmpresa #pro').val("Registro");			 
			 $('#modalEmpresas').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
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
function modal_eliminar(fact_empresas_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
		swal({
			title: "¿Esta seguro?",
			text: "¿Desea remover este usuario este registro?",
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
				text: "¡Sí, remover la empresa!",
				closeModal: false,
				},
			},
		}).then((value) => {
			if (value === null || value.trim() === "") {
				swal("¡Necesita escribir algo!", { icon: "error" });
				return false;
			}
			eliminarRegistro(fact_empresas_id, value);
			swal("Empresa removida", "La empresa ha sido eliminado correctamente.", "success");
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

function eliminarRegistro(fact_empresas_id, inputValue){
	var url = '<?php echo SERVERURL; ?>php/reg_empresas/eliminar.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:'fact_empresas_id='+fact_empresas_id+'&comentario='+inputValue,
		success: function(registro){
			if(registro == 1){ 			   
				swal({
					title: "Success", 
					text: "Registro eliminado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
				});
				pagination(1);
				getEmpresa();
				return false;				
			}else if(registro == 2){
				swal({
					title: "Error", 
					text: "Error, no se puede eliminar este registro",
					icon: "error", 
					dangerMode: true
				});
				return false;				
			}else if(registro == 3){
				swal({
					title: "Error", 
					text: "Lo sentimos este registro cuenta con información almacenada, no se puede eliminar",
					icon: "error", 
					dangerMode: true
				});
				return false;				
			}else{
				swal({
					title: "Error", 
					text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
					icon: "error", 
					dangerMode: true
				});
				return false;	
			}
		}
	});
	return false;
}
//FIN FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES QUE NO ESTAN ALMACENADOS EN LA AGENDA

function editarRegistro(fact_empresas_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
		$('#formularioEmpresa')[0].reset();		
		var url = '<?php echo SERVERURL; ?>php/reg_empresas/editar.php';

			$.ajax({
			type:'POST',
			url:url,
			data:'fact_empresas_id='+fact_empresas_id,
			success: function(valores){
				var array = eval(valores);
				$('#reg').hide();
				$('#edi').show();
				$('#formularioEmpresa #pro').val('Registro');
                $('#formularioEmpresa #fact_empresas_id').val(fact_empresas_id);
				$('#formularioEmpresa #empresa').val(array[0]);			
                $('#formularioEmpresa #rtn_empresa').val(array[1]);
				
				$('#formularioEmpresa').attr({ 'data-form': 'update' });
			 	$('#formularioEmpresa').attr({ 'action': '<?php echo SERVERURL; ?>php/reg_empresas/modificarRegistro.php' });	

				$('#modalEmpresas').modal({
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
			icon: "error", 
			dangerMode: true
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
	var url = '<?php echo SERVERURL; ?>php/reg_empresas/paginar.php';
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
    $("#modalEmpresas").on('shown.bs.modal', function(){
        $(this).find('#formularioEmpresa #empresa').focus();
    });
});
</script>