<script>
$(document).ready(function() {
   getColaborador();
   getReporte();
   pagination_preclinica(1);
});

$(document).ready(function() {
  $('#form_main #servicio').on('change', function(){	
     pagination_preclinica(1);
  });
});

$(document).ready(function() {
  $('#form_main #reporte').on('change', function(){	
     pagination_preclinica(1);
  });
});

$(document).ready(function() {
  $('#form_main #fecha_i').on('change', function(){
     pagination_preclinica(1);
  });
});

$(document).ready(function() {
  $('#form_main #fecha_f').on('change', function(){	
     pagination_preclinica(1);
  });
});

$(document).ready(function() {
  $('#form_main #bs-regis').on('keyup', function(){	
     pagination_preclinica(1);
  });
});

$(document).ready(function() {
  $('#form_main #colaborador_usuario').on('change', function(){	
     pagination_preclinica(1);
  });
});

function pagination_preclinica(partida){
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var dato = $('#form_main #bs-regis').val();
	var profesional = "";

	if($('#form_main #profesional').val() == "" || $('#form_main #profesional').val() == null){
		profesional = "";
	}else{
		profesional = $('#form_main #profesional').val();
	}	
	
	var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/paginar_preclinica.php';		

	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&desde='+desde+'&hasta='+hasta+'&dato='+dato+'&profesional='+profesional,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);			
		}
	});
	return false;	
}

function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #profesional').html("");
			$('#form_main #profesional').html(data);
        }
     });		
}

function getReporte(){
    var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/getReporte.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #reporte').html("");
			$('#form_main #reporte').html(data);				
        }
     });		
}

function reporteEXCEL(){
  if($('#form_main #servicio').val()!=""){		
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var profesional = $('#form_main #profesional').val();

	if(profesional == "" || profesional == null){
		profesional = "";
	}else{
		profesional = $('#form_main #profesional').val();
	}	
		
	url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/reportePreclinica.php?desde='+desde+'&hasta='+hasta+'&profesional='+profesional;
	    
	window.open(url);	
}else{
	swal({
		title: "Error", 
		text: "Debe seleccionar por lo menos una opción de búsqueda",
		icon: "error", 
		dangerMode: true,
		closeOnEsc: false, // Desactiva el cierre con la tecla Esc
		closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
	});		  
  }		
}

function reporteEXCELDiario(){
	if($('#form_main #servicio').val()!=""){		
		var desde = $('#form_main #fecha_i').val();
		var hasta = $('#form_main #fecha_f').val();
		var profesional = $('#form_main #profesional').val();

		if(profesional == "" || profesional == null){
			profesional = "";
		}else{
			profesional = $('#form_main #profesional').val();
		}	

		var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/reporteDiarioUsuarios.php?desde='+desde+'&hasta='+hasta+'&profesional='+profesional;
		window.open(url);	
	}else{
		swal({
			title: "Error", 
			text: "Debe seleccionar por lo menos una opción de búsqueda",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});	  
	}		
}


function limpiar(){
	$('#servicio').html("");
    $('#agrega-registros').html("");
	$('#pagination').html("");		
    getColaborador();
	getReporte();
	pagination_preclinica(1);
}

function modal_eliminarPreclinica(preclinica_id, pacientes_id){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
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
				text: "¡Sí, remover la preclnica!",
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
			eliminarPreclinica(preclinica_id, value);
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

function modal_eliminarPostClinica(postclinica_id, pacientes_id){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
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
			text: "¿Desea eliminar la postclinica de este usuario: " + dato + "?",
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
				text: "¡Sí, remover la postclinica!",
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
			eliminarPreclinica(postclinica_id, value);
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

function eliminarPreclinica(id, comentario){
  if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/eliminarPreclinica.php';
		
		var fecha = getFechaPreclinica(id);

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
					pagination_preclinica(1);			 
				 }else if(registro == 2){
					swal({
						title: "Error", 
						text: "Error al Eliminar el Registro",
						icon: "error", 
						dangerMode: true,
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
					});
					pagination_preclinica(1);			 
				 }else if(registro == 3){
					swal({
						title: "Error", 
						text: "No se puede eliminar este registro, existe información en la atención del usuario",
						icon: "error", 
						dangerMode: true
					});
					pagination_preclinica(1);			 
				 }else{		
					swal({
						title: "Error", 
						text: "No se puede eliminar este registro, por favor intente de nuevo más tarde",
						icon: "error", 
						dangerMode: true
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
					dangerMode: true
				});			   
			   return false;			
			}	
		}		
  }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		icon: "error", 
		dangerMode: true
	});		
  }
}

$('#eliminar_preclinica #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 eliminarPreclinica();		
});

function editarPreclinica(preclinica_id){
   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	   var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/editarPreclinica.php';
	
	   $.ajax({
		  type:'POST',
		  url:url,
		  data:'id='+preclinica_id,
		  success: function(valores){
			  var datos = eval(valores);
			  $('#formulario_agregar_preclinica #pro').val('Edicion');
  			  $('#formulario_agregar_preclinica #id-registro').val(preclinica_id);
			  $('#formulario_agregar_preclinica #expediente').val(datos[0]);
			  $('#formulario_agregar_preclinica #fecha').val(datos[1]);
			  $('#formulario_agregar_preclinica #identidad').val(datos[2]);
			  $('#formulario_agregar_preclinica #nombre').val(datos[3]);
			  $('#formulario_agregar_preclinica #pa').val(datos[4]);
			  $('#formulario_agregar_preclinica #fr').val(datos[5]);
			  $('#formulario_agregar_preclinica #fc').val(datos[6]);
			  $('#formulario_agregar_preclinica #temperatura').val(datos[7]);
			  $('#formulario_agregar_preclinica #peso').val(datos[8]);
			  $('#formulario_agregar_preclinica #talla').val(datos[9]);
			  $('#formulario_agregar_preclinica #observaciones').val(datos[10]);
			  $('#formulario_agregar_preclinica #profesional_consulta').val(datos[11]);				  

			  $('#formulario_agregar_preclinica').attr({ 'data-form': 'update' }); 
			  $('#formulario_agregar_preclinica').attr({ 'action': '<?php echo SERVERURL; ?>php/reportes_enfermeria/modificarPreclinica.php' });

			  $('#formulario_agregar_preclinica #grupo').hide();
			  $('#edit_preclinica').show();
			  $('#reg_preclinica').hide();
			  $('#agregar_preclinica').modal({
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
  
$('#form_main #exportar').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 e.preventDefault();
	 reporteEXCEL();
 }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		icon: "error", 
		dangerMode: true
	});						 
 }		 
});

$('#form_main #reporte_diario').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 e.preventDefault();
	 reporteEXCELDiario();
 }else{
	swal({
		title: "Acceso Denegado", 
		text: "No tiene permisos para ejecutar esta acción",
		icon: "error", 
		dangerMode: true
	});					 
 }		 
});

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

function getFechaPreclinica(preclinica_id){
    var url = '<?php echo SERVERURL; ?>php/reportes_enfermeria/getFechaPreclinica.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'preclinica_id='+preclinica_id,
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

$('#form_main #limpiar').on('click', function(e){
    e.preventDefault();
    limpiar();
});
</script>