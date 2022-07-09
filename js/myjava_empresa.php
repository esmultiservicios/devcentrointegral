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
             $('#formularioEmpresa #pro').val("Registro");			 
			 $('#modalEmpresa').modal({
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
	
	//INICIO REGISTRAR LOS DESCUENTOS 
	$('#reg').on('click', function(e){
		 if ($('#formularioEmpresa #empresa').val() != "" && $('#formulario_descuento #cai').val() != "" && $('#formulario_descuento #rtn').val() != "" && $('#formulario_descuento #direccion').val() != ""){					 
			e.preventDefault();
			agregar();	
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
	//FIN REGISTRAR LOS DESCUENTOS 
	
	//INICIO EDITAR LOS DESCUENTOS 
	$('#edi').on('click', function(e){
		 if ($('#formularioEmpresa #empresa').val() != "" && $('#formulario_descuento #cai').val() != "" && $('#formulario_descuento #rtn').val() != "" && $('#formulario_descuento #direccion').val() != ""){					 
			e.preventDefault();
			agregarRegistro();	
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
    //FIN EDITAR LOS DESCUENTOS 
	
	//FIN VENTANA MODAL PARA REGISTRAR LOS DESCUENTOS 
	
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

//INICIO FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES QUE NO ESTAN ALMACENADOS EN LA AGENDA
function agregar(){
	var url = '<?php echo SERVERURL; ?>php/empresas/agregar.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formularioEmpresa').serialize(),
		success: function(registro){
			if(registro == 1){
				$('#formularioEmpresa')[0].reset();  			   
				swal({
					title: "Success", 
					text: "Registro almacenado correctamente",
					type: "success",
					timer: 3000, //timeOut for auto-close
				});	
				$('#modalEmpresa').modal('hide');
				pagination(1);
				getEmpresa();
				$('#formularioEmpresa #pro').val('Registro');
				return false;				
			}else if(registro == 2){
				swal({
					title: "Error", 
					text: "Error, no se puede almacenar este registro",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});	
			    return false;				
			}else if(registro == 3){
				swal({
					title: "Error", 
					text: "Lo sentimos este registro ya existe",
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

function agregarRegistro(){
	var url = '<?php echo SERVERURL; ?>php/empresas/agregarRegistro.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formularioEmpresa').serialize(),
		success: function(registro){
			if(registro == 1){ 			   
				swal({
					title: "Success", 
					text: "Registro almacenado correctamente",
					type: "success",
					timer: 3000, //timeOut for auto-close
				});
				$('#modalEmpresa').modal('hide');
				pagination(1);
				getEmpresa();
				$('#formularioEmpresa #pro').val('Registro');
				return false;				
			}else if(registro == 2){
				swal({
					title: "Error", 
					text: "Error, no se puede modifciar este registro",
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

function eliminarRegistro(empresa_id, inputValue){
	var url = '<?php echo SERVERURL; ?>php/empresas/eliminar.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:'empresa_id='+empresa_id+'&comentario='+inputValue,
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
			}else if(registro == 4){
				swal({
					title: "Error", 
					text: "No se puede eliminar esta empresa, ya que su usuario pertenece a ella",
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

function editarRegistro(empresa_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){	
		$('#formularioEmpresa')[0].reset();		
		var url = '<?php echo SERVERURL; ?>php/empresas/editar.php';

			$.ajax({
			type:'POST',
			url:url,
			data:'empresa_id='+empresa_id,
			success: function(valores){
				var array = eval(valores);
				$('#reg').hide();
				$('#edi').show();
				$('#formularioEmpresa #pro').val('Registro');
                $('#formularioEmpresa #empresa_id').val(empresa_id);
				$('#formularioEmpresa #empresa').val(array[0]);			
                $('#formularioEmpresa #rtn').val(array[1]);
                $('#formularioEmpresa #telefono').val(array[2]);
                $('#formularioEmpresa #correo').val(array[3]);				
                $('#formularioEmpresa #direccion').val(array[4]);
                $('#formularioEmpresa #otra_info').val(array[5]);
                $('#formularioEmpresa #eslogan').val(array[6]);				
                $('#formularioEmpresa #celular').val(array[7]);	
                $('#formularioEmpresa #horario_atencion').val(array[8]);				
                $('#formularioEmpresa #facebook_empresa').val(array[9]);					
                $('#formularioEmpresa #sitioweb_empresa').val(array[10]);	
				
				$('#modalEmpresa').modal({
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
	$('#formularioEmpresa #empresa').val("");
	$('#formularioEmpresa #cai').val("");
	$('#formularioEmpresa #rtn').val("");
	$('#formularioEmpresa #telefono').val("");
	$('#formularioEmpresa #correo').val("");	
	$('#formularioEmpresa #direccion').val("");	
	getEmpresa();
}

function modal_eliminar(empresa_id){
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
			eliminarRegistro(empresa_id, inputValue);
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

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/empresas/paginar.php';
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

function getEmpresa(){
    var url = '<?php echo SERVERURL; ?>php/empresas/getEmpresa.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #empresa').html("");
			$('#form_main #empresa').html(data);			
        }
     });		
}
//FIN FUNCION PARA OBTENER LOS COLABORADORES

//FIN FUNCIONES
/***************************************************************************************************************************************************************************/

$(document).ready(function(){
// Prepare the preview for profile picture
    $("#wizard-picture").change(function(){
        readURL(this);
    });
});
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>