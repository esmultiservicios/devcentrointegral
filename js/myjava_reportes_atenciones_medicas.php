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
    var url = '<?php echo SERVERURL; ?>php/reportes_atenciones_medicas/getServicio.php';		
		
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
	var url = '<?php echo SERVERURL; ?>php/reportes_atenciones_medicas/paginar.php';	
	
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
	 
    url = '<?php echo SERVERURL; ?>php/reportes_atenciones_medicas/reporte.php?desde='+desde+'&hasta='+hasta+'&colaborador='+colaborador;
	
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

	var url = '<?php echo SERVERURL; ?>php/reportes_atenciones_medicas/reporteDiarioAtenciones.php?desde='+desde+'&hasta='+hasta+'&servicio='+servicio+'&colaborador='+colaborador;
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

function modal_eliminarTransitoRecibida(transito_id, expediente){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){	
	   mensajeEliminarTR("Remover","¿Desea eliminar el usuario <b>" + consultarNombre(expediente) + "</b>?");
	   $('#eliminar_transito_recibida #dato').val(transito_id);	
   }else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		
	}	
}

function modal_eliminarTransitoEnviada(transito_id, expediente){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){   
	   mensajeEliminarTE("Remover","¿Desea eliminar el usuario <b>" + consultarNombre(expediente) + "</b>?");
	   $('#eliminar_transito_enviada #dato').val(transito_id);	   
   }else{
		swal({
			title: "Acceso Denegado", 
			text: "No tiene permisos para ejecutar esta acción",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		 
	}	
}

$('#eliminar_transito_recibida #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 eliminarTransitoRecibida();		
});

$('#eliminar_transito_enviada #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 eliminarTransitoEnviada();		 
});

function eliminarTransitoRecibida(){
  if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){	
	var url = '<?php echo SERVERURL; ?>php/reportes_transito/eliminarTransitoRecibida.php';
	var id = $('#eliminar_transito_recibida #dato').val();
		
	var fecha = getFechaRegistroTransitoRecibida(id);
	
    var hoy = new Date();
    fecha_actual = convertDate(hoy);		
	
  if(getMes(fecha)==2){	  
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de este periodo",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	 
		return false;	
  }else{	
   if ( fecha <= fecha_actual){  
	$.ajax({
      type:'POST',
	  url:url,
	  data:'id='+id,
	  success: function(registro){
		 if(registro == 1){
			swal({
				title: "Success", 
				text: "Registro eliminado correctamente",
				type: "success", 
				timer: 3000, //timeOut for auto-close
			});	
			$('#ModalAdd').modal('hide');
			pagination_transito(1);
		 }else{	 
			swal({
				title: "Error", 
				text: "Error al Eliminar el Registro",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});				 
		 }		 
		 return false;
  	  }
	});
	}else{
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de esta fecha",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		   
	   return false;		
	}
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

function eliminarTransitoEnviada(){
  if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){	
	var url = '<?php echo SERVERURL; ?>php/reportes_transito/eliminarTransitoEnviada.php';
	var id = $('#eliminar_transito_enviada #dato').val();
		
	var fecha = getFechaRegistroTransitoEnviada(id);
	
    var hoy = new Date();
    fecha_actual = convertDate(hoy);		
	
  if(getMes(fecha)==2){
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de esta fecha",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});		 
	 return false;	
  }else{	
   if ( fecha <= fecha_actual){  
	$.ajax({
      type:'POST',
	  url:url,
	  data:'id='+id,
	  success: function(registro){
		 if(registro == 1){
			swal({
				title: "Success", 
				text: "Registro eliminado correctamente",
				type: "success",
				timer: 3000, //timeOut for auto-close
			});			 
			pagination_transito(1);
		 }else{
			swal({
				title: "Error", 
				text: "Error al Eliminar el Registro",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});				 
		 }		 
		 return false;
  	  }
	});
	}else{	
		swal({
			title: "Error", 
			text: "No se puede agregar/modificar registros fuera de este periodo",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});			
	    return false;		
	}
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

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function getMes(fecha){
    var url = '<?php echo SERVERURL; ?>php/atas/getMes.php';
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

function getFechaRegistroTransitoRecibida(transito_id){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getFechaTransitoRecibida.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'transito_id='+transito_id,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;
}

function getFechaRegistroTransitoEnviada(transito_id){
    var url = '<?php echo SERVERURL; ?>php/reportes_transito/getFechaTransitoEnviada.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'transito_id='+transito_id,
		async: false,
		success:function(data){	
          fecha = data;			  		  		  			  
		}
	});
	return fecha;	
}

function consultarNombre(id){	
    var url = '<?php echo SERVERURL; ?>php/reporte_hospitalizacion/getNombre.php';
	var resp;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'id='+id,
		async: false,
		success:function(data){	
          resp = data;			  		  		  			  
		}
	});
	return resp;		
}

$('#form_main #reporte_excel').on('click', function(e){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){
    e.preventDefault();
    reporteEXCEL();
 }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});					 
 }
});

$('#form_main #reporte_diario').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){
	 e.preventDefault();
	 reporteEXCELDiario();
 }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		type: "error", 
		confirmButtonClass: 'btn-danger'
	});					 
 }		 
});

$('#form_main #limpiar').on('click', function(e){
    e.preventDefault();
    limpiar();
});
</script>