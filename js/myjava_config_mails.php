<script>
$(document).ready(pagination(1));getConsulta();getTipo();
 $(function(){
	  $('#for_main #nuevo_registro').on('click',function(e){
		e.preventDefault();
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		  $('#formulario_registros')[0].reset();
     	  $('#formulario_registros #pro').val('Registro');
		  $('#formulario_registros #nombre_registro').val('');
		  $('#edi').hide();
		  $('#reg').show();
   	      $('#formulario_registros #mensaje').html('');				
		  $('#formulario_registros #mensaje').removeClass('error');		  
		  $('#formulario_registros #mensaje').removeClass('bien');
		  $('#formulario_registros #mensaje').removeClass('alerta');
		  $('#formulario_registros').attr({ 'data-form': 'save' }); 
		  $('#formulario_registros #conf_pass').attr('readonly', false);
		  $('#formulario_registros #conf_tipo').attr('disabled', false);
		  $('#formulario_registros').attr({ 'action': '<?php echo SERVERURL; ?>php/conf_mail/agregar.php' });			  
		  getConsulta();
		  getTipo();
		  $('#registrar').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		  });
		}else{
				swal({
					title: "Acceso Denegado", 
					text: "No tiene permisos para ejecutar esta acción",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});					 
           }
	   });
	   
                      			   
	   $('#for_main #bs_regis').on('keyup',function(){
		  pagination(1);
   	      return false;
   });	
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#registrar").on('shown.bs.modal', function(){
        $(this).find('#formulario_registros #conf_servidor').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

function getConsulta(){
    var url = '<?php echo SERVERURL; ?>php/conf_mail/getConsulta.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){		
		    $('#formulario_registros #conf_smtp_secure').html("");
			$('#formulario_registros #conf_smtp_secure').html(data);
			$('#formulario_registros #conf_smtp_secure').selectpicker('refresh');			
		}			
     });		
}

function getTipo(){
    var url = '<?php echo SERVERURL; ?>php/conf_mail/getTipo.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){		
		    $('#formulario_registros #conf_tipo').html("");
			$('#formulario_registros #conf_tipo').html(data);	
			$('#formulario_registros #conf_tipo').selectpicker('refresh');			
		}			
     });		
}

function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/conf_mail/paginar.php';
	var dato = $('#for_main #bs_regis').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}

function agregar(){
	var url = '<?php echo SERVERURL; ?>php/conf_mail/agregar.php';
	
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario_registros').serialize(),
		success: function(registro){
			if (registro == 1){
				$('#formulario_registros')[0].reset();
				$('#formulario_registros #pro').val('Registro');
				swal({
					title: "Success", 
					text: "Registro almacenado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});	
				$('#registrar').modal('hide');
				getConsulta();
				getTipo();
				pagination(1);
			   return false;
			}else if (registro == 2){
			   $('#formulario_registros #mensaje').html('');				
				swal({
					title: "Error", 
					text: "Error al guardar el registro",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});				
			}else if (registro == 3){
			   $('#formulario_registros #mensaje').html('');				
				swal({
					title: "Error", 
					text: "Este registro ya existe",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});			   
			}else{
			   $('#formulario_registros #mensaje').html('');				
				swal({
					title: "Error", 
					text: "Error al procesar su solicitud",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});			
			}
		}
	});
	return false;	
}

function modificar(){
	var url = '<?php echo SERVERURL; ?>php/conf_mail/modificar.php';
	
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario_registros').serialize(),
		success: function(registro){
			if (registro == 1){
				$('#formulario_registros #pro').val('Edición');
				swal({
					title: "Success", 
					text: "Registro modificado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});	
				$('#registrar').modal('hide');
				pagination(1);
				return false;
			}else if (registro == 2){
			   $('#formulario_registros #mensaje').html('');				
				swal({
					title: "Error", 
					text: "Error al modificar el registro",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});				
			}else{
			   $('#formulario_registros #mensaje').html('');				
				swal({
					title: "Error", 
					text: "Error al procesar su solicitud",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});			
			}
		}
	});
	return false;		
}

function editarRegistro(id){
	$('#formulario_registros')[0].reset();		
	var url = '<?php echo SERVERURL; ?>php/conf_mail/editar.php';
		
	$.ajax({
		type:'POST',
		url:url,
		data:'id='+id,
		success: function(valores){
				var datos = eval(valores);
				$('#reg').hide();
				$('#edi').show();
				$('#formulario_registros #pro').val('Edicion');
				$('#formulario_registros #id_registro').val(id);
                $('#formulario_registros #conf_tipo').val(datos[0]);
				$('#formulario_registros #conf_tipo').selectpicker('refresh');
				$('#formulario_registros #conf_mail').val(datos[1]);				
				$('#formulario_registros #conf_servidor').val(datos[2]);
                $('#formulario_registros #conf_smtp_secure').val(datos[3]);
				$('#formulario_registros #conf_smtp_secure').selectpicker('refresh');
				$('#formulario_registros #conf_puerto').val(datos[4]);				
				$('#formulario_registros #conf_pass').val(datos[5]);
				$('#formulario_registros #conf_tipo').attr('disabled', true);
				
				$('#formulario_registros').attr({ 'data-form': 'update' }); 
				$('#formulario_registros').attr({ 'action': '<?php echo SERVERURL; ?>php/conf_mail/modificar.php' });
		  
				$('#registrar').modal({
					show:true,
					keyboard: false,
					backdrop:'static'
				});
			return false;
		}
	});
	return false;	
}

function modal_eliminar(id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){	
		swal({
			title: "¿Esta seguro?",
			text: "¿Desea eliminar el correo: " + consultarCorreo(id) + "",
			icon: "warning",
			buttons: {
				cancel: {
				text: "Cancelar",
				visible: true
				},
				confirm: {
				text: "¡Sí, Eliminar el correo!",
				className: "btn-warning"
				}
			},
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		}).then((willConfirm) => {
			if (willConfirm === true) {
				eliminarRegistro(id);
			}
		});		
	}else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});				 
	}	
}

function eliminarRegistro(id){
	var url = '<?php echo SERVERURL; ?>php/conf_mail/eliminar.php';
	
	$.ajax({
		type:'POST',
		url:url,
		data:'id='+id,
		success: function(registro){
			if (registro == 1){
				swal({
					title: "Success", 
					text: "Registro almacenado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});			       				
			   pagination(1);
			   return false;
			}else if (registro == 2){
				swal({
					title: "Error", 
					text: "Error al intentar eliminar el registro, por favor intente de nuevo",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				return false;			
			}else if (registro == 3){
				swal({
					title: "Error", 
					text: "Error al intentar eliminar el registro, cuenta con información almacenada",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				return false;			
			}else{
				swal({
					title: "Error", 
					text: "Error procesar su solicitud",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});	
				return false;				
			}
		}
	});
	return false;	
}

$(document).ready(function() {
	$('#for_main #consulta').on('change', function(){
          pagination(1);
    });					
});


//BOTONES DE ACCION
$('#formulario_registros #reg').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 if ($('#formulario_registros #nombre_registro').val() != "" && $('#formulario_registros #consulta_registro').val() != ""){
		e.preventDefault();
		agregar();			   
		return false;
	 }else{
       $('#formulario_registros #pro').val('Registro');	
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});	
	   return false;	   
	 }  
});

$('#formulario_registros #edi').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 if ($('#formulario_registros #nombre_registro').val() != "" && $('#formulario_registros #consulta_registro').val() != ""){
		e.preventDefault();
		modificar();			   
		return false;
	 }else{
		$('#formulario_registros #pro').val('Edición');		
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});			
		return false;	   
	 }  
});


function consultarCorreo(id){	
    var url = '<?php echo SERVERURL; ?>php/conf_mail/getCorreo.php';

	var entidad = '';

	if($('#for_main #consulta').val() == "" || $('#for_main #consulta').val() == null){
		entidad = 'motivo_traslado';
	}else{
		entidad = $('#for_main #consulta').val();
	}
	var resp;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'entidad='+entidad+'&id='+id,
		async: false,
		success:function(data){	
          resp = data;			  		  		  			  
		}
	});
	return resp;		
}

$("#test_confEmails").on("click", function(e){
	e.preventDefault();
	var server = $('#formulario_registros #conf_servidor').val();
	var correo = $('#formulario_registros #conf_mail').val();
	var password = $('#formulario_registros #conf_pass').val();
	var port = $('#formulario_registros #conf_puerto').val();
	var smtpSecure = $('#formulario_registros #conf_smtp_secure').val();

	testEmail(server, correo, password, port, smtpSecure)
});

function testEmail(server, correo, password, port, smtpSecure){
	var url = '<?php echo SERVERURL; ?>php/conf_mail/testEmail.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
		data:'server='+server+'&correo='+correo+'&password='+password+'&port='+port+'&smtpSecure='+smtpSecure,
        success: function(data){
			if(data == 1){
				swal({
					title: "Success",
					text: "Conexión realizada satisfactoriamente",
					icon: "success",
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});
		  }else{
			swal({
					title: "Error",
					text: "Credenciales invalidas, por favor corregir, también recuerde en su servidor de correo: Activar Aplicaciones poco seguras (SmtpClientAuthentication)",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera						  
		 	 });
		  }		
		}
     });	
}
</script>