<script>
$(document).ready(function() {
   getServicio();
   getProfesionales();
   getAtencion();
   getStatusRepro();
   getHoraNueva();
   pagination(1);
});

$(document).ready(function() {
  $('#form_agenda_main #servicio').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_agenda_main #medico_general').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_agenda_main #fecha').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_agenda_main #fechaf').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_agenda_main #bs-regis').on('keyup', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_agenda_main #atencion').on('change', function(){
     pagination(1);
  });
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#eliminar").on('shown.bs.modal', function(){
        $(this).find('#form_ausencia #motivo_ausencia').focus();
    });
});

$(document).ready(function(){
    $("#eliminar_cita").on('shown.bs.modal', function(){
        $(this).find('#form-eliminarcita #comentario').focus();
    });
});

/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#form_agenda_main #reporte').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
	e.preventDefault();
	if($('#form_agenda_main #servicio').val() != ""){
	   reporteEXCEL();
	}else{
		swal({
			title: "Error", 
			text: "Error al exportar, debe seleccionar el servicio",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	
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

$('#form_agenda_main #Reporte_Agenda').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
	e.preventDefault();
	if($('#form_agenda_main #servicio').val() != ""){
	   reporteEXCELReporte();
	}else{
		swal({
			title: "Error", 
			text: "Error al exportar, debe seleccionar el servicio",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	
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


$('#form_agenda_main #reporte_sms').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
   if($('#form_agenda_main #servicio').val() != ""){
	   e.preventDefault();
	   reporteSMS();
   }else{
		swal({
			title: "Error", 
			text: "Debe seleccionar un servicio antes de continuar",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	  
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

$('#form_agenda_main #reporte_smsDiasAntes').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
   if($('#form_agenda_main #servicio').val() != ""){
	   e.preventDefault();
	   reporteSMSDiasAntes();
   }else{
		swal({
			title: "Error", 
			text: "Debe seleccionar un servicio antes de continuar",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	 	   
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

$('#formulario #edi1').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	if ($('#formulario #comentario').val()!= ""){
	    e.preventDefault();
	    agregaRegistroComentario(); 
	}else{
		swal({
			title: "Error", 
			text: "El comentario no puede estar vacío",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});
		return false;
	}
});

$('#form_agenda_main #agenda_usuarios').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
	e.preventDefault();
	if($('#form_agenda_main #servicio').val() != ""){
	   	reporteExcelAgenda();
	}else{
		swal({
			title: "Error", 
			text: "Error al exportar, debe seleccionar el servicio",
			type: "error", 
			confirmButtonClass: "btn-danger"
		});	
	}	
}else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: "btn-danger"
	});					 
}	
});

function clean(){
	getHoraNueva();
	getStatusRepro();
}

function agregaRegistroComentario(){
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/agregar_comentario.php';		
	
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario').serialize(),
		success: function(registro){
            if ($('#pro').val() == 'Edicion'){
			  if(registro == 1){
					swal({
						title: "Error", 
						text: "El médico ya tiene ocupada esa hora, por favor corregir",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});
				   return false; 
			  }else{
					swal({
						title: "Success", 
						text: "Comentario almacenado correctamente",
						type: "success",
						timer: 3000, //timeOut for auto-close
					});	
					$('#registrar').modal('hide');
					pagination(1);					   			  
					return false; 
			  }
			}
		}
	});
	return false;	
}

function getNombre(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getNombre.php';
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(data){	
          $('#form-eliminarcita #usuario').val(data);			  		  		  			  
		}
	});	
	
}

function getExpediente(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getExpediente.php';
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(data){	
          $('#form-eliminarcita #expediente').val(data);			  		  		  			  
		}
	});	
	
}

function getPacientes_id(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getPacientes_id.php';
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(data){	
          $('#form-eliminarcita #pacientes_id').val(data);			  		  		  			  
		}
	});	
	
}

function modal_eliminar(id){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
	   $('#eliminar_cita').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
	   });
	   $('#form-eliminarcita')[0].reset();
	   $('#form-eliminarcita #agenda_id_cita').val(id);
	   $('#form-eliminarcita #pro').val("Eliminar");
	   $('#form-eliminarcita #expediente').val(expediente);
	   getNombre(id);
	   getExpediente(id);
	   $('#form-eliminarcita').attr({ 'data-form': 'delete' }); 
	   $('#form-eliminarcita').attr({ 'action': '<?php echo SERVERURL; ?>php/agenda_pacientes/eliminar.php' });		   
   }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});				 
	}	
}

function getFechaAusencias(fecha, colaborador_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaAusencias.php';
	var valor = "";
	$.ajax({
	    type:'POST',
		url:url,
		data:'fecha='+fecha+'&colaborador_id='+colaborador_id,
		async: false,
		success:function(data){	
          valor = data;			  		  		  			  
		}
	});
	return valor;
}

$(document).ready(function(e) {
  $('#formulario #hora_nueva').on('change', function(){	
    var url = '<?php echo SERVERURL; ?>php/citas/getHora.php';
	var fecha = $('#formulario #fecha_n').val();
	var hora = $('#formulario #hora_nueva').val();
	var agenda_id = $('#formulario #agenda_id').val();
	var colaborador_id = $('#form_agenda_main #medico_general').val();
	
	$("#formulario #edi").attr('disabled', false);	
	
	var hoy = new Date();
    fecha_actual = convertDate(hoy);
	
	if(fecha<fecha_actual){
		swal({
			title: "Error", 
			text: "No se puede reprogramar en esta fecha",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});
		$("#formulario #edi").attr('disabled', true);
	}else{	
	  $.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'fecha='+fecha+'&agenda_id='+agenda_id+'&colaborador_id='+colaborador_id+'&hora='+hora,
		success:function(data){	
			 if (data == 'NulaN'){
				swal({
					title: "Error", 
					text: "No se puede agendar este usuario en esta hora ya que es un usuario nuevo",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});		   
			   $('#formulario #edi').attr('disabled', true);
			   return false;
			 }else if (data == 'NulaS'){
				swal({
					title: "Error", 
					text: "No se puede agendar este usuario en esta hora ya que es un usuario subsiguiente",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});		   
			
				$('#formulario #edi').attr('disabled', true);
				return false;
			 }else if (data == 'Nula'){
				swal({
					title: "Error", 
					text: "No se puede agendar este usuario en esta hora",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});		   
			
				$('#formulario #edi').attr('disabled', true);				
				return false;
			 }else if (data == 'NulaP'){
				swal({
					title: "Error", 
					text: "No se puede agendar este usuario en esta hora",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});		   
				$('#formulario #edi').attr('disabled', true);
				return false;
			 }else if (data == 2){
				swal({
					title: "Error", 
					text: "El médico ya tiene la hora ocupada",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});
				$('#formulario #edi').attr('disabled', true);
				return false;
			 }else if (data == 3){
				swal({
					title: "Error", 
					text: "Usuario ya tiene cita agendada ese día",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});				
				$('#formulario #edi').attr('disabled', true);
				return false;
			 }else{
			     $('#hora_citaeditend').val(data);
				 $('#formulario #edi').attr('disabled', false);
		         return false;				  		  		  		  			  
		     }
		}
	  });
	  return false;
	 }
    });
});

$(document).ready(function(e) {
  $('#formulario #fecha_n').on('blur', function(){
	if(getFechaAusencias($('#fecha_n').val(), $('#formulario #id-registro').val()) == 2){
		$("#formulario #hora_nueva").attr('disabled', false);
        $("#formulario #status_repro").attr('disabled', false);
		
		 $("#formulario #edi").attr('disabled', false);	
		 
		 var url = '<?php echo SERVERURL; ?>php/citas/getHora.php';
		 var fecha = $('#fecha_n').val();
		 var hora = $('#hora_nueva').val();
		 var agenda_id = $('#formulario #agenda_id').val();
		 var colaborador_id = $('#form_agenda_main #medico_general').val();

		var hoy = new Date();
		fecha_actual = convertDate(hoy);

		if(fecha<fecha_actual){
			swal({
				title: "Error", 
				text: "No se puede reprogramar en esta fecha",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});
			$("#formulario #edi").attr('disabled', true);
		}else{
		 $.ajax({
		   type:'POST',
		   url:url,
		   async: true,
		   data:'fecha='+fecha+'&agenda_id='+agenda_id+'&colaborador_id='+colaborador_id+'&hora='+hora,
		   success:function(data){	
			   if (data == 'NulaN'){
					swal({
						title: "Error", 
						text: "No se puede agendar este usuario en esta hora ya que es un usuario nuevo",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});
					$("#formulario #edi").attr('disabled', true);
					return false;
			   }else if (data == 'NulaS'){
					swal({
						title: "Error", 
						text: "No se puede agendar este usuario en esta hora ya que es un usuario subsiguiente",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});
					$("#formulario #edi").attr('disabled', true);
					return false;
			   }else if (data == 'Nula'){
					swal({
						title: "Error", 
						text: "No se puede agendar este usuario en esta hora",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});
					$("#formulario #edi").attr('disabled', true);
				  return false;
			   }else if (data == 'NulaP'){
					swal({
						title: "Error", 
						text: "No se puede reprogramar en esta fecha",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
					$("#formulario #edi").attr('disabled', true);
				  return false;
			   }else if (data == 2){
					swal({
						title: "Error", 
						text: "El médico ya tiene la hora ocupada",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});
					$("#formulario #edi").attr('disabled', true);
					return false;
			   }else if (data == 3){
					swal({
						title: "Error", 
						text: "Usuario ya tiene cita agendada ese día",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
					$("#formulario #edi").attr('disabled', true);
					return false;
			   }else{						
				  $('#hora_citaeditend').val(data);
				  $("#formulario #edi").attr('disabled', false);
				  return false;				  		  		  		  			  
				}
		  }
		});
		return false;
	  }	
	}else{
		swal({
			title: "Error", 
			text: "El médico se encuentra ausente, no se le puede agendar una cita. " + getComentarioAusencia($('#fecha_n').val(), $('#formulario #id-registro').val()) + "",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});					
		$("#edi").attr('disabled', true);
        $("#formulario #hora_nueva").attr('disabled', true);
        $("#formulario #status_repro").attr('disabled', true);		
	}
  });
});

function editarRegistro(agenda_id, colaborador_id, pacientes_id, servicio_id){ 
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
   var atencion;
   
   if($('#form_agenda_main #atencion').val() == ""){
	  atencion = 0;
   }else{
	  atencion = $('#form_agenda_main #atencion').val();
   }
     
   if(atencion==2 || atencion==0){
	   $('#formulario')[0].reset();
	   var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/editar.php';
	   var fecha = $('#fecha').val();
	
		$.ajax({
		   type:'POST',
		   url:url,
		   data:'agenda_id='+agenda_id+'&fecha='+fecha+'&colaborador_id='+colaborador_id,
		   success: function(valores){
				var datos = eval(valores);
				getHoraNueva();
				getStatusRepro();
				$('#formulario #reg').hide();
				$('#formulario #edi').show();
				$('#formulario #pro').val('Edicion');
				$('#formulario #pacientes_id_registro').val(pacientes_id);
				$('#formulario #servicio_registro').val(servicio_id);
				$('#formulario #id-registro').val(datos[0]);
				$('#formulario #expediente').val(datos[1]);			
				$('#formulario #nombre').val(datos[2]);
				$('#formulario #fecha_a').val(datos[3]);
				$('#formulario #agenda_id').val(datos[4]);
				$('#formulario #observacion').val(datos[7]);
                $('#formulario #comentario').val(datos[5]);				
				$('#formulario #fecha_n').val(datos[6]);
				$('#formulario #status_repro').val(datos[9]);
				$('#formulario #cant_reprogramaciones').html('Reprogramaciones: ' + datos[8]);
				$("#formulario #edi1").attr('disabled', true);
				if(atencion==2){
					$("#formulario #edi").attr('disabled', false);
				}else{
					$("#formulario #edi").attr('disabled', true);
				}					
				
			   $('#formulario').attr({ 'data-form': 'save' }); 
			   $('#formulario').attr({ 'action': '<?php echo SERVERURL; ?>php/agenda_pacientes/agregar.php' });					
				$('#registrar').modal({
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
			title: "Error", 
			text: "No se puede reprogramar esta cita",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});			 
  }		
  }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});						 
  }	
}

function reporteEXCEL(){
	var fecha = $('#form_agenda_main #fecha').val();
	var fechaf = $('#form_agenda_main #fechaf').val();
	var servicio = $('#form_agenda_main #servicio').val();
	var medico_general = $('#form_agenda_main #medico_general').val();
	var dato = $('#bs-regis').val();
	var atencion = "";
	
	if ($('#atencion').val() == ""){
	   atencion = 0;	
	}else{
	   atencion = $('#atencion').val();
	}	
	
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/buscar_agenda_usuarios_excel.php?dato='+dato+'&fecha='+fecha+'&fechaf='+fechaf+'&servicio='+servicio+'&medico_general='+medico_general+'&atencion='+atencion;
    window.open(url);		 
}

function reporteEXCELReporte(){	
	var fecha = $('#form_agenda_main #fecha').val();
	var fechaf = $('#form_agenda_main #fechaf').val();
	var servicio = $('#form_agenda_main #servicio').val();
	var medico_general = $('#form_agenda_main #medico_general').val();
	var dato = $('#bs-regis').val();
	var atencion = "";
	
	if ($('#atencion').val() == ""){
	   atencion = 0;	
	}else{
	   atencion = $('#atencion').val();
	}	
	
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/buscar_agenda_usuarios_excel_reporte.php?dato='+dato+'&fecha='+fecha+'&fechaf='+fechaf+'&medico_general='+medico_general+'&servicio='+servicio+'&atencion='+atencion;
    window.open(url);		 
}

function reporteExcelAgenda(){
	var fecha = $('#fecha').val();
	var servicio_id = $('#form_agenda_main #servicio').val()
	var servicio = $('#form_agenda_main #servicio').val();
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/reporteAgendaUsuarios.php?fecha='+fecha+'&servicio_id='+servicio_id;
	
    window.open(url);		 
}

function reporteSMS(){
	var fecha = $('#form_agenda_main #fecha').val();
	var servicio = $('#form_agenda_main #servicio').val();
	
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/sms.php?fecha='+fecha+'&servicio='+servicio;
	
    window.open(url);		 
}

function reporteSMSDiasAntes(){
	var fecha = $('#form_agenda_main #fecha').val();
	var servicio = $('#form_agenda_main #servicio').val();
	
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/sms_diasantes.php?fecha='+fecha+'&servicio='+servicio;
	
    window.open(url);		 
}

function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/paginar.php';
	var fecha = $('#form_agenda_main #fecha').val();
	var fechaf = $('#form_agenda_main #fechaf').val();
	var servicio;
	var unidad;
	var medico_general;
	var dato = '';
	var atencion;
	
	
	if ($('#form_agenda_main #atencion').val() == "" || $('#form_agenda_main #atencion').val() == null){
	  atencion = 0;	
	}else{
	  atencion = $('#form_agenda_main #atencion').val();
	}
	
	if ($('#form_agenda_main #servicio').val() == "" || $('#form_agenda_main #servicio').val() == null){
	  servicio = 1;	
	}else{
	  servicio = $('#form_agenda_main #servicio').val();
	}	
	
	if ($('#form_agenda_main #medico_general').val() == "" || $('#form_agenda_main #medico_general').val() == null){
	  medico_general = "";	
	}else{
	  medico_general = $('#form_agenda_main #medico_general').val();
	}		
	
	if($('#form_agenda_main #bs-regis').val() == "" || $('#form_agenda_main #bs-regis').val() == null){
		dato = '';
	}else{
	    dato = $('#form_agenda_main #bs-regis').val();	
	}
	
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		data:'partida='+partida+'&fecha='+fecha+'&fechaf='+fechaf+'&dato='+dato+'&servicio='+servicio+'&medico_general='+medico_general+'&atencion='+atencion,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}

//CONSULTA EN LA CARPETA CITAS
function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#form_agenda_main #medico_general').html("");
			$('#form_agenda_main #medico_general').html(data);		
		}			
     });	
}

function getServicio_id(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getServicio_id.php';
	var servicio_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(valores){	
          servicio_id = valores;	  
		}
	});
	return servicio_id;		
}

function getNewAgendaID(pacientes_id,colaborador_id,servicio_id,fecha){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getNewAgendaId.php';
	var new_agenda_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'pacientes_id='+pacientes_id+'&colaborador_id='+colaborador_id+'&servicio_id='+servicio_id+'&fecha='+fecha,
		success:function(valores){	
          new_agenda_id = valores;	  
		}
	});
	return new_agenda_id;		
}

$(document).ready(function() {
	$("#formulario #edi1").attr('disabled', true);		
	  $('#formulario #checkeliminar').on('click', function(){
		  if($('#formulario #checkeliminar:checked').val() == 1){
			  $("#formulario #edi1").attr('disabled', false);  
		  }else{
			  $("#formulario #edi1").attr('disabled', true); 
		  }			 
      });	  
});

function nosePresentoRegistro(agenda_id, pacientes_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){		
		if($('#form_agenda_main #atencion').val() == 0){ 			  
			  var nombre_usuario = consultarNombre(pacientes_id);
			  var expediente_usuario = consultarExpediente(pacientes_id);
			  var dato;

			  if(expediente_usuario == 0){
				  dato = nombre_usuario;
			  }else{
				  dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
			  }

			swal({
			  title: "¿Esta seguro?",
			  text: "¿Desea remover este usuario: " + dato + " que no se presento a su cita?",
			  type: "input",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  inputPlaceholder: "Comentario",
			  cancelButtonText: "Cancelar",	
			  confirmButtonText: "¡Sí, remover el usuario!",
			  confirmButtonClass: "btn-warning",	  
			}, function (inputValue) {
			  if (inputValue === false) return false;
			  if (inputValue === "") {
				swal.showInputError("¡Necesita escribir algo!");
				return false
			  }
				eliminarRegistro(agenda_id, inputValue);
			});		
	   }else{	
			swal({
				title: "Error", 
				text: "Error al ejecutar esta acción, el usuario debe estar en estatus pendiente",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});			  
	   }
	 }else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});					 
	  }	
}

function eliminarRegistro(agenda_id, comentario, fecha){
	var hoy = new Date();
	fecha_actual = convertDate(hoy);

	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/usuario_no_presento.php';	
	
    $.ajax({
	  type:'POST',
	  url:url,
	  data:'agenda_id='+agenda_id+'&fecha='+fecha+'&comentario='+comentario,
	  success: function(registro){
		  if(registro == 1){
			swal({
				title: "Success", 
				text: "Ausencia almacenada correctamente",
				type: "success",
				timer: 3000, //timeOut for auto-close
			});
			pagination(1);
			return false; 
		  }else if(registro == 2){	
				swal({
					title: "Error", 
					text: "Error al remover este registro",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});
				return false; 
		  }else if(registro == 3){	
				swal({
					title: "Error", 
					text: "Este registro ya tiene almacenada una ausencia",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});
				return false; 
		  }else if(registro == 4){	
				swal({
					title: "Error", 
					text: "Este usuario ya ha sido precliniado, no puede marcarle una ausencia",
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
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function getMes(fecha){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getMes.php';
	var resp;
	
	$.ajax({
	    type:'POST',
		data:'fecha='+fecha,
		url:url,
		async: false,
		success:function(data){	
          resp = data;		  
		}
	});
	return resp	;	
}

function limpiar(){
	$('#form_agenda_main #servicio').html("");		
	$('#form_agenda_main #medico_general').html("");		
	$('#form_agenda_main #atencion').html("");	
    $('#form_agenda_main #agrega-registros').html("");
	$('#form_agenda_main #pagination').html("");		
	getServicio();
	getProfesionales();
    getAtencion();
    pagination(1);
}

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/servicios.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_agenda_main #servicio').html("");
			$('#form_agenda_main #servicio').html(data);

		    $('#formulario_triage_reporte #servicio_triage').html("");
			$('#formulario_triage_reporte #servicio_triage').html(data);			
		}			
     });	
}

function getAtencion(){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getReporte.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_agenda_main #atencion').html("");
			$('#form_agenda_main #atencion').html(data);
		}			
     });	
}

function getStatusRepro(){
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getStatusID.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario #status_repro').html("");
			$('#formulario #status_repro').html(data);
		}			
     });	
}

function getHoraNueva(){
    var url = '<?php echo SERVERURL; ?>php/citas/getHoraConsulta.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#formulario #hora_nueva').html("");
			$('#formulario #hora_nueva').html(data);
		}			
     });	
}

$(document).ready(function() {
	setInterval('pagination(1)',220000);	
});

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

function consultarExpediente(pacientes_id){	
    var url = '<?php echo SERVERURL; ?>php/pacientes/getExpedienteInformacion.php';
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

function consultarFecha(fecha){	
    var url = '<?php echo SERVERURL; ?>php/citas/consultarFecha.php';
	var fecha;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'fecha='+fecha,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;		
}

function consultaTipoUsuario(pacientes_id, colaborador_id, servicio_id, expediente){	
    var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/getTipoUsuario.php';
	var tipo;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id+'&colaborador_id='+colaborador_id+'&servicio_id='+servicio_id+'&expediente='+expediente,
		async: false,
		success:function(data){	
          tipo = data;			  		  		  			  
		}
	});
	return tipo;		
}

$(document).ready(function() {
	$('#formulario_triage #observacion_triage').on('change', function(){
	    var valor = $('#formulario_triage #observacion_triage').val();
		
		if(valor == 1 || valor == 2){
			$('#formulario_triage #si_asistira_triage').prop('checked', false); 
			$('#formulario_triage #no_asistira_triage').prop('checked', true);
		}else{
			$('#formulario_triage #si_asistira_triage').prop('checked', true); 
			$('#formulario_triage #no_asistira_triage').prop('checked', false);			
		}
    });					
});	

$('#form_agenda_main #limpiar').on('click', function(e){
    e.preventDefault();
    limpiar();
});
</script>