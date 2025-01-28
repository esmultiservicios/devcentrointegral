<script>
$(document).ready(function() {
   getServicio();
   getProfesionales();
   pagination(1);
});

$(document).ready(function() {
  $('#form_main #servicio').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_main #colaborador').on('change', function(){
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_main #fecha_i').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_main #fecha_f').on('change', function(){	
     pagination(1);
  });
});

$(document).ready(function() {
  $('#form_main #bs_regis').on('keyup', function(){	
     pagination(1);
  });
});

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/getServicio.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_main #servicio').html("");
			$('#form_main #servicio').html(data);
		}			
     });	
}

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#form_main #colaborador').html("");
			$('#form_main #colaborador').html(data);		
		}			
     });	
}

function pagination(partida){
	var colaborador = '';
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var dato = $('#form_main #bs_regis').val();
	var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/paginar.php';	
	
	if($('#form_main #colaborador').val() == "" || $('#form_main #colaborador').val() == null){
		colaborador = "";
	}else{
		colaborador = $('#form_main #colaborador').val();
	}

	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&desde='+desde+'&hasta='+hasta+'&colaborador='+colaborador+'&dato='+dato,	
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);			
		}
	});
	return false;	
}

function reporteEXCEL(){
	var colaborador = '';
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var url = '';
	
	if($('#form_main #colaborador').val() == "" || $('#form_main #colaborador').val() == null){
		colaborador = "";
	}else{
		colaborador = $('#form_main #colaborador').val();
	}
	 
    url = '<?php echo SERVERURL; ?>php/reporte_ausencias/reporte.php?desde='+desde+'&hasta='+hasta+'&colaborador='+colaborador;
	
	window.open(url);
}

function reporteEXCELDiario(){		
	var servicio = '';
	var colaborador = '';
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var url = '';

	if($('#form_main #servicio').val() == "" || $('#form_main #servicio').val() == null){
		servicio = "";
	}else{
		servicio = $('#form_main #servicio').val();
	}
	
	if($('#form_main #colaborador').val() == "" || $('#form_main #colaborador').val() == null){
		colaborador = "";
	}else{
		colaborador = $('#form_main #colaborador').val();
	}

	var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/reporteDiarioAusencias.php?desde='+desde+'&hasta='+hasta+'&servicio='+servicio+'&colaborador='+colaborador;
	window.open(url);			
}

function limpiar(){
	$('#unidad').html("");
	$('#medico_general').html("");
    $('#agrega-registros').html("");
	$('#pagination').html("");		
    getServicio();
	pagination_transito(1);
}

function modal_eliminarAusencias(ausencia_id, pacientes_id){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5){	
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
			text: "¿Desea eliminar la preclínica de este usuario: " + dato + "?",
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
				text: "¡Sí, remover la ausencia!",
				closeModal: false,
				},
			},
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 			
		}).then((value) => {
			if (value === null || value.trim() === "") {
				swal("¡Necesita escribir algo!", { icon: "error" });
				return false;
			}
			eliminarAusencias(ausencia_id, value);
			swal("Ausencia Removida", "La ausencia del usuario ha sido eliminada correctamente.", "success");
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

function eliminarAusencias(id, comentario){
  if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5){
		var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/eliminarAusencias.php';
		
		var fecha = getFechaAusencia(id);

		var hoy = new Date();
		fecha_actual = convertDate(hoy);

		if(getMes(fecha)==2){	  
			swal({
				title: "Error", 
				text: "No se puede agregar/modificar registros fuera de este periodo",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
			});	 		 
			return false;	
		}else{	
		   if ( fecha <= fecha_actual){  
			$.ajax({
			  type:'POST',
			  url:url,
			  data:'id='+id+'&comentario='+comentario,
			  success: function(registro){
				 if(registro == 1){
					swal({
						title: "Success", 
						text: "Registro eliminado correctamente",
						icon: "success", 
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 						
					});						 
					pagination(1);			 
				 }else if(registro == 2){
					swal({
						title: "Error", 
						text: "Error al Eliminar el Registro",
						icon: "error", 
						dangerMode: true,
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 	
					});
					pagination(1);			 
				 }else{		
					swal({
						title: "Error", 
						text: "No se puede eliminar este registro, por favor intente de nuevo más tarde",
						icon: "error", 
						dangerMode: true,
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 	
					});
				 }
				 return false;
			  }
			});
			}else{	
				swal({
					title: "Error", 
					text: "No se puede agregar/modificar registros fuera de esta fecha",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 	
				});			   
			   return false;			
			}	
		}		
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

function getFechaAusencia(ausencia_id){
    var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/getFechaAusencias.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'ausencia_id='+ausencia_id,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;	
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


$('#form_main #reporte_excel').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5){
    e.preventDefault();
    reporteEXCEL();
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

$('#form_main #reporte_diario').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5){
	 e.preventDefault();
	 reporteEXCELDiario();
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

$('#form_main #limpiar').on('click', function(e){
    e.preventDefault();
    limpiar();
});
</script>