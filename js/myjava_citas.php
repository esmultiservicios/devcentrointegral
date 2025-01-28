<script>
$(document).ready(function() {
	getServicio();
	getProfesionales();
	getHoraConsulta();
	actualizarEventos();
	var hoy = new Date();
    fecha_actual = convertDate(hoy);	
	$("#form-addevent #color").css("pointer-events","none");
	$("#ModalEdit #color").css("pointer-events","none");
	
	$('#calendar').fullCalendar({			
	    header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},		
		defaultView: 'agendaWeek',
		height: 792,
		width: 990,		
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		dayNamesShort: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", 	"Octubre", "Noviembre", "Diciembre"],	
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],		
		defaultDate: fecha_actual,
		slotLabelInterval: '00:30:00',
        minTime: "08:00:00",
        maxTime: "23:59:59",
        slotDuration: "00:30:00",
		editable: true,
		eventLimit: true, // allow "more" link when too many events
		selectable: true,
		selectHelper: true,
		//eventDurationEditable: false,
		displayEventTime: true,
	    businessHours: {
          start: '08:00:00', // hora final
          end: '23:59:59', // hora inicial
          dow: [ 1, 2, 3, 4, 5, 6 ] // dias de semana, 0=Domingo
        },	
		
		select: function(start, end) {
		  if(getFechaAusencias(moment(start).format('YYYY-MM-DD HH:mm:ss'), $('#botones_citas #medico_general').val()) == 2){
			if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
                 $("#ModalAdd_enviar").attr('disabled', false);			   				
			     if ($('#medico_general').val()!="" && $('#servicio').val()!=""){
				     $('#form-addevent')[0].reset();	
			         //if (moment(start).format('YYYY-MM-DD HH:mm:ss') >= fecha_actual){
					    $('#ModalAdd #fecha_cita').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
			            $('#ModalAdd #fecha_cita_end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
			            $('#ModalAdd #medico').val($('#botones_citas #medico_general').val());
						$('#ModalAdd #unidad').val($('#botones_citas #unidad').val());
					    $('#ModalAdd #serv').val($('#botones_citas #servicio').val()); 
						$('#form-addevent #profesional_citas').val(getProfesionalName($('#botones_citas #medico_general').val()));
			
		                $('#ModalAdd').modal({
							show:true,
							keyboard: false,
							backdrop:'static'
		                });
                        $('#mensaje_ModalAdd').removeClass('error');					  
					    $('#mensaje_ModalAdd').removeClass('bien');
					    $('#mensaje_ModalAdd').hide();
					    $('#mensaje_ModalAdd').html("");
			         /*}else{				  	    	
						swal({
							title: "Error", 
							text: "No se puede agregar una cita en esta fecha",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera	
						});								
			         }*/										
			         }else{
						swal({
							title: "Error", 
							text: "Debe seleccionar un médico y un servcicio antes de agendar una cita",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});					
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
		  }else{			  
				swal({
					title: "Error", 
					text: "El médico se encuentra ausente, no se le puede agendar una cita. " + getComentarioAusencia(moment(start).format('YYYY-MM-DD HH:mm:ss'))+ "",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});		  
		  }			   
		},
		eventRender: function(event, element) {
		  element.bind('dblclick', function() { 
              if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
				 $("#ModalEdit_enviar").attr('disabled', false);			   
	             $("#ModalImprimir_enviar").attr('disabled', false);
		         $('#form-editevent')[0].reset();		  
		         var palabras = event.title.split("-");	
                 var fecha = moment(event.start).format('YYYY-MM-DD HH:mm:ss').split(" ");					 
		         $('#ModalEdit #paciente').val(palabras[1]);
		         $('#ModalEdit #fecha_citaedit1').val(moment(event.start).format('YYYY-MM-DD HH:mm:ss'));	
		         $('#ModalEdit #fecha_citaeditend').val(moment(event.end).format('YYYY-MM-DD HH:mm:ss'));			 
		         $('#ModalEdit #color').val(event.color);
                 getColaborador_id(event.id);
				 getComentario(event.id);
				 getComentario1(event.id);
				 getHora(event.id);
				 getFechaInicio(event.id);
				 getHoraInicio(event.id);
				 getExpediente(event.id);
		         $('#ModalEdit #id').val(event.id);				
		         $('#ModalEdit').modal({
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
		},
		eventDrop: function(event, delta, revertFunc) { // si changement de position
		   if(getFechaAusencias(moment(event.start).format('YYYY-MM-DD HH:mm:ss')) == 2){
		       if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		           if (moment(event.start).format('YYYY-MM-DD HH:mm:ss') >= fecha_actual){
			          edit(event);	
		           }else{   
						swal({
							title: "Error", 
							text: "No se puede mover una cita en esta fecha",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});			
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
		   }else{			    
				swal({
					title: "Error", 
					text: "El médico se encuentra ausente, no se le puede agendar una cita. " + getComentarioAusencia(moment(event.start).format('YYYY-MM-DD HH:mm:ss')) + "",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});			   
		   }	
		},
		/*eventResize: function(event,dayDelta,minuteDelta,revertFunc) { // si changement de longueur
		 if(getFechaAusencias(moment(start).format('YYYY-MM-DD HH:mm:ss')) == 2){
		   if (getUsuarioSistema() == 1){
			   edit(event);
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
		 }else{			  
			swal({
				title: "Error", 
				text: "El médico se encuentra ausente, no se le puede agendar una cita",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});	  
		  }	
		}*///, events: "<?php echo SERVERURL; ?>php/citas/getCalendar.php",
    });
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#ModalAdd").on('shown.bs.modal', function(){
        $(this).find('#form-addevent #expediente').focus();
    });
});

$(document).ready(function(){
    $("#registrar_ausencias").on('shown.bs.modal', function(){
        $(this).find('#formulario_ausencias #comentario_ausencias').focus();
    });
});

$(document).ready(function(){
    $("#registrar_config_edades").on('shown.bs.modal', function(){
        $(this).find('#formulario_config_edades #edad').focus();
    });
});

$(document).ready(function(){
    $("#modal_sobrecupo").on('shown.bs.modal', function(){
        $(this).find('#formulario_sobrecupo #sobrecupo_expediente').focus();
    });
});

$(document).ready(function(){
    $("#buscarCita").on('shown.bs.modal', function(){
        $(this).find('#form-buscarcita #bs-regis').focus();
    });
});

$(document).ready(function(){
    $("#buscarHistorial").on('shown.bs.modal', function(){
        $(this).find('#form-buscarhistorial #bs-regis').focus();
    });
});

$(document).ready(function(){
    $("#buscarHistorialReprogramaciones").on('shown.bs.modal', function(){
        $(this).find('#form_buscarhistorial_reprogramaciones #bs-regis').focus();
    });
});

$(document).ready(function(){
    $("#buscarHistorialNo").on('shown.bs.modal', function(){
        $(this).find('#form-buscarhistorialno #bs-regis').focus();
    });
});

$(document).ready(function(){
    $("#modal_busqueda_colaboradores").on('shown.bs.modal', function(){
        $(this).find('#formulario_busqueda_coloboradores #buscar').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$('#formulario_reportes #reportes_exportar').on('click', function(e){
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){	
	e.preventDefault();
	if($('#reporte_servicio').val() != ""){
	   reporteEXCEL();
	}else{
		swal({
			title: "Error", 
			text: "No se puede generar el reporte",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		   
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
});
			
$('#botones_citas #ausencias').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 e.preventDefault();
	 $('#formulario_ausencias')[0].reset();
      pagination_ausencias(1);	 
	 $('#registrar_ausencias').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	 });
	 $('#formulario_ausencias #pro_ausencias').val("Registrar");
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

$('#botones_citas #config_edades').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 e.preventDefault();
	 $('#formulario_config_edades')[0].reset();
	 $('#registrar_config_edades').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	 });
	 $('#formulario_config_edades')[0].reset();
	 getEdadConfig();
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

$('#botones_citas #sobrecupo').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 e.preventDefault();
	 $('#formulario_sobrecupo')[0].reset();
      pagination_ausencias(1);	 
	 $('#modal_sobrecupo').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	 });
	 clean_sobrecupo();
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

$('#botones_citas #historial_nopresento').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
     e.preventDefault();
	 $('#form-buscarhistorialno')[0].reset();
     pagination_busqueda_historial_nopresento(1);	 
	 $('#buscarHistorialNo').modal({
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

$('#botones_citas #historial_reprogramaciones').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
     e.preventDefault();
	 $('#form_buscarhistorial_reprogramaciones')[0].reset();
     //pagination_historial_nopresento(1);	
     pagination_busqueda_reprogramaciones(1);		 
	 $('#buscarHistorialReprogramaciones').modal({
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

$('#botones_citas #historial').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
     e.preventDefault();
	 $('#form-buscarhistorial')[0].reset();
     pagination_busqueda_historial(1);	 
	 $('#buscarHistorial').modal({
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

$('#botones_citas #search').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
     e.preventDefault();
	 $('#form-buscarcita')[0].reset();
     pagination_busqueda(1);	 
	 $('#buscarCita').modal({
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

$('#ModalAdd_enviar').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 if ($('#expediente').val() == "" || $('#fecha_cita').val() == "" ){
		 $('#form-addevent')[0].reset();
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		   
		return false;
	 }else{
		e.preventDefault();
		agregaRegistro();
	 }     
});

$('#mensaje_status #mensaje_status_okay').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 if ($('#mensaje_status #status_repro').val() == "" || $('#mensaje_status #mensaje_status_comentario').val() == "" ){					
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});			 
		return false;
	 }else{
	   e.preventDefault();
	   modificarStatus();
	 }   
});

$('#mensaje_status #mensaje_status_refresh').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 getStatusRepro();  
});

$('#ModalDelete_enviar').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	 if ($('#fecha_citaedit').val() == "" || $('#fecha_citaeditend').val() == "" ){
		$('#form-editevent')[0].reset();
		swal({
			title: "Error", 
			text: "No se puede eliminar el registro, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});			 		   
		return false;
	 }else{
		e.preventDefault();
        eliminar();
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
});

$('#ModalImprimir_enviar').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
	 if ($('#fecha_citaedit').val() == "" || $('#fecha_citaeditend').val() == "" ){
		$('#form-editevent')[0].reset();
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		   
		return false;
	 }else{
        e.preventDefault();		 
		reportePDF($('#form-editevent #id').val());
	 } 		
});

$('#ModalEdit_enviar').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
	 if ($('#fecha_citaedit').val() == "" || $('#fecha_citaeditend').val() == "" ){
		$('#form-editevent')[0].reset();				
		swal({
			title: "Error", 
			text: "No se pueden enviar los datos, los campos estan vacíos",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});			   
		return false;
	 }else{
        e.preventDefault();		 
		actualizar(); 
	 } 		
});

$('#reg_ausencias').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 agregaAusencias();	
});
   	
$('#reg_buscarausencias').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
	 e.preventDefault();
	 pagination_ausencias(1);	
});

$('#botones_citas #refresh').on('click', function(e){ // delete event clicked // We don't want this to act as a link so cancel the link action
	e.preventDefault();
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		 if ( $('#botones_citas #servicio').val() == ""){
			swal({
				title: "Error", 
				text: "Debe seleccionar un servicio de la lista, antes de poder refrescar los eventos",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});		
		 }else if ( $('#botones_citas #medico_general').val() == ""){
			swal({
				title: "Error", 
				text: "Debe seleccionar un médico de la lista, antes de poder refrescar los eventos",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});
		 }else{
			actualizarEventos(); 
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
});

$(document).ready(function() { 
    getStatusRepro();
});

$(document).ready(function() { 
$(function(){
	$('#reportes_fechai').on('change', function(){	
		pagination_busqueda_reportes(1);
	});
  });
});

$(document).ready(function() { 
$(function(){
	$('#reportes_fechaf').on('change', function(){	
		pagination_busqueda_reportes(1);
	});
  });
});
 
$(document).ready(function() {
	setInterval('actualizarEventos()',4000);	
});

//Buscar Cita de Usuarios
$(document).ready(function() {
	$('#form-buscarcita #bs-regis').on('keyup',function(){      
	    pagination_busqueda(1);
   	    return false;
    });	
});

//Buscar Historial usuarios que llegaron a su cita
$(document).ready(function() {
	$('#form-buscarhistorialno #bs-regis').on('keyup',function(){      
	    pagination_busqueda_historial_nopresento(1);
   	    return false;
    });	
});

//Buscar Historial usarios que no se presentaron a su cita
$(document).ready(function() {
	$('#form-buscarhistorial #bs-regis').on('keyup',function(){      
	    pagination_busqueda_historial(1);
   	    return false;
    });	
});

//Buscar Historial usarios que no se presentaron a su cita
$(document).ready(function() {
	$('#form_buscarhistorial_reprogramaciones #bs-regis').on('keyup',function(){      
	    pagination_busqueda_reprogramaciones(1);
   	    return false;
    });	
});

function pagination_busqueda_historial_nopresento(partida){
	var url = '<?php echo SERVERURL; ?>php/citas/buscar_historial_nosepresento.php';
    var dato = $('#form-buscarhistorialno #bs-regis').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#form-buscarhistorialno #agrega-registros').html(array[0]);
			$('#form-buscarhistorialno #pagination').html(array[1]);
		}
	});
	return false;
}

function pagination_busqueda_reprogramaciones(partida){
	var url = 'php/citas/buscar_historial_reprogramaciones.php';
    var dato = $('#form_buscarhistorial_reprogramaciones #bs-regis').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#form_buscarhistorial_reprogramaciones #agrega-registros').html(array[0]);
			$('#form_buscarhistorial_reprogramaciones #pagination').html(array[1]);
		}
	});
	return false;
}

function pagination_busqueda(partida){
	var url = '<?php echo SERVERURL; ?>php/citas/buscar_cita.php';
	var dato = $('#form-buscarcita #bs-regis').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#form-buscarcita #agrega-registros').html(array[0]);
			$('#form-buscarcita #pagination').html(array[1]);
		}
	});
	return false;
}

function pagination_busqueda_historial(partida){
	var url = '<?php echo SERVERURL; ?>php/citas/buscar_historial.php';
	var dato = $('#form-buscarhistorial #bs-regis').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato,
		success:function(data){
			var array = eval(data);
			$('#form-buscarhistorial #agrega-registros').html(array[0]);
			$('#form-buscarhistorial #pagination').html(array[1]);
		}
	});
	return false;
}
    
function agregaRegistro(){
	var url = '<?php echo SERVERURL; ?>php/citas/addEvent.php';
	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:$('#form-addevent').serialize(),
		success: function(registro){		
          if (registro == 1){
				$('#form-addevent')[0].reset();	
				swal({
					title: "Error", 
					text: "Los datos ingresados son incorrectos",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				$("#mensaje_ModalAdd #mensaje_ModalAdd").attr('disabled', true);               
				return false;
		  }else if (registro == 2){
				$('#form-addevent')[0].reset();
				swal({
					title: "Error", 
					text: "Usuario ya tiene cita agendada en ese dia",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});	
				$("#mensaje_ModalAdd #mensaje_ModalAdd").attr('disabled', true);  			   
			   return false;
		  }else if (registro == 3){
				$('#form-addevent')[0].reset();
				swal({
					title: "Error", 
					text: "El médico ya tiene esta hora ocupada",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				$("#mensaje_ModalAdd #mensaje_ModalAdd").attr('disabled', true);  			   
				return false;
		  }else if (registro == 4){
				$('#form-addevent')[0].reset();	
				swal({
					title: "Error", 
					text: "Error al completar el registro, por favor verifique la información",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				$("#mensaje_ModalAdd #mensaje_ModalAdd").attr('disabled', true);  			   
				return false;
		 }else{
             $("#mensaje_ModalAdd #mensaje_ModalAdd").attr('disabled', false);  			  
		     $("#calendar").fullCalendar('renderEvent',
             {
                id: registro.id,
                title: registro.title,
                start: registro.start,
                end: registro.end,
                color: registro.color,					   
             },
                   true);
		     $('#form-addevent')[0].reset();	
				swal({
					title: "Success", 
					text: "Cita agendada correctamente",
					icon: "success",
					timer: 3000,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});
				$('#ModalAdd').modal('hide');
				$("#ModalAdd_enviar").attr('disabled', true);
				reportePDF(registro.id);
				sendEmail(registro.id);
				return false;			
		  }		
		},
		error : function(){
			swal({
				title: "Error", 
				text: "No se enviaron los datos, favor corregir",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});			
		}
	});
	return false;
}

function getComentario(paciente_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getComentario.php';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'paciente_id='+paciente_id,
		success:function(data){	
          $('#ModalEdit #coment1').val(data);			  		  		  			  
		}
	});
}

function getComentario1(paciente_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getComentario1.php';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'paciente_id='+paciente_id,
		success:function(data){	
          $('#ModalEdit #coment_1').val(data);			  		  		  			  
		}
	});
}

function getExpediente(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getExpediente.php';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(data){	
          $('#ModalEdit #expediente_edit').val(data);			  		  		  			  
		}
	});
}

function edit(event){
  start = event.start.format('YYYY-MM-DD HH:mm:ss');
  if(event.end){
	end = event.end.format('YYYY-MM-DD HH:mm:ss');
  }else{
	end = start;
  }
			
  id =  event.id;
	
  Event = [];
  Event[0] = id;
  Event[1] = start;
  Event[2] = end;	

  var pacientes_id = getPacientes_id(id);
  var colaborador_id = getColaboradorEdicion_id(id);
  var servicio_id = getServicio_id(id); 
  var usuario = getNombreUsuario(pacientes_id);	
  
  $.ajax({
	 url: '<?php echo SERVERURL; ?>php/citas/editEventDate.php',
	 type: "POST",
	 async: true,
	 data: {Event:Event},
	 success: function(rep) {
  	   if(rep == 1){
		 $('#mensaje_status').modal({
		    show:true,
		    backdrop:'static'
		 });
	     $('#mensaje_status #mensaje_status_mensaje').html("Se ha sido modificado la fecha de la cita para el usuario: " + usuario + ". ¿Por favor agregue un Estatus de la Reprogramación?"); 
	     $('#mensaje_status #bad').hide();
	     $('#mensaje_status #okay').show();
		 $('#mensaje_status #mensaje_status_agenda_id').val(getNewAgendaID(pacientes_id,colaborador_id,servicio_id,start));
		 reportePDF(getNewAgendaID(pacientes_id,colaborador_id,servicio_id,start));
		 sendEmailReprogramación(getNewAgendaID(pacientes_id,colaborador_id,servicio_id,start));
		 getStatusRepro();				  
	   }else if(rep == 2){
			swal({
				title: "Error", 
				text: "Error al modificar la fecha de la cita",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});			 
	   }else if(rep == 3){
			swal({
				title: "Error", 
				text: "Usuario ya tiene cita agendada en ese dia",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});		 
	   }else if(rep == 4){
			swal({
				title: "Error", 
				text: "El médico ya tiene esta hora ocupada",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});				
		 }else if(rep == 5){	
			swal({
				title: "Error", 
				text: "No se puede mover este usuario a esta hora",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});				
		 }else if(rep == 6){
			swal({
				title: "Error", 
				text: "Este usuario ya tiene realizada su preclínica, no se puede realizar ningún cambio",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});				
		 }
	  }
  });
}

function actualizar(){ 
	$('#mensaje_ModalAdd').removeClass('error');
       var eventID = $('#ModalEdit #id').val();   
	   var url = '<?php echo SERVERURL; ?>php/citas/actualizarEventTitle.php';
           $.ajax({
               url: url,
			   async: true,
               data:$('#form-editevent').serialize(),
               type: "POST",
               success: function(json) {
                   if(json == 1){
						swal({
							title: "Success", 
							text: "Cita modificada correctamente",
							icon: "success",
							timer: 3000, //timeOut for auto-close
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera														
						});
						$('#ModalEdit').modal('hide');
						reportePDF(eventID);
						sendEmailCambioCita(eventID);
						$("#ModalDelete_enviar").attr('disabled', true);
						$("#ModalEdit_enviar").attr('disabled', true);			   
						$("#ModalImprimir_enviar").attr('disabled', true);
                       if ($('#form-editevent #colaborador').val() != $('#form-editevent #medico1').val()){
						   $("#calendar").fullCalendar('removeEvents', eventID);	
                           $('#calendar').fullCalendar('rerenderEvents' );	
                           $('#calendar').fullCalendar('updateEvent', json);		
                           $('#calendar').fullCalendar('rerenderEvents' );	
                           $("#ModalEdit_enviar").attr('disabled', false);						   
					   }else{
                          $('#calendar').fullCalendar('updateEvent', json);		
                          $('#calendar').fullCalendar('rerenderEvents' );
                          $("#ModalEdit_enviar").attr('disabled', false); 						  
					   }					   
				    }else if(json == 2){
						swal({
							title: "Error", 
							text: "Los datos ingresados son incorrectos",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
						$("#ModalEdit_enviar").attr('disabled', true);  						  
			        }else if(json == 3){
						swal({
							title: "Error", 
							text: "Usuario ya tiene cita agendada en ese dia",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
                        $("#ModalEdit_enviar").attr('disabled', true); 						  
			         }else if(json == 4){
						swal({
							title: "Error", 
							text: "El médico ya tiene esta hora ocupada",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});						  
						 $("#ModalEdit_enviar").attr('disabled', true); 
			        }else if(json == 5){	
						swal({
							title: "Error", 
							text: "Este usuario ya tiene realizada su preclínica, no se puede realizar ningún cambio",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});						  
						$("#ModalEdit_enviar").attr('disabled', true); 
			        }   
               }
           });
}

function eliminar(){ 
    var eventID = $('#ModalEdit #id').val();
	var comentario = $('#ModalEdit #coment').val();
	
	     var url = '<?php echo SERVERURL; ?>php/citas/eliminarEventTitle.php';
           $.ajax({
               url: url,
               data: 'delete=delete&id='+eventID+'&comentario='+comentario,
               type: "POST",
			   async: true,
               success: function(json) {
                   if(json == 1){
						$('#form-editevent')[0].reset();
						$("#calendar").fullCalendar('removeEvents',eventID);	
						$('#calendar').fullCalendar('rerenderEvents' );					   
						swal({
							title: "Success", 
							text: "Cita eliminada correctamente",
							icon: "success", 
							timer: 3000, //timeOut for auto-close
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera							
						});	
						$('#ModalEdit').modal('hide');
						$("#ModalDelete_enviar").attr('disabled', true);
						$("#ModalEdit_enviar").attr('disabled', true);			   
						$("#ModalImprimir_enviar").attr('disabled', true);
						$("#form-editevent #fecha_citaedit").val(getFechaSistema());
						return false;					   
				   }else if(json == 2){	
						swal({
							title: "Error", 
							text: "No se puedo eliminar el registro",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});				   				  
						return false;					   					   
				   }else if(json == 3){	
						swal({
							title: "Error", 
							text: "No se puedo eliminar el registro ya se realizo la preclínica para este usuario",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});		   
						return false;					   					   
				   }else if(json == 4){	
						swal({
							title: "Error", 
							text: "Lo sentimos, no se puede eliminar esta cita, debido a que sobrepasa el tiempo permitido, por favor proceda a reprogramar la cita, para poder continuar",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});			   
						return false;					   					   
				   }else{
						swal({
							title: "Error", 
							text: "Error al procesar la solicitud",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
						return false;
				   }
               }
           });	   
}
	
$(document).ready(function(e) {
    $('#form-addevent #expediente').on('blur', function(){
		var url = '<?php echo SERVERURL; ?>php/citas/buscar_expediente.php';
        var expediente = $('#expediente').val();
        var colaborador_id = $('#medico_general').val();		
		var start = $('#fecha_cita').val();
		var end = $('#fecha_cita_end').val();
		var servicio_id = $('#serv').val();

	    if($('#expediente').val() != ""){
	         $.ajax({
		        type:'POST',
		        url:url,
		        async: true,
		        data:'expediente='+expediente+'&colaborador_id='+colaborador_id+'&start='+start+'&end='+end+'&servicio_id='+servicio_id+'&unidad='+unidad,
		        success:function(data){			   
			       if (data == 1){
						swal({
							title: "Error", 
							text: "El Profesional ya tiene esa hora ocupada",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
						$("#ModalAdd_enviar").attr('disabled', true);				  
						return false;				  
			       }else if (data == 2){
						swal({
							title: "Error", 
							text: "El paciente ya tiene esa hora ocupada",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});				  			  
						$("#ModalAdd_enviar").attr('disabled', true);
						return false;				  
			       }else{
				         var array = eval(data);
				         if (array[3] == 'NulaSError'){
							swal({
								title: "Error", 
								text: "No se puede agendar este usuario en esta hora",
								icon: "error", 
								dangerMode: true,
								closeOnEsc: false, // Desactiva el cierre con la tecla Esc
								closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
							});
							$("#ModalAdd_enviar").attr('disabled', true);
							return false;	
				         }else if (array[3] == 'NuevosExcede'){
							swal({
								title: "Error", 
								text: "No se puede agendar mas usuarios nuevos ya llego al límite",
								icon: "error", 
								dangerMode: true,
								closeOnEsc: false, // Desactiva el cierre con la tecla Esc
								closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
							});
							$("#ModalAdd_enviar").attr('disabled', true);
							return false;	
				         }else if (array[3] == 'SubsiguienteExcede'){
							swal({
								title: "Error", 
								text: "No se puede agendar mas usuarios subsiguientes ya llego al límite",
								icon: "error", 
								dangerMode: true,
								closeOnEsc: false, // Desactiva el cierre con la tecla Esc
								closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
							});
							return false;	
				         }else if (array[3] == 'Vacio'){
							swal({
								title: "Error", 
								text: "El profesional no tiene asignada una jornada laboral",
								icon: "error", 
								dangerMode: true,
								closeOnEsc: false, // Desactiva el cierre con la tecla Esc
								closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
							});
							$("#ModalAdd_enviar").attr('disabled', true);
							return false;	
				        }else{
			                 $('#form-addevent #paciente_id').val(array[0]);
			                 $('#form-addevent #nombre').val(array[1]);
			                 $('#form-addevent #color').val(array[2]);				  
				             $('#form-addevent #hora').val(array[3]);	
				             $('#form-addevent #medico').val(array[4]);
                             $("#form-addevent #ModalAdd_enviar").attr('disabled', false);	
				       }			  					  
			       }		  		  		  			  
		    }
	  });
	 }
	  return false;	 
	});
});

$(document).ready(function(e) {
  $('#ModalEdit #hora_nueva').on('change', function(){	
    var url = '<?php echo SERVERURL; ?>php/citas/getHora.php';
	var fecha = $('#ModalEdit #fecha_citaedit').val();
	var hora = $('#ModalEdit #hora_nueva').val();
	var agenda_id = $('#ModalEdit #id').val();
	var colaborador_id = $('#ModalEdit #colaborador').val();
	
    var hoy = new Date();
	
	if ( fecha >= fecha_actual){
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'fecha='+fecha+'&agenda_id='+agenda_id+'&colaborador_id='+colaborador_id+'&hora='+hora,
		success:function(data){	
			 if (data == 'NulaSError'){
				swal({
					title: "Error", 
					text: "No se puede agendar este usuario en esta hora",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});				
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else if (data == 'NuevosExcede'){
				swal({
					title: "Error", 
					text: "No se puede agendar mas usuarios nuevos ya llego al límite",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});					
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else if (data == 'SubsiguienteExcede'){
				swal({
					title: "Error", 
					text: "No se puede agendar mas usuarios subsiguientes ya llego al límite",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});						
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else if (data == 'Vacio'){
				swal({
					title: "Error", 
					text: "El profesional no tiene asignada una jornada laboral",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});	
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else if (data == 1){
				swal({
					title: "Error", 
					text: "El Profesional ya tiene esa hora ocupada",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else if (data == 2){
				swal({
					title: "Error", 
					text: "El Paciente ya tiene esa hora ocupada",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				$("#ModalEdit_enviar").attr('disabled', true);
				return false;
			 }else{
			     $('#ModalEdit #hora_citaeditend').val(data);
				 $("#ModalEdit_enviar").attr('disabled', false);
                 getFecha(fecha, hora);
		         return false;				  		  		  		  			  
		     }
		}
	});
	}else{
		swal({
			title: "Error", 
			text: "No se puede realizar esta acción en esta fecha",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		
		$("#ModalEdit_enviar").attr('disabled', true);
		return false;		
	}
	return false;
  });
});

$(document).ready(function(e) {
  $('#ModalEdit #fecha_citaedit').on('blur', function(){	
   if(getFechaAusenciasEdicionCitas($('#ModalEdit #fecha_citaedit').val()) == 2){
		var url = '<?php echo SERVERURL; ?>php/citas/getHora.php';
		var fecha = $('#ModalEdit #fecha_citaedit').val();
		var hora = $('#ModalEdit #hora_nueva').val();
		var agenda_id = $('#ModalEdit #id').val();
		var colaborador_id = $('#ModalEdit #colaborador').val();
		
		var hoy = new Date();

		if ( fecha >= fecha_actual){	
			$.ajax({
			   type:'POST',
			   url:url,
			   async: true,
			   data:'fecha='+fecha+'&agenda_id='+agenda_id+'&colaborador_id='+colaborador_id+'&hora='+hora,
			   success:function(data){	
					 if (data == 'NulaSError'){
						swal({
							title: "Error", 
							text: "No se puede agendar este usuario en esta hora",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});				
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else if (data == 'NuevosExcede'){
						swal({
							title: "Error", 
							text: "No se puede agendar mas usuarios nuevos ya llego al límite",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});					
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else if (data == 'SubsiguienteExcede'){
						swal({
							title: "Error", 
							text: "No se puede agendar mas usuarios subsiguientes ya llego al límite",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});						
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else if (data == 'Vacio'){
						swal({
							title: "Error", 
							text: "El profesional no tiene asignada una jornada laboral",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});	
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else if (data == 1){
						swal({
							title: "Error", 
							text: "El Profesional ya tiene esa hora ocupada",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else if (data == 2){
						swal({
							title: "Error", 
							text: "El Paciente ya tiene esa hora ocupada",
							icon: "error", 
							dangerMode: true,
							closeOnEsc: false, // Desactiva el cierre con la tecla Esc
							closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
						});
						$("#ModalEdit_enviar").attr('disabled', true);
						return false;
					 }else{
						 $('#ModalEdit #hora_citaeditend').val(data);
						 $("#ModalEdit_enviar").attr('disabled', false);
						 getFecha(fecha, hora);
						 return false;				  		  		  		  			  
					 }
				}
			});
		}else{
			swal({
				title: "Error", 
				text: "No se puede realizar esta acción en esta fecha",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});
			 $("#ModalEdit_enviar").attr('disabled', true);
			 $("#ModalEdit #hora_nueva").attr('disabled', false);
			 return false;			
		}
	  return false;
   }else{
		swal({
			title: "Error", 
			text: "l médico se encuentra ausente, no se le puede agendar una cita. " + getComentarioAusencia($('#ModalEdit #fecha_citaedit').val())+ "",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});					   
		$("#ModalEdit_enviar").attr('disabled', true);	
        $("#ModalEdit #hora_nueva").attr('disabled', true);			
   }
  });
});

function getFecha(fecha, hora){
    var url = '<?php echo SERVERURL; ?>php/citas/getFecha.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'fecha='+fecha+'&hora='+hora,
		success:function(data){	
           var datos = eval(data);		
		   $('#ModalEdit #fecha_citaedit1').val(datos[0]);
		   $('#ModalEdit #fecha_citaeditend').val(datos[1]);		   
		}
	});
	return false;		
}

function getColaborador_id(dato){
    var url = '<?php echo SERVERURL; ?>php/citas/colaborador.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+dato,
		success:function(data){		
		  if (data == 'Error'){		
				swal({
					title: "Error", 
					text: "Error en los datos",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});				 
				return false;				  
		  }else{;			 			 
		 	  $('#ModalEdit #medico1').val(data);	
			  $('#ModalEdit #colaborador').val(data);	  
		  }		  		  		  			  
		 }
	});
	return false;		
}

function getHora(dato){
    var url = '<?php echo SERVERURL; ?>php/citas/getHoraUsuario.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'agenda_id='+dato,
		success:function(data){			
		   $('#ModalEdit #hora_citaeditend').val(data);	
		}
	});
	return false;		
}

function getFechaInicio(dato){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaInicio.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'agenda_id='+dato,
		success:function(data){			
		   $('#ModalEdit #fecha_citaedit').val(data);		  		  			  
		}
	});
	return false;		
}

function getHoraInicio(dato){
    var url = '<?php echo SERVERURL; ?>php/citas/getHoraInicio.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		data:'agenda_id='+dato,
		success:function(data){			
		   $('#ModalEdit #hora_nueva').val(data); 		   
		}
	});
	return false;		
}

function actualizarEventos(){
	if ($('#botones_citas #servicio').val() != ""){
		var servicio_id = $('#botones_citas #servicio').val();
        var url = '<?php echo SERVERURL; ?>php/citas/getCalendar_busqueda.php'; 
		
	    $.ajax({
          type: "POST",
          url: url,
		  async: true,
          data:'&servicio='+servicio_id,
          success: function(events){
              $('#calendar').fullCalendar('removeEvents');
              $('#calendar').fullCalendar('addEventSource', events);         
              $('#calendar').fullCalendar('rerenderEvents');
          } 
        });			
	}	
}

$(document).ready(function() {
   $('#form-editevent #colaborador').on('change', function(){
	  var fecha = $('#form-editevent #fecha_citaedit').val();
         if(getFechaAusenciasEdicionCitas(fecha) == 2){
		     $("#ModalEdit_enviar").attr('disabled', false);
		 }else{	
				$("#ModalEdit_enviar").attr('disabled', true);
				swal({
					title: "Error", 
					text: "El médico se encuentra ausente, no se le puede mover una cita. " + getComentarioAusencia(fecha) + "",
					icon: "error", 
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});			 
	    }
    });					
});		

function convertDate(inputFormat) {
     function pad(s) { return (s < 10) ? '0' + s : s; }
     var d = new Date(inputFormat);
     return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

//BOOSTRAP SELECT
$(document).ready(function() {
	  $('#servicio').on('change', function(){
		  actualizarEventos();
      });					
});

function agregaAusencias(){
	var url = '<?php echo SERVERURL; ?>php/citas/agregarAusencias.php';
	$.ajax({
		type:'POST',
		url:url,
		data:$('#formulario_ausencias').serialize(),
		success: function(registro){
			if(registro == 1){
				pagination_ausencias(1);
				getProfesionales();
				$('#formulario_ausencias #comentario_ausencias').val("");
				$('#formulario_ausencias #pro_ausencias').val("Registro");
				swal({
					title: "Success", 
					text: "Registro almacenado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});
				$('#formulario_ausencias #medico_ausencia').html("");			   
				return false;				
			}else{
				swal({
					title: "Error", 
					text: "No se puede procesar su solicitud",
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});				
			}
		}
	});
	return false;
}

$(document).ready(function() {
	  $('#colaborador_ausencia').on('change', function(){
		var puesto_id = $('#colaborador_ausencia').val();
        var url = '<?php echo SERVERURL; ?>php/citas/getMedicoAusencias.php';		
		
		$.ajax({
            type: "POST",
            url: url,
			async: true,
            data:'puesto_id='+puesto_id,
            success: function(data){
				$('#medico_ausencia').html(data);				
            }
         });
		 
      });					
});

function getFechaAusenciasEdicionCitas(fecha){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaAusencias.php';
	var colaborador_id = $('#form-editevent #colaborador').val();
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

function pagination_ausencias(partida){
	var url = '<?php echo SERVERURL; ?>php/citas/paginar_ausencias.php';
	var medico = $('#formulario_ausencias #medico_ausencia').val();
	var fechai = $('#formulario_ausencias #fecha_ausencia').val();
	var fechaf = $('#formulario_ausencias #fecha_ausenciaf').val();
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&medico='+medico+'&fechai='+fechai+'&fechaf='+fechaf,
		success:function(data){
			var array = eval(data);
			$('#formulario_ausencias #agrega-registros_ausencias').html(array[0]);
			$('#formulario_ausencias #pagination').html(array[1]);
		}
	});
	return false;
}

function reportePDF(agenda_id){
	window.open('<?php echo SERVERURL; ?>php/citas/tickets.php?agenda_id='+agenda_id);
}

function getPacientes_id(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getPacientes_id.php';
	var pacientes_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(valores){	
          pacientes_id = valores;	  
		}
	});
	return pacientes_id;		
}

function getColaboradorEdicion_id(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getColaborador_id.php';
	var colaborador_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'agenda_id='+agenda_id,
		success:function(valores){	
          colaborador_id = valores;	  
		}
	});
	return colaborador_id;		
}

function getServicio_id(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getServicio_id.php';
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
    var url = '<?php echo SERVERURL; ?>php/citas/getNewAgendaId.php';
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
	$("#ModalDelete_enviar").attr('disabled', true);		
	  $('#checkeliminar').on('click', function(){
		  if($('#checkeliminar:checked').val() == 1){
			  $("#ModalDelete_enviar").attr('disabled', false);  
		  }else{
			  $("#ModalDelete_enviar").attr('disabled', true); 
		  }			 
      });  
});


function eliminarRegistro(id){	
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	var url = '<?php echo SERVERURL; ?>php/citas/eliminar.php';

	$.ajax({
		type:'POST',
		url:url,
		data:'id='+id,
		success: function(registro){
			if(registro == 1){
			   pagination_ausencias(1);
			   $('#bs-regis').val("");	
				swal({
					title: "Success", 
					text: "Registro eliminado correctamente",
					icon: "success",
					timer: 3000, //timeOut for auto-close
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});			
				return false;				
			}else{
				$('#bs-regis').val("");	
				swal({
					title: "Error", 
					text: "No se puede elimiar este registro",
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

function clean_sobrecupo(){
   getJornada();
   getServiciosobrecupo();
   $('#formulario_sobrecupo #sobrecupo_unidad').html("");			  	   
   $('#formulario_sobrecupo #sobrecupo_medico').html("");			  
   $('#formulario_sobrecupo #pro_ausencias').val('Registro');   
}

function getJornada(){
    var url = '<?php echo SERVERURL; ?>php/citas/getJornada.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		success:function(data){		
		   $('#formulario_sobrecupo #jornada').html("");
		   $('#formulario_sobrecupo #jornada').html(data);
		   $('#formulario_sobrecupo #jornada').selectpicker('refresh');
		}
	});
	return false;		
}

function getServiciosobrecupo(){
    var url = '<?php echo SERVERURL; ?>php/citas/servicios.php';
		
	$.ajax({
	    type:'POST',
		url:url,
		async: true,
		success:function(data){					 			 
		 	  $('#formulario_sobrecupo #sobrecupo_servicio').html("");	
			  $('#formulario_sobrecupo #sobrecupo_servicio').html(data);	
			  $('#formulario_sobrecupo #sobrecupo_servicio').selectpicker('refresh');
		 }
	});
	return false;		
}

$(document).ready(function() {
	  $('#formulario_sobrecupo #sobrecupo_servicio').on('change', function(){
		var servicio_id = $('#formulario_sobrecupo #sobrecupo_servicio').val();
        var url = '<?php echo SERVERURL; ?>php/citas/getUnidad.php';	        
		
		$.ajax({
            type: "POST",
            url: url,
			async: true,
            data:'servicio='+servicio_id,
            success: function(data){
				$('#formulario_sobrecupo #sobrecupo_unidad').html(data);
				$('#formulario_sobrecupo #sobrecupo_unidad').selectpicker('refresh');
            }
         });
		 
      });					
});

/*VERIFICAR LA EXISTENCIA DEL USUARIO (PACIENTE)*/
$(document).ready(function(e) {
    $('#formulario_sobrecupo #sobrecupo_expediente').on('blur', function(){
	 if($('#formulario_sobrecupo #sobrecupo_expediente').val()!=""){
		var url = '<?php echo SERVERURL; ?>php/citas/buscar_expediente_consulta.php';
        var expediente = $('#formulario_sobrecupo #sobrecupo_expediente').val();
	    $.ajax({
		   type:'POST',
		   url:url,
		   data:'expediente='+expediente,
		   success:function(data){
			  var array = eval(data);
			  if (array[0] == "Error"){
					swal({
						title: "Error", 
						text: "Registro no encontrado",
						icon: "error", 
						dangerMode: true,
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
					});
					$('#formulario_sobrecupo #paciente').val("");
					clean_sobrecupo();
					$("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', true);
					return false;
			  }else if (array[0] == "Familiar"){
					swal({
						title: "Error", 
						text: "Registro no encontrado o puede  ser un familiar, por favor corregir",
						icon: "error", 
						dangerMode: true,
						closeOnEsc: false, // Desactiva el cierre con la tecla Esc
						closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
					});		  
					$('#paciente').val("");
					clean_sobrecupo();
					$("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', true);
					return false;				  
			  }else{
			     $('#formulario_sobrecupo #sobrecupo_nombre').val(array[0]);		  
				 clean_sobrecupo();
			  }		  			  
		  }
	  });
	  return false;		
	 }else{
		$('#formulario_sobrecupo')[0].reset();	
        $("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', true);		
	 }
	});
});

function getAgenda_id(expediente, fecha_cita, colaborador_id, servicio_id){
	var url = '<?php echo SERVERURL; ?>php/citas/getAgenda.php';
	var agenda;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		data:'expediente='+expediente+'&fecha_cita='+fecha_cita+'&colaborador_id='+colaborador_id+'&servicio_id='+servicio_id,
		success:function(data){	
          agenda = data;			  		  		  			  
		}
	});
	return agenda;
}

//VERIFICAR LA FECHA DE AUSENCIA DEL PROFESIONAL
$(document).ready(function(e) {
  $('#formulario_sobrecupo #sobrecupo_fecha_cita').on('change', function(){
     if(getFechaAusenciasSobreCupo($('#formulario_sobrecupo #sobrecupo_fecha_cita').val()) == 1){
		swal({
			title: "Error", 
			text: "El médico se encuentra asuente no se puede agendar una cita",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});	
		$("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', true);	
     }else{
		$("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', false);	
	 }
  });
});

function getFechaAusenciasSobreCupo(fecha){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaAusencias.php';
	var colaborador_id = $('#formulario_sobrecupo #sobrecupo_medico').val();
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

function getComentarioAusencia(fecha){
    var url = '<?php echo SERVERURL; ?>php/citas/getComentarioAusencias.php';
	var colaborador_id = $('#botones_citas #medico_general').val();
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

function getEdadConfig(){
    var url = '<?php echo SERVERURL; ?>php/citas/getEdadConfig.php';

	$.ajax({
	    type:'POST',
		url:url,
		success:function(data){	
          $('#formulario_config_edades #edad_devuelta').html("Valor por default: <strong>" + data + "</strong>");		  		  		  			  
		}
	});
}

function getFinSemana(fecha){
    var url = '<?php echo SERVERURL; ?>php/citas/getFinSemana.php';
	
	var valor = "";
	$.ajax({
	    type:'POST',
		url:url,
		data:'fecha='+fecha,
		async: false,
		success:function(data){	
          valor = data;			  		  		  			  
		}
	});
	return valor;	
}

function getStatusRepro(){
    var url = '<?php echo SERVERURL; ?>php/citas/getStatusID.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#mensaje_status #status_repro').html("");
			$('#mensaje_status #status_repro').html(data);
		}			
     });	
}

function getHoraConsulta(){
    var url = '<?php echo SERVERURL; ?>php/citas/getHoraConsulta.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#form-editevent #hora_nueva').html("");
			$('#form-editevent #hora_nueva').html(data);
			$('#form-editevent #hora_nueva').selectpicker('refresh');
		}			
     });	
}


function modificarStatus(){
	var status_id = $('#mensaje_status #status_repro').val();
	var agenda_id = $('#mensaje_status #mensaje_status_agenda_id').val();
	var comentario = $('#mensaje_status #mensaje_status_comentario').val();
	
	if(status_id == "" || status_id == null){
		status_id = 0;
	}else{
		status_id = $('#mensaje_status #status_repro').val();
	}
	
    var url = '<?php echo SERVERURL; ?>php/citas/addStatus.php';
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+agenda_id+'&status_id='+status_id+'&comentario='+comentario,
		success:function(data){	
          if(data == 1){
			swal({
				title: "Success", 
				text: "Estatus almacenado correctamente",
				icon: "success",
				timer: 3000, //timeOut for auto-close
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera				
			});
			$('#mensaje_status').modal('hide');
		  }else{    
			swal({
				title: "Error", 
				text: "Error, está acción no se puedo procesar",
				icon: "error", 
				dangerMode: true,
				closeOnEsc: false, // Desactiva el cierre con la tecla Esc
				closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
			});			  
		  }			  		  		  			  
		}
	});	
}

function getNombreUsuario(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getNombreUsuario.php';
	
	var valor = "";
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		success:function(data){	
          valor = data;			  		  		  			  
		}
	});
	return valor;	
}

function sendEmail(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/mail/correo_citas.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+agenda_id,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

function sendEmailCambioCita(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/mail/correo_cambio_citas.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+agenda_id,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

function sendEmailReprogramación(agenda_id){
    var url = '<?php echo SERVERURL; ?>php/mail/correo_reprogramaciones.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		data:'agenda_id='+agenda_id,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/citas/getServicio.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#botones_citas #servicio').html("");
			$('#botones_citas #servicio').html(data);
			$('#botones_citas #servicio').selectpicker('refresh');
		}			
     });	
}	

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#botones_citas #medico_general').html("");
			$('#botones_citas #medico_general').html(data);
			$('#botones_citas #medico_general').selectpicker('refresh');

		    $('#form-editevent #colaborador').html("");
			$('#form-editevent #colaborador').html(data);
			$('#form-editevent #colaborador').selectpicker('refresh');

		    $('#formulario_ausencias #medico_ausencia').html("");
			$('#formulario_ausencias #medico_ausencia').html(data);
			$('#formulario_ausencias #medico_ausencia').selectpicker('refresh');				
		}			
     });	
}	

//CONSULTAR INFORMACION DEL USUARIO
function consultarDepartamento(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarDepartamento.php';
	var departamento;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          departamento = data;			  		  		  			  
		}
	});
	return departamento;	
}

function consultarMunicipio(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarMunicipio.php';
	var municipio;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          municipio = data;			  		  		  			  
		}
	});
	return municipio;	
}

function consultarPais(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarPais.php';
	var pais;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          pais = data;			  		  		  			  
		}
	});
	return pais;	
}

function consultarEstadoCivil(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarEstadoCivil.php';
	var estado_civil;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          estado_civil = data;			  		  		  			  
		}
	});
	return estado_civil;	
}

function consultarRaza(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarRaza.php';
	var raza;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          raza = data;			  		  		  			  
		}
	});
	return raza;	
}

function consultarReligion(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarReligion.php';
	var religion;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          religion = data;			  		  		  			  
		}
	});
	return religion;	
}

function consultarProfesion(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarProfesion.php';
	var profesion;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          profesion = data;			  		  		  			  
		}
	});
	return profesion;	
}

function consultarEscolaridad(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarEscolaridad.php';
	var escolaridad;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          escolaridad = data;			  		  		  			  
		}
	});
	return escolaridad;	
}

function consultarLugarNacimiento(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarLugarNacimiento.php';
	var lugar_nacimiento;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          lugar_nacimiento = data;			  		  		  			  
		}
	});
	return lugar_nacimiento;	
}

function consultarResponsable(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarResponsable.php';
	var responsable;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          responsable = data;			  		  		  			  
		}
	});
	return responsable;	
}

function consultarParentesco(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarParentesco.php';
	var parentesco;
	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){	
          parentesco = data;			  		  		  			  
		}
	});
	return parentesco;	
}

//DEVUELVE EL PACIENTES_ID DEL USUARIO
function consultarExpediente(expediente){
    var url = '<?php echo SERVERURL; ?>php/citas/consultarExpediente.php';
	var pacientes_id;
		
	$.ajax({
	    type:'POST',
		url:url,
		data:'expediente='+expediente,
		async: false,
		success:function(data){	
          pacientes_id = data;			  		  		  			  
		}
	});
	return pacientes_id;		
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
//FIN CONSULTAR INFORMACION DEL USUARIO

function getFechaSistema(){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaSistema.php';
	var fecha_sistema;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          fecha_sistema = data;			  		  		  			  
		}
	});
	return fecha_sistema;	
}

function getProfesionalName(profesional){
    var url = '<?php echo SERVERURL; ?>php/citas/getProfesionalName.php';
	var profesional;
	$.ajax({
	    type:'POST',
		url:url,
		data:'profesional='+profesional,
		async: false,
		success:function(data){	
          profesional = data;			  		  		  			  
		}
	});
	return profesional;		
}


//INICIO DE BLOQUEQUEAR FECHA EN EL FORMULARIO DE SOBRECUPO
$(document).ready(function(e) {
  $('#formulario_sobrecupo #sobrecupo_fecha_cita').on('change', function(){	
  
	var fecha = $('#formulario_sobrecupo #sobrecupo_fecha_cita').val();
	var hoy = new Date();
    fecha_actual = convertDate(hoy);
	
	if(fecha<fecha_actual){
		swal({
			title: "Error", 
			text: "No se puede agregar un sobre cupo en esta fecha",
			icon: "error", 
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});	
		$("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', true);
	}else{	
        $("#formulario_sobrecupo #sobrecupo_agregar").attr('disabled', false);
	}
   });
});
//FIN DE BLOQUEQUEAR FECHA EN EL FORMULARIO DE SOBRECUPO
$('#form-editevent #buscar_profesional').on('click', function(e){
	listar_colaboradores_buscar();
	 $('#modal_busqueda_colaboradores').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

var listar_colaboradores_buscar = function(){
	var table_colaboradores_buscar = $("#dataTableColaboradores").DataTable({		
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
	table_colaboradores_buscar.search('').draw();
	$('#buscar').focus();
	
	view_colaboradores_busqueda_dataTable("#dataTableColaboradores tbody", table_colaboradores_buscar);
}

var view_colaboradores_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#form-editevent #colaborador').val(data.colaborador_id);
		$('#modal_busqueda_colaboradores').modal('hide');
	});
}
</script>