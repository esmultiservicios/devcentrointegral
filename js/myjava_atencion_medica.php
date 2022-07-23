<script>
$(document).ready(function() {
	setInterval('pagination(1)',22000); 	
	getColaborador();
	getAseguradora();
	getFactEmpresas();
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#registro_transito_eviada").on('shown.bs.modal', function(){
        $(this).find('#formulario_transito_enviada #expediente').focus();
    });
});

$(document).ready(function(){
    $("#registro_transito_recibida").on('shown.bs.modal', function(){
        $(this).find('#formulario_transito_recibida #expediente').focus();
    });
});

$(document).ready(function(){
    $("#modal_registro_atenciones").on('shown.bs.modal', function(){
        $(this).find('#formulario_atenciones #expediente').focus();
    });
});

$(document).ready(function(){
    $("#buscar_atencion").on('shown.bs.modal', function(){
        $(this).find('#formulario_buscarAtencion #busqueda').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	$('.footer').show();
	$('.footer1').hide();

	//LLAMADA A LAS FUNCIONES
	funcionesFormPacientes();
	
	//INICIO ABRIR VENTANA MODAL PARA EL REGISTRO DE ATENCIONES DE PACIENTES
	$('#form_main #nuevo_registro').on('click',function(e){
		e.preventDefault();
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){
			 $('#formulario_atenciones')[0].reset();	
			 limpiarFormPacientes();	
			 $('#reg_atencion').show();
			 $('#edi_atencion').hide();
			 $('#formulario_atenciones #consultorio_').hide();
             $("#formulario_atenciones #label_servicio").hide();
             $("#formulario_atenciones #servicio").hide();
			 $("#formulario_atenciones #fecha").attr('readonly', false);
			 $("#formulario_atenciones #paciente_consulta").attr('disabled', false);	
			 $("#reg_atencion").attr('disabled', false);		
			 $('#formulario_atenciones #consultorio_').show();
			 $('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');
			 $('#formulario_atenciones #buscar_pacientes_atenciones').show();	
			 
			 //HABILITAR OBJETOS
			 $('#formulario_atenciones #paciente_consulta').attr('disabled', false);	
			 $('#formulario_atenciones #buscar_pacientes_atenciones').attr('disabled', false)
			 $('#formulario_atenciones').attr({ 'data-form': 'save' }); 
			 $('#formulario_atenciones').attr({ 'action': '<?php echo SERVERURL; ?>php/atencion_pacientes/agregar.php' });	

			 $('#modal_registro_atenciones').modal({
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
	//FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE ATENCIONES DE PACIENTES
	
	//INICIO ABRIR VENTANA MODAL TRANSITO ENVIADA
	$('#form_main #transito_enviada').on('click',function(){
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){	
		     $('#formulario_transito_enviada #pro').val("Registro");
			 $('#registro_transito_eviada').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});
			limpiarTE();
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
	//FIN ABRIR VENTANA MODAL TRANSITO ENVIADA
	
	//INICIO ABRIR VENTANA MODAL TRANSITO RECIBIDA
	$('#form_main #transito_recibida').on('click',function(){
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){
		     $('#formulario_transito_recibida #pro').val("Registro");			
			 $('#registro_transito_recibida').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});
			limpiarTR();
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
	//FIN ABRIR VENTANA MODAL TRANSITO RECIBIDA
	
	//INICIO CONSULTRAR USUARIOS ATENDIDOS
	$('#form_main #historial').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){
			e.preventDefault();
             paginationBusqueda(1);
			 $('#formulario_buscarAtencion #pro').val("Búsqueda de Atenciones");
			 $('#formulario_buscarAtencion #paciente_consulta').html("");
			 $('#formulario_buscarAtencion #agrega_registros_busqueda_').html('<td colspan="3" style="color:#C7030D">No se encontraron resultados, seleccione un paciente para visualizar sus datos</td>');
			 $('#buscar_atencion').modal({
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
		}	 
	});	
	//FIN CONSULTRAR USUARIOS ATENDIDOS

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

	$('#form_main #estado').on('change',function(){
	  pagination(1);
	});
	
	$('#formulario_buscarAtencion #busqueda').on('keyup',function(){
	  paginationBusqueda(1);
      $('#formulario_buscarAtencion #paciente_consulta').html('');
	  $('#formulario_buscarAtencion #agrega_registros_busqueda_').html('<td colspan="12" style="color:#C7030D">No se encontraron resultados</td>');
	  $('#formulario_buscarAtencion #pagination_busqueda_').html('');	  
	});	
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	  
});
//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#registro_transito_eviada #enviada').html("");
			$('#registro_transito_eviada #enviada').html(data);

		    $('#formulario_transito_recibida #recibida').html("");
			$('#formulario_transito_recibida #recibida').html(data);		
        }
     });		
}

function editarRegistro(pacientes_id, agenda_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){	
	   if( $('#form_main #estado').val() == 0 ){
			$('#formulario_atenciones')[0].reset();		
			var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/editar.php';

				$.ajax({
				type:'POST',
				url:url,
				data:'pacientes_id='+pacientes_id+'&agenda_id='+agenda_id,
				success: function(valores){
					var array = eval(valores);
					$('#reg_atencion').hide();
					$('#edi_atencion').show();
					$('#formulario_atenciones #pro').val('Registro');
					$('#formulario_atenciones #pacientes_id').val(pacientes_id);
					$('#formulario_atenciones #agenda_id').val(agenda_id);
					$('#formulario_atenciones #identidad').val(array[0]);
					$('#formulario_atenciones #nombre').val(array[1]);					 
					$('#formulario_atenciones #edad').val(array[2]);		
					$('#formulario_atenciones #procedencia').val(array[3]);
					$('#formulario_atenciones #paciente_consulta').val(array[6]);
					$('#formulario_atenciones #fecha').val(array[7]);
					$('#formulario_atenciones #fecha_nac').val(array[8]);
					$('#formulario_atenciones #antecedentes').val(array[9]);
					$('#formulario_atenciones #historia_clinica').val(array[10]);
					$('#formulario_atenciones #exame_fisico').val(array[11]);
					$('#formulario_atenciones #diagnostico').val(array[12]);					
					$('#formulario_atenciones #seguimiento_read').val(array[13]);
					$('#formulario_atenciones #servicio_id').val(array[14]);
					$('#formulario_atenciones #edad').val(array[15]);
					$('#formulario_atenciones #preclinica').val(array[16]);					
					$("#formulario_atenciones #fecha").attr('readonly', true);
					$("#edi_atencion").attr('disabled', false);	
					$("#formulario_atenciones #label_servicio").show();
					$('#formulario_atenciones #consultorio_').hide();
					$('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');
					$('#formulario_atenciones #buscar_pacientes_atenciones').hide();	
					
					//DESHABILITAR OBJETOS
					$('#formulario_atenciones #paciente_consulta').attr('disabled', true);					
					
					$('#formulario_atenciones').attr({ 'data-form': 'save' }); 
					$('#formulario_atenciones').attr({ 'action': '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarRegistro.php' });						
					
					caracteresSeguimiento();
					caracteresDiagnostico();
					caracteresExamenFisico();
					caracteresHistoriaClinica();
					caracteresAntecedentes();

					$('#modal_registro_atenciones').modal({
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
				text: "Lo sentimos, este registro ya existe, no se puede agregar nuevamente su atención",
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

//INICIO FUNCION AUSENCIA DE USUARIOS
function nosePresentoRegistro(pacientes_id, agenda_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){		
		if($('#form_main #estado').val() == 0){ 			  
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

	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/usuario_no_presento.php';
	
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
//FIN FUNCION AUSENCIA DE USUARIOS

//ATENCION A USUARIOS
$('#formulario_atenciones #antecedentes').keyup(function() {
	    var max_chars = 3200;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_atenciones #charNum_antecedentes').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

function caracteresAntecedentes(){
	var max_chars = 3200;
	var chars = $('#formulario_atenciones #antecedentes').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_atenciones #charNum_antecedentes').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
}

$('#formulario_atenciones #historia_clinica').keyup(function() {
	    var max_chars = 3200;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_atenciones #charNum_historia').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

function caracteresHistoriaClinica(){
	var max_chars = 3200;
	var chars = $('#formulario_atenciones #historia_clinica').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_atenciones #charNum_historia').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
}

$('#formulario_atenciones #exame_fisico').keyup(function() {
	    var max_chars = 3200;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_atenciones #charNum_examen').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

function caracteresExamenFisico(){
	var max_chars = 3200;
	var chars = $('#formulario_atenciones #exame_fisico').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_atenciones #charNum_examen').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
}

$('#formulario_atenciones #diagnostico').keyup(function() {
	    var max_chars = 3200;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_atenciones #charNum_diagnostico').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

function caracteresDiagnostico(){
	var max_chars = 3200;
	var chars = $('#formulario_atenciones #diagnostico').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_atenciones #charNum_diagnostico').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
}

$('#formulario_atenciones #seguimiento').keyup(function() {
	    var max_chars = 3200;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_atenciones #charNum_seguimiento').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

function caracteresSeguimiento(){
	var max_chars = 3200;
	var chars = $('#formulario_atenciones #seguimiento').val().length;
	var diff = max_chars - chars;
	
	$('#formulario_atenciones #charNum_seguimiento').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
}

//TANSITO ENVIADA
$('#formulario_transito_enviada #motivo').keyup(function() {
	    var max_chars = 255;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_transito_enviada #charNumMotivoTE').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

//TRANSITO RECIBIDA
$('#formulario_transito_recibida #motivo').keyup(function() {
	    var max_chars = 255;
        var chars = $(this).val().length;
        var diff = max_chars - chars;
		
		$('#formulario_transito_recibida #charNumMotivoTR').html(diff + ' Caracteres'); 
		
		if(diff == 0){
			return false;
		}
});

//INICIO BUSQUEDA DE VALORES PARA EL PACIENTE, SEGUN EL PACIENTE SELECCIONADO
$(document).ready(function(e){
    $('#formulario_atenciones #paciente_consulta').on('change', function(){
	 if($('#formulario_atenciones #paciente_consulta').val() != "" || $('#formulario_atenciones #servicio').val() != ""){
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
        var pacientes_id = $('#formulario_atenciones #paciente_consulta').val();
	    $.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
				var array = eval(data);		  
				$('#formulario_atenciones #identidad').val(array[0]);
				$('#formulario_atenciones #nombre').val(array[1]);					 
				$('#formulario_atenciones #edad').val(array[2]);
				$('#formulario_atenciones #procedencia').val(array[3]);
				$('#formulario_atenciones #paciente_consulta').val(array[6]);
				$('#formulario_atenciones #antecedentes').val(array[7]);
				$('#formulario_atenciones #historia_clinica').val(array[8]);
				$('#formulario_atenciones #exame_fisico').val(array[9]);	
				$('#formulario_atenciones #seguimiento_read').val(array[10]);
				$('#formulario_atenciones #diagnostico').val(array[11]);
				$('#formulario_atenciones #fecha_nac').val(array[12]);				
				$("#reg_atencion").attr('disabled', false);
				return false;			 				
			}		  			  
	    });
	    return false;		
	 }else{ 
		$('#formulario_atenciones')[0].reset();	
        $("#reg_atencion").attr('disabled', true);		
	 }
	});
});
//FIN BUSQUEDA DE VALORES PARA EL PACIENTE, SEGUN EL PACIENTE SELECCIONADO

//INICIO TRANSITO USUARIO
$(document).ready(function(e) {
    $('#formulario_transito_enviada #paciente_te').on('change', function(){
	 if($('#formulario_transito_enviada #paciente_te').val()!=""){
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
        var pacientes_id = $('#formulario_transito_enviada #paciente_te').val();
	    $.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
			  var array = eval(data);
			  $('#formulario_transito_enviada #identidad').val(array[0]);	  			  
		  }
	  });
	  return false;		
	 }else{
		$('#formulario_transito_enviada')[0].reset();
        $('#formulario_transito_enviada #pro').val("Registro");		
        $("#reg_transitoe").attr('disabled', true);			
	 }
	});
});

$(document).ready(function(e) {
    $('#formulario_transito_recibida #paciente_tr').on('change', function(){
	 if($('#formulario_transito_recibida #paciente_tr').val()!=""){
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
        var pacientes_id = $('#formulario_transito_recibida #paciente_tr').val();
	    $.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
			  var array = eval(data);
			  $('#formulario_transito_recibida #identidad').val(array[0]);	  			  
		  }
	  });
	  return false;		
	 }else{
		$('#formulario_transito_recibida')[0].reset();	
		$('#formulario_transito_recibida #pro').val("Registro");
        $("#reg_transitor").attr('disabled', true);		
	 }
	});
});


$('#reg_transitoe').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||  getUsuarioSistema() == 5){
		 if ($('#formulario_transito_enviada #expediente').val() == "" && $('#formulario_transito_enviada #motivo').val() == "" && $('#formulario_agregar_referencias_recibidas #enviadaa').val() == ""){
			 $('#formulario_transito_enviada')[0].reset();						   
			swal({
				title: 'Error', 
				text: 'No se pueden enviar los datos, los campos estan vacíos',
				type: 'error', 
				confirmButtonClass: 'btn-danger'
			});			
			return false;
		 }else{
			e.preventDefault();
			agregarTransitoEnviadas();		
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

$('#reg_transitor').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5){
		 if ($('#formulario_transito_recibida #expediente').val() == "" && $('#formulario_transito_recibida #motivo').val() == "" && $('#formulario_agregar_referencias_recibidas #enviadaa').val() == ""){
			$('#formulario_transito_recibida')[0].reset();							   
			swal({
				title: 'Error', 
				text: 'No se pueden enviar los datos, los campos estan vacíos',
				type: 'error', 
				confirmButtonClass: 'btn-danger'
			});				
			return false;
		 }else{
			e.preventDefault();
			agregarTransitoRecibidas();		
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
//FIN TRANSITO USUARIOS

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar.php';
    var fechai = $('#form_main #fecha_b').val();
	var fechaf = $('#form_main #fecha_f').val();
	var dato = '';
	var estado = '';
	
    if($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null){
		estado = 0;
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
		data:'partida='+partida+'&fechai='+fechai+'&fechaf='+fechaf+'&dato='+dato+'&estado='+estado,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE REGISTROS

//INICIO PAGINACION DE HISTORIAL DE ATENCIONES
function paginationBusqueda(partida){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar_buscar.php';

	if($('#formulario_buscarAtencion #busqueda').val() == "" || $('#formulario_buscarAtencion #busqueda').val() == null){
		dato = '';
	}else{
		dato = $('#formulario_buscarAtencion #busqueda').val();
	}
	
	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#formulario_buscarAtencion #agrega_registros_busqueda').html(array[0]);
			$('#formulario_buscarAtencion #pagination_busqueda').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE HISTORIAL DE ATENCIONES

//CONSULTAMOS TODAS LAS HISTORIAS CLINICAS DE ESTE USUARIO
function detallesAtencion(pacientes_id){
	$('#formulario_buscarAtencion #pacientes_id').val(pacientes_id);
	paginarSeguimiento(1);
}

function paginarSeguimiento(partida){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar_historias_clinicas.php';

	var pacientes_id = $('#formulario_buscarAtencion #pacientes_id').val();
	
	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:'partida='+partida+'&pacientes_id='+pacientes_id,
		success:function(data){
			var array = eval(data);
			$('#formulario_buscarAtencion #paciente_consulta').html('<b>Paciente:</b> ' + getNombrePaciente(pacientes_id));
			$('#formulario_buscarAtencion #agrega_registros_busqueda_').html(array[0]);
			$('#formulario_buscarAtencion #pagination_busqueda_').html(array[1]);
		}
	});
	return false;	
}


//INICIO FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES
function limpiarFormPacientes(){
   $('#formulario_atenciones #historia_clinica').val('');
   $('#formulario_atenciones #historia_clinica_read').val('');   
   $('#formulario_atenciones #seguimiento').val('');
   $('#formulario_atenciones #seguimiento_read').val('');   
   funcionesFormPacientes();
   $('#formulario_atenciones #pro').val('Registro');  
}
//FIN FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES

//INICIO TRANSITO DE PACIENTES
function agregarTransitoEnviadas(){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarTransitoEnviadas.php';
	
   	var fecha = $('#formulario_transito_enviada #fecha').val();	
    var hoy = new Date();
    fecha_actual = convertDate(hoy);
	
   if(getMes(fecha)==2){
	swal({
		title: 'Error', 
		text: 'No se puede agregar/modificar registros fuera de este periodo',
		type: 'error', 
		confirmButtonClass: 'btn-danger'
	});		
	return false;	
   }else{	
    if ( fecha <= fecha_actual){
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario_transito_enviada').serialize(),
		success: function(registro){
			if (registro == 1){
			    $('#formulario_transito_enviada')[0].reset();
			    $('#formulario_transito_enviada #pro').val('Registro');		   
				swal({
					title: 'Almacenado', 
					text: 'Registro almacenado correctamente',
					type: 'success', 
					timer: 3000,
				});	
				limpiarTE();
				$('#registro_transito_eviada').modal('hide');
			    return false;
			}else if(registro == 2){							   				   			   
				swal({
					title: 'Error', 
					text: 'Error al intentar almacenar este registro',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});				   		   
			   return false;
			}else if(registro == 3){							   				   			   
				swal({
					title: "Error", 
					text: "Este registro no cuenta con atencion almacenada",
					type: "error", 
					confirmButtonClass: "btn-danger"
				});				   		   
			   return false;
			}else if(registro == 4){							   				   			   
				swal({
					title: "Error", 
					text: "Este registro ya existe",
					type: "error", 
					confirmButtonClass: "btn-danger"
				});				   		   
			   return false;
			}else{							   					   			   
				swal({
					title: "Error", 
					text: "Error al completar el registro",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});			   
			    return false;
			}
		}
	});	
   }else{
		swal({
			title: 'Error', 
			text: 'No se puede agregar/modificar registros fuera de esta fecha',
			type: 'error', 
			confirmButtonClass: 'btn-danger'
		});				
		return false;	   
   }
  }
}

function agregarTransitoRecibidas(){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarTransitoRecibidas.php';
	
   	var fecha = $('#formulario_transito_recibida #fecha').val();	
    var hoy = new Date();
    fecha_actual = convertDate(hoy);
	
   if(getMes(fecha)==2){
		swal({
			title: 'Error', 
			text: 'No se puede agregar/modificar registros fuera de este periodo',
			type: 'error', 
			confirmButtonClass: 'btn-danger'
		});				
		return false;	
   }else{	
    if ( fecha <= fecha_actual){    
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario_transito_recibida').serialize(),
		success: function(registro){
			if (registro == 1){
			    $('#formulario_transito_recibida')[0].reset();
			    $('#pro').val('Registro');
				swal({
					title: 'Almacenado', 
					text: 'Registro almacenado correctamente',
					type: 'success',
					timer: 3000,					
				});
				$('#registro_transito_recibida').modal('hide');
				limpiarTR();
			    return false;
			}else if(registro == 2){							   					   			   
				swal({
					title: 'Error', 
					text: 'Error al intentar almacenar este registro',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});				   				   
			    return false;
			}else if(registro == 3){							   					   			   
				swal({
					title: 'Error', 
					text: 'Este registro no cuenta con atencion almacenada',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});				   				   
			    return false;
			}else if(registro == 4){							   					   			   
				swal({
					title: 'Error', 
					text: 'Este registro ya existe',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});				   				   
			    return false;
			}else{				   			   
				swal({
					title: 'Error', 
					text: 'Error al completar el registro',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});			    
			    return false;
			}
		}
	});	
   }else{
		swal({
			title: 'Error', 
			text: 'No se puede agregar/modificar registros fuera de esta fecha',
			type: 'error', 
			confirmButtonClass: 'btn-danger'
		});
	    return false;	 
   }
  }
}
//FIN TRANSITO DE PACIENTES

//INICIO OBTENER EL AGENDA ID, DE LA ENTIDAD AGENDA DE PACIENTES
function getAgendaID(pacientes_id, fecha, servicio){	
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getAgendaID.php';
	var agenda_id;
	
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'pacientes_id='+pacientes_id+'&fecha='+fecha+'&servicio='+servicio,
		success:function(data){		
          agenda_id = data;			  		  		  			  
		}
	});
	return agenda_id;	
}
//FIN OBTENER EL AGENDA ID, DE LA ENTIDAD AGENDA DE PACIENTES

//INICIO AGRUPAR FUNCIONES DE PACIENTES
function funcionesFormPacientes(){
	getServicioTransito();
	getServicioAtencion();
	getEstado();
	getPacientes();
	getConsultorio();
	pagination(1);
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

//INICIO OBTENER EL NOMBRE DEL PACIENTE
function getNombrePaciente(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getNombrePaciente.php';
	var paciente;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          paciente = data;			  		  		  			  
		}
	});
	return paciente;	
}
//FIN OBTENER EL NOMBRE DEL PACIENTE

//INICIO OBTENER EL MONTO QUE COBRA EL PROFESIONAL EN LOS SERVICIOS QUE ATIENDE
function getMonto(colaborador_id,agenda_id, tipo_tarifa){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getMonto.php';
	var monto;
	$.ajax({
	    type:'POST',
		url:url,
		data:'colaborador_id='+colaborador_id+'&agenda_id='+agenda_id+'&tipo_tarifa='+tipo_tarifa,
		async: false,
		success:function(data){	
          monto = data;			  		  		  			  
		}
	});
	return monto;	
}
//FIN OBTENER EL MONTO QUE COBRA EL PROFESIONAL EN LOS SERVICIOS QUE ATIENDE

//INICIO OBTENER EL PORCENTAJE
function getPorcentaje(descuento_id, agenda_id){		
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getDescuentoPorcentaje.php';
	var porcentaje;
	$.ajax({
	    type:'POST',
		url:url,
		data:'descuento_id='+descuento_id+'&agenda_id='+agenda_id,
		async: false,
		success:function(data){	
          porcentaje = data;			  		  		  			  
		}
	});
	return porcentaje;
}
//FIN OBTENER EL PORCENTAJE

//INICIO FUNCION NETO A COBRAR
function getNetoCobrar(monto,porcentaje){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getNetoCobrar.php';
	var monto;
	$.ajax({
	    type:'POST',
		url:url,
		data:'monto='+monto+'&porcentaje='+porcentaje,
		async: false,
		success:function(data){	
          monto = data;			  		  		  			  
		}
	});
	return monto;	
}
//FIN FUNCION NETO A COBRAR

//INICIO PARA OBTENER EL COLABORADOR_ID
function getColaborador_id(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getColaborador.php';
	var colaborador_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          colaborador_id = data;			  		  		  			  
		}
	});
	return colaborador_id;	
}
//FIN PARA OBTENER EL COLABORADOR_ID

//INICIO PARA OBTENER EL SERVICIO DEL TRANSITO DE USUARIOS	
function getServicioTransito(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/servicios_transito.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_transito_enviada #servicio').html("");
			$('#formulario_transito_enviada #servicio').html(data);
			
		    $('#formulario_transito_recibida #servicio').html("");
			$('#formulario_transito_recibida #servicio').html(data);			
        }
     });		
}
//FIN PARA OBTENER EL SERVICIO DEL TRANSITO DE USUARIOS

//INICIO FUNCION LIMPIAR TRANSITO
function limpiarTE(){
	getPacientes();
	getColaborador();
	$('#formulario_transito_enviada #pro').val("Registro");
	$('#formulario_transito_enviada #motivo').val("");
	$("#reg_transitoe").attr('disabled', false);
}

function limpiarTR(){
	getPacientes();
	getColaborador();
	$('#formulario_transito_recibida #pro').val("Registro");	
	$('#formulario_transito_recibida #motivo').val("");
	$("#reg_transitor").attr('disabled', false);	
}
//FIN FUNCION LIMPIAR TRANSITO

//INICIO FUNCION PARA OBTENER LOS PACIENTES
function getPacientes(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getPacientes.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_atenciones #paciente_consulta').html("");
			$('#formulario_atenciones #paciente_consulta').html(data);

		    $('#formulario_transito_enviada #paciente_te').html("");
			$('#formulario_transito_enviada #paciente_te').html(data);

		    $('#formulario_transito_recibida #paciente_tr').html("");
			$('#formulario_transito_recibida #paciente_tr').html(data);				
        }
     });	
}
//FIN FUNCION PARA OBTENER LOS PACIENTES

//INICIO PARA OBTENER EL SERVICIO DEL FORMULARIO DE PACIENTES
function getServicioAtencion(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/servicios.php';
	
	var servicio_id;
	$.ajax({
	    type:'POST',
		data:'agenda_id='+agenda_id,
		url:url,
		async: false,
		success:function(data){	
          servicio_id = data;			  		  		  			  
		}
	});
	return servicio_id;		
}
//FIN PARA OBTENER EL SERVICIO DEL FORMULARIO DE PACIENTES

//INICIO PARA OBTENER EL ESTADO DE LOS PACIENTES (ATENDIDOS, AUSENTES)
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getEstado.php';		
		
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
//FIN PARA OBTENER EL ESTADO DE LOS PACIENTES (ATENDIDOS, AUSENTES)

//INICIO PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL
function evaluarRegistrosPendientes(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/evaluarPendientes.php';
	var string = '';
	
	$.ajax({
	    type:'POST',
		data:'fecha='+fecha,
		url:url,
		success: function(valores){	
		   var datos = eval(valores);
		   if(datos[0]>0){			   
			  if(datos[0] == 1 || datos[0] == 0){
				  string = 'Registro pendiente';
			  }else{
				  string = 'Registros pendientes';
			  }
			  			  
			  swal({
					title: 'Advertencia', 
					text: "Se le recuerda que tiene " + datos[0] + " " + string + " de subir en las Atenciones Medicas en este mes de " + datos[1] + ". Debe revisar sus registros pendientes.", 
					type: 'warning', 
					confirmButtonClass: 'btn-warning'
			  });			  
		   }
           		  		  		  			  
		}
	});	
}
//FIN PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL

//INICIO PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL Y ENVIARLOS POR CORREO ELECTRONICO COMO RECORDATORIO
function evaluarRegistrosPendientesEmail(){
    var url = '<?php echo SERVERURL; ?>php/mail/evaluarPendientes_atencionesMedicas.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}
//FIN PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL Y ENVIARLOS POR CORREO ELECTRONICO COMO RECORDATORIO

function getConsultorio(){
	var url = '<?php echo SERVERURL; ?>php/citas/getServicio.php';
		
	$.ajax({
	   type:'POST',
	   url:url,
	   success:function(data){
	      $('#formulario_atenciones #servicio_id').html("");
		  $('#formulario_atenciones #servicio_id').html(data);  
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
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getMes.php';
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
/**********************************************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************************************/
$('#formulario_transito_enviada #buscar_pacientes_te').on('click', function(e){
	listar_pacientes_buscar_te();
	$('#modal_busqueda_pacientes').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});

$('#formulario_transito_enviada #buscar_colaboradores_te').on('click', function(e){
	listar_colaboradores_buscar_te();
	$('#modal_busqueda_colaboradores').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});

$('#formulario_transito_recibida #buscar_pacientes_tr').on('click', function(e){
	listar_pacientes_buscar_tr();
	$('#modal_busqueda_pacientes').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});

$('#formulario_transito_recibida #buscar_colaboradores_tr').on('click', function(e){
	listar_colaboradores_buscar_tr();
	$('#modal_busqueda_colaboradores').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});

$('#formulario_atenciones #buscar_pacientes_atenciones').on('click', function(e){
	listar_pacientes_buscar_atenciones();
	 $('#modal_busqueda_pacientes').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

$('#formulario_atenciones #buscar_religion_atenciones').on('click', function(e){
	listar_religion_buscar();
	 $('#modal_busqueda_religion').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

$('#formulario_atenciones #buscar_profesion_atenciones').on('click', function(e){
	listar_profesion_buscar();
	 $('#modal_busqueda_profesion').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

$('#formulario_atenciones #buscar_servicios_atenciones').on('click', function(e){
	listar_servicios_buscar();
	 $('#modal_busqueda_servicios').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

//INICIO FORMULARIO DE BUSQUEDA
var listar_pacientes_buscar_atenciones = function(){
	var table_pacientes_buscar_atenciones = $("#dataTablePacientes").DataTable({		
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
	table_pacientes_buscar_atenciones.search('').draw();
	$('#buscar').focus();
	
	view_pacientes_busqueda_atenciones_dataTable("#dataTablePacientes tbody", table_pacientes_buscar_atenciones);
}

var view_pacientes_busqueda_atenciones_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_atenciones #pacientes_id').val(data.pacientes_id);
		$('#formulario_atenciones #paciente_consulta').val(data.pacientes_id);
		
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
        var pacientes_id = data.pacientes_id;
	    $.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
				var array = eval(data);		  
				$('#formulario_atenciones #identidad').val(array[0]);
				$('#formulario_atenciones #nombre').val(array[1]);					 
				$('#formulario_atenciones #edad').val(array[2]);		
				$('#formulario_atenciones #procedencia').val(array[3]);
				$('#formulario_atenciones #religion_id').val(array[4]);
				$('#formulario_atenciones #profesion_id').val(array[5]);
				$('#formulario_atenciones #paciente_consulta').val(array[6]);
				$('#formulario_atenciones #antecedentes').val(array[7]);
				$('#formulario_atenciones #historia_clinica').val(array[8]);
				$('#formulario_atenciones #exame_fisico').val(array[9]);	
				$('#formulario_atenciones #seguimiento_read').val(array[10]);
				$('#formulario_atenciones #diagnostico').val(array[11]);				
				$('#formulario_atenciones #fecha_nac').val(array[12]);				
				$("#reg_atencion").attr('disabled', false);
				return false;			 				
			}		  			  
	    });
		
		$('#modal_busqueda_pacientes').modal('hide');
	});
}

var listar_servicios_buscar = function(){
	var table_servicios_buscar = $("#dataTableServicios").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/citas/getServiciosTable.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"nombre"}		
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_servicios_buscar.search('').draw();
	$('#buscar').focus();
	
	view_servicios_busqueda_dataTable("#dataTableServicios tbody", table_servicios_buscar);
}

var view_servicios_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_atenciones #servicio_id').val(data.servicio_id);
		$('#modal_busqueda_servicios').modal('hide');
	});
}

var listar_pacientes_buscar_te = function(){
	var table_pacientes_buscar_te = $("#dataTablePacientes").DataTable({		
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
	table_pacientes_buscar_te.search('').draw();
	$('#buscar').focus();
	
	view_pacientes_busqueda_te_dataTable("#dataTablePacientes tbody", table_pacientes_buscar_te);
}

var view_pacientes_busqueda_te_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_transito_enviada #pacientes_id').val(data.pacientes_id);
		$('#formulario_transito_enviada #paciente_te').val(data.pacientes_id);
		
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
		var pacientes_id = $('#formulario_transito_enviada #paciente_te').val();
		
		$.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
			  var array = eval(data);
			  $('#formulario_transito_enviada #identidad').val(array[0]);	  			  
		  }
		});
		
		$('#modal_busqueda_pacientes').modal('hide');
	});
}

var listar_colaboradores_buscar_te = function(){
	var table_colaboradores_buscar_te = $("#dataTableColaboradores").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/facturacion/getColaboradoresTabla.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"colaborador"},
			{"data":"identidad"},
			{"data":"puesto"}			
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_colaboradores_buscar_te.search('').draw();
	$('#buscar').focus();
	
	view_colaboradores_busqueda_te_dataTable("#dataTableColaboradores tbody", table_colaboradores_buscar_te);
}

var view_colaboradores_busqueda_te_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  		
		$('#formulario_transito_enviada #colaborador_id').val(data.colaborador_id);
		$('#formulario_transito_enviada #enviada').val(data.colaborador_id);		
		$('#modal_busqueda_colaboradores').modal('hide');
	});
}

var listar_pacientes_buscar_tr = function(){
	var table_pacientes_buscar_tr = $("#dataTablePacientes").DataTable({		
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
	table_pacientes_buscar_tr.search('').draw();
	$('#buscar').focus();
	
	view_pacientes_busqueda_tr_dataTable("#dataTablePacientes tbody", table_pacientes_buscar_tr);
}

var view_pacientes_busqueda_tr_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		
		$('#formulario_transito_recibida #pacientes_id').val(data.pacientes_id);
		$('#formulario_transito_recibida #paciente_tr').val(data.pacientes_id);
		
		var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
		var pacientes_id = $('#formulario_transito_recibida #paciente_tr').val();
		
		$.ajax({
		   type:'POST',
		   url:url,
		   data:'pacientes_id='+pacientes_id,
		   success:function(data){
			  var array = eval(data);
			  $('#formulario_transito_recibida #identidad').val(array[0]);	  			  
		  }
		});	
		
		$('#modal_busqueda_pacientes').modal('hide');
	});
}

var listar_colaboradores_buscar_tr = function(){
	var table_colaboradores_buscar_tr = $("#dataTableColaboradores").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/facturacion/getColaboradoresTabla.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"colaborador"},
			{"data":"identidad"},
			{"data":"puesto"}			
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_colaboradores_buscar_tr.search('').draw();
	$('#buscar').focus();
	
	view_colaboradores_busqueda_tr_dataTable("#dataTableColaboradores tbody", table_colaboradores_buscar_tr);
}

var view_colaboradores_busqueda_tr_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formulario_transito_recibida #colaborador_id').val(data.colaborador_id);
		$('#formulario_transito_recibida #recibida').val(data.colaborador_id);
		$('#modal_busqueda_colaboradores').modal('hide');
	});
}
//FIN FORMULARIO DE BUSQUEDA

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modal_busqueda_profesion").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_profesion #buscar').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_religion").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_religion #buscar').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_pacientes").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_pacientes #buscar').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#form_main #nueva_factura').on('click', function(e){
	e.preventDefault();
	formFactura();
});

function formFactura(){
	 $('#formulario_facturacion')[0].reset();
	 $('#main_facturacion').hide();	
	 $('#facturacion').show();	
	 $('#label_acciones_volver').html("Volver");
	 $('#acciones_atras').removeClass("active");
	 $('#acciones_factura').addClass("active");
	 $('#label_acciones_factura').html("Factura");
	 $('#formulario_facturacion #fecha').attr('readonly', true);
	 $('#formulario_facturacion #colaborador_id').val(getColaborador_id());
	 $('#formulario_facturacion #colaborador_nombre').val(getProfesional());
	 $('#formulario_facturacion').attr({ 'data-form': 'save' }); 
	 $('#formulario_facturacion').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php' }); 	 
	 limpiarTabla();
	 $('.footer').hide();
	 $('.footer1').show();
	 $('#formulario_facturacion #validar').hide();	
	 $('#formulario_facturacion #guardar1').hide();	 
}

$('#acciones_atras').on('click', function(e){
	 e.preventDefault();
	 if($('#formulario_facturacion #cliente_nombre').val() != "" || $('#formulario_facturacion #colaborador_nombre').val() != ""){
		swal({
		  title: "Tiene datos en la factura",
		  text: "¿Esta seguro que desea volver, recuerde que tiene información en la factura la perderá?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-warning",
		  confirmButtonText: "¡Si, deseo volver!",
		  closeOnConfirm: false	  
		},
		function(){
			$('#main_facturacion').show();
			$('#label_acciones_factura').html("");
			$('#facturacion').hide();
			$('#acciones_atras').addClass("breadcrumb-item active");
			$('#acciones_factura').removeClass("active");
			$('#formulario_facturacion')[0].reset();
			swal.close();
			$('.footer').show();
		    $('.footer1').hide();	
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

$(document).ready(function(){
	getServicio();
	listar_pacientes_buscar();
	listar_servicios_buscar();
	listar_servicios_factura_buscar();
	listar_productos_facturas_buscar();
});

function getProfesional(){
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getProfeisonal.php';
	var profesional
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          profesional = data;			  		  		  			  
		}
	});
	return profesional;	
}

function showFactura(atencion_id){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/editarFactura.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		data:'atencion_id='+atencion_id,
		success:function(data){	
		    var datos = eval(data);
	        $('#formulario_facturacion')[0].reset();
	        $('#formulario_facturacion #pro').val("Registro");
			$('#formulario_facturacion #pacientes_id').val(datos[0]);
            $('#formulario_facturacion #cliente_nombre').val(datos[1]);
            $('#formulario_facturacion #fecha').val(getFechaActual());
            $('#formulario_facturacion #colaborador_id').val(datos[3]);
			$('#formulario_facturacion #colaborador_nombre').val(datos[4]);
			$('#formulario_facturacion #servicio_id').val(datos[5]);		
			$('#label_acciones_volver').html("ATA");
			$('#label_acciones_receta').html("Receta");
			
			$('#formulario_facturacion #fecha').attr("readonly", true);
			$('#formulario_facturacion #validar').attr("disabled", false);
			$('#formulario_facturacion #addRows').attr("disabled", false);
			$('#formulario_facturacion #removeRows').attr("disabled", false);
		    $('#formulario_facturacion #validar').show();
		    $('#formulario_facturacion #editar').hide();
		    $('#formulario_facturacion #eliminar').hide();
			limpiarTabla();				
			
			$('#main_facturacion').hide();	
			$('#facturacion').show();
			
			$('#formulario_facturacion').attr({ 'data-form': 'save' }); 
			$('#formulario_facturacion').attr({ 'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php' }); 

			$('#formulario_facturacion #validar').hide();
			$('#formulario_facturacion #guardar1').hide();
			
			$('.footer').hide();
			$('.footer1').show();	
			cleanFooterValueBill();										
		}
	});
}

$(document).ready(function() {
	$('#formulario_atenciones #search_antecedentes_stop').hide();
	$('#formulario_atenciones #search_historia_clinica_stop').hide();
	$('#formulario_atenciones #search_exame_fisico_stop').hide();
	$('#formulario_atenciones #search_diagnostico_stop').hide();
	$('#formulario_atenciones #search_seguimiento_stop').hide();
	
    var recognition = new webkitSpeechRecognition();
    recognition.continuous = true;
    recognition.lang = "es";
	
    $('#formulario_atenciones #search_antecedentes_start').on('click',function(event){
		$('#formulario_atenciones #search_antecedentes_start').hide();
		$('#formulario_atenciones #search_antecedentes_stop').show();
		recognition.start();
		
		recognition.onresult = function (event) {
			finalResult = '';
			var valor_anterior  = $('#formulario_atenciones #antecedentes').val();
			for (var i = event.resultIndex; i < event.results.length; ++i) {
				if (event.results[i].isFinal) {
					finalResult = event.results[i][0].transcript;
					if(valor_anterior != ""){
						$('#formulario_atenciones #antecedentes').val(valor_anterior + ' ' + finalResult);
						caracteresAntecedentes();
					}else{
						$('#formulario_atenciones #antecedentes').val(finalResult);
						caracteresAntecedentes();
					}				
				}
			}
		};		
		return false;
    });	

    $('#formulario_atenciones #search_historia_clinica_start').on('click',function(event){
		$('#formulario_atenciones #search_historia_clinica_start').hide();
		$('#formulario_atenciones #search_historia_clinica_stop').show();
		recognition.start();
		
		recognition.onresult = function (event) {
			finalResult = '';
			var valor_anterior  = $('#formulario_atenciones #historia_clinica').val();
			for (var i = event.resultIndex; i < event.results.length; ++i) {
				if (event.results[i].isFinal) {
					finalResult = event.results[i][0].transcript;
					if(valor_anterior != ""){
						$('#formulario_atenciones #historia_clinica').val(valor_anterior + ' ' + finalResult);
						caracteresHistoriaClinica();
					}else{
						$('#formulario_atenciones #historia_clinica').val(finalResult);
						caracteresHistoriaClinica();
					}					
				}
			}
		};		
		return false;
    });	

    $('#formulario_atenciones #search_exame_fisico_start').on('click',function(event){
		$('#formulario_atenciones #search_exame_fisico_start').hide();
		$('#formulario_atenciones #search_exame_fisico_stop').show();
		recognition.start();
		
		recognition.onresult = function (event) {
			finalResult = '';
			var valor_anterior  = $('#formulario_atenciones #exame_fisico').val();
			for (var i = event.resultIndex; i < event.results.length; ++i) {
				if (event.results[i].isFinal) {
					finalResult = event.results[i][0].transcript;
					if(valor_anterior != ""){
						$('#formulario_atenciones #exame_fisico').val(valor_anterior + ' ' + finalResult);
						caracteresExamenFisico();
					}else{
						$('#formulario_atenciones #exame_fisico').val(finalResult);
						caracteresExamenFisico();
					}						
				}
			}
		};		
		return false;
    });	

    $('#formulario_atenciones #search_diagnostico_start').on('click',function(event){
		$('#formulario_atenciones #search_diagnostico_start').hide();
		$('#formulario_atenciones #search_diagnostico_stop').show();
		recognition.start();
		
		recognition.onresult = function (event) {
			finalResult = '';
			var valor_anterior  = $('#formulario_atenciones #diagnostico').val();
			for (var i = event.resultIndex; i < event.results.length; ++i) {
				if (event.results[i].isFinal) {
					finalResult = event.results[i][0].transcript;
					if(valor_anterior != ""){
						$('#formulario_atenciones #diagnostico').val(valor_anterior + ' ' + finalResult);
						caracteresDiagnostico();
					}else{
						$('#formulario_atenciones #diagnostico').val(finalResult);
						caracteresDiagnostico();
					}						
				}
			}
		};		
		return false;
    });	

    $('#formulario_atenciones #search_seguimiento_start').on('click',function(event){
		$('#formulario_atenciones #search_seguimiento_start').hide();
		$('#formulario_atenciones #search_seguimiento_stop').show();
		recognition.start();
		
		recognition.onresult = function (event) {
			finalResult = '';
			var valor_anterior  = $('#formulario_atenciones #seguimiento').val();			
			for (var i = event.resultIndex; i < event.results.length; ++i) {
				if (event.results[i].isFinal) {
					finalResult = event.results[i][0].transcript;
					if(valor_anterior != ""){
						$('#formulario_atenciones #seguimiento').val(valor_anterior + ' ' + finalResult);
						caracteresSeguimiento();
					}else{
						$('#formulario_atenciones #seguimiento').val(finalResult);
						caracteresSeguimiento();
					}
				}
			}
		};		
		return false;
    });	

	$('#formulario_atenciones #search_antecedentes_stop').on("click", function(event){
		$('#formulario_atenciones #search_antecedentes_start').show();
		$('#formulario_atenciones #search_antecedentes_stop').hide();
		recognition.stop();
	});	

	$('#formulario_atenciones #search_historia_clinica_stop').on("click", function(event){
		$('#formulario_atenciones #search_historia_clinica_start').show();
		$('#formulario_atenciones #search_historia_clinica_stop').hide();
		recognition.stop();
	});	

	$('#formulario_atenciones #search_exame_fisico_stop').on("click", function(event){
		$('#formulario_atenciones #search_exame_fisico_start').show();
		$('#formulario_atenciones #search_exame_fisico_stop').hide();
		recognition.stop();
	});	

	$('#formulario_atenciones #search_diagnostico_stop').on("click", function(event){
		$('#formulario_atenciones #search_diagnostico_start').show();
		$('#formulario_atenciones #search_diagnostico_stop').hide();
		recognition.stop();
	});	

	$('#formulario_atenciones #search_seguimiento_stop').on("click", function(event){
		$('#formulario_atenciones #search_seguimiento_start').show();
		$('#formulario_atenciones #search_seguimiento_stop').hide();
		recognition.stop();
	});		
});

$(document).ready(function() {
	$('#formulario_atenciones #fecha_nac').on('change', function(){
		var fecha_nac = $('#formulario_atenciones #fecha_nac').val();
		var url = '<?php echo SERVERURL; ?>php/pacientes/getEdad.php';		
			
		$.ajax({
			type: "POST",
			url: url,
			async: true,
			data:'fecha_nac='+fecha_nac,
			success: function(data){
				var array = eval(data);	
				$('#formulario_atenciones #edad').val(array[3]);
			}
		 });
	});		 
});

function volver(){
	$('#main_facturacion').show();
	$('#label_acciones_factura').html("");
	$('#facturacion').hide();
	$('#acciones_atras').addClass("breadcrumb-item active");
	$('#acciones_factura').removeClass("active");	
	$('.footer').show();
	$('.footer1').hide();		
}

function getFechaActual(){
	var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getFechaActual.php';
	var fecha_actual;

	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){
          fecha_actual = data;
		}
	});
	return fecha_actual;	
}

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
//FIN BUSQUEDA SERVICIOS

function getAseguradora(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getAseguradora.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #aseguradora_id').html("");
			$('#formulario_facturacion #aseguradora_id').html(data);			
        }
     });		
}

function getFactEmpresas(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getFactEmpresas.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #fact_empresas_id').html("");
			$('#formulario_facturacion #fact_empresas_id').html(data);			
        }
     });		
}
</script>