<script>
$(document).ready(function() {
	  $('#form_main #nuevo_registro').on('click',function(e){
	   e.preventDefault();
	   if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		$('#reg_preclinica').show();
		$('#edit_preclinica').hide();		 	 
	    $('#formulario_agregar_preclinica')[0].reset();	
		$('#formulario_agregar_preclinica #pro').val('Registro');	
		$('#formulario_agregar_preclinica #mensaje').removeClass('error');
		$('#formulario_agregar_preclinica #mensaje').removeClass('bien');
		$('#formulario_agregar_preclinica #mensaje').html("");	
        $("#formulario_agregar_preclinica #expediente").attr('readonly', false);
        $('#formulario_agregar_preclinica #fecha').val($('#form_main #fecha_i').val());
        $("#formulario_agregar_preclinica #reg_preclinica").attr('disabled', true);
		$('#formulario_agregar_preclinica #visita').prop('checked', false); //DESELECCIONA UN CHECK BOX
		$("#formulario_agregar_preclinica #fecha").attr('readonly', false);	
        $('#formulario_agregar_preclinica #grupo').show();	
		$('#formulario_agregar_preclinica #group_alta').show();
		$('#formulario_agregar_preclinica #grupo_profesional_consulta').hide();
		$('#formulario_agregar_preclinica #visita').show();
        limpiarFormulario();	

		$('#formulario_agregar_preclinica').attr({ 'data-form': 'save' }); 
		$('#formulario_agregar_preclinica').attr({ 'action': '<?php echo SERVERURL; ?>php/preclinica/agregarPreclinica.php' });		
	     $('#agregar_preclinica').modal({
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
	   
    //BUSQUEDA	
	$('#form_main #servicio').on('change',function(){	
	     $('#form_main #colaborador').html("");	
	     pagination(1);
    });	
	
	$('#form_main #colaborador').on('change',function(){     	    
		pagination(1);
    });		
	
	$('#form_main #bs-regis').on('keyup',function(){
	   pagination(1);
    });

    $('#form_main #fecha_i').on('change',function(){
	   pagination(1);
    });	
	
    $('#form_main #fecha_f').on('change',function(){
	   pagination(1);
    });		

    limpiarFormulario();
});

$('#form_ausencia #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
	e.preventDefault();
	if($('#form_ausencia #motivo_ausencia').val() != ""){
		eliminarRegistro(); 
	}else{
		swal({
			title: "Error", 
			text: "El comentario no puede quedar en blanco",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});	
		return false;
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

$(document).ready(function(){
    pagination(1);
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#agregar_preclinica").on('shown.bs.modal', function(){
        $(this).find('#formulario_agregar_preclinica #expediente').focus();
    });
});

$(document).ready(function(){
    $("#eliminar").on('shown.bs.modal', function(){
        $(this).find('#form_ausencia #motivo_ausencia').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$(document).ready(function(e) {
    pagination(1);		 					
	limpiarFormularioMain();
	getProfesionales();
});

/*VERIFICAR LA EXISTENCIA DEL USUARIO (PACIENTE)*/
$(document).ready(function(e) {
    $('#formulario_agregar_preclinica #expediente').on('blur', function(){
	 if($('#formulario_agregar_preclinica #expediente').val()!=""){
		var url = '<?php echo SERVERURL; ?>php/preclinica/buscar_expediente.php';
        var expediente = $('#formulario_agregar_preclinica #expediente').val();
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
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
				  $("#reg_preclinica").attr('disabled', true);
				 return false;
			  }else if (array[0] == "Error1"){		  
					swal({
						title: "Error", 
						text: "Este es un usuario temporal, no se puede agregar la preclínica, o simplemente el usuario no existe",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	

				  $("#reg_preclinica").attr('disabled', true)				  
				 return false;
			  }else if (array[0] == "Familiar"){		  
					swal({
						title: "Error", 
						text: "Este usuario es un familiar, solo se permite buscar usuarios, por favor verificar con el departamento de Admisión, para más detalles",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	

				  $("#reg_preclinica").attr('disabled', true);				  
				 return false;
			  }else{
				 $('#formulario_agregar_preclinica #pro').val('Registro');
			     $('#formulario_agregar_preclinica #identidad').val(array[0]);
                 $('#formulario_agregar_preclinica #nombre').val(array[1]);			  			 	
                 $("#reg_preclinica").attr('disabled', false);			  
			  }		  			  
		  }
	  });
	  return false;		
	 }else{ 
		$('#formulario_agregar_preclinica')[0].reset();	
        $("#reg_preclinica").attr('disabled', true);
        $('#formulario_agregar_preclinica #pro').val('Registro');		
	 }
	});
});

function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/preclinica/paginar.php';
	var dato = '';
	var unidad = '';
	var fechai = $('#form_main #fecha_i').val();
	var fechaf = $('#form_main #fecha_f').val();	
	var colaborador = '';
	
	if($('#form_main #unidad').val() == "" || $('#form_main #unidad').val() == null){
		unidad = "";
	}else{
		unidad = $('#form_main #unidad').val();
	}
	
	if($('#form_main #colaborador').val() == "" || $('#form_main #colaborador').val() == null){
		colaborador = '';
	}else{
		colaborador = $('#form_main #colaborador').val();
	}
	
	if($('#form_main #bs-regis').val() == "" || $('#form_main #bs-regis').val() == null){
		dato = '';
	}else{
		dato = $('#form_main #bs-regis').val();
	}
	
	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&dato='+dato+'&unidad='+unidad+'&fechai='+fechai+'&fechaf='+fechaf+'&colaborador='+colaborador,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}

//FORMULARIO PRINCIPAL
function getServicioFormMain(){
    var url = '<?php echo SERVERURL; ?>php/preclinica/servicios.php';		
		
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

$(document).ready(function() {
	  $('#form_main #servicio').on('change', function(){
		var servicio_id = $('#form_main #servicio').val();
        var url = '<?php echo SERVERURL; ?>php/preclinica/getUnidad.php';		
		
		$.ajax({
            type: "POST",
            url: url,
			async: true,
            data:'servicio='+servicio_id,
            success: function(data){
				$('#form_main #unidad').html("");
				$('#form_main #unidad').html(data);				
            }
         });
		 
      });					
});

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

//METODOS
//Limpiar formulario
function limpiarFormularioMain(){	
	$('#form_main #colaborador').html("");	
	$('#form_main #unidad').html("");	
	getServicioFormMain();
}

function limpiarFormulario(){
	getServicio();	
	getColaborador();	
}

//Consultar Servicio
function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/preclinica/servicios.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_agregar_preclinica #servicio').html("");
			$('#formulario_agregar_preclinica #servicio').html(data);					
        }
     });		
}

//Llenar unidad al seleccionar el servicio
$(document).ready(function() {
	  $('#formulario_agregar_preclinica #servicio').on('change', function(){
		var servicio_id = $('#formulario_agregar_preclinica #servicio').val();
        var url = '<?php echo SERVERURL; ?>php/preclinica/getUnidad.php';		
		
		$.ajax({
            type: "POST",
            url: url,
			async: true,
            data:'servicio='+servicio_id,
            success: function(data){
				$('#formulario_agregar_preclinica #unidad').html(data);					
            }
         });
		 
      });					
});

$(document).ready(function() {
	  $('#formulario_agregar_preclinica #unidad').on('change', function(){
		var servicio_id = $('#formulario_agregar_preclinica #servicio').val();
		var puesto_id = $('#formulario_agregar_preclinica #unidad').val();
        var url = '<?php echo SERVERURL; ?>php/preclinica/getMedico.php';		
		
		$.ajax({
            type: "POST",
            url: url,
			async: true,
            data:'servicio='+servicio_id+'&puesto_id='+puesto_id,
            success: function(data){
				$('#formulario_agregar_preclinica #medico').html(data);				 				
            }
         });
		 
      });					
});

$(document).ready(function() {
	  $('#formulario_agregar_preclinica #medico').on('change', function(){
		   if($('#formulario_agregar_preclinica #medico').val()){
			   $("#reg_preclinica").attr('disabled', false);
		   }
      });					
});		  
//Llenar el profesional al seleccionar la unidad

/*************************************************/
//FORMULARIOS
function editarRegistro(agenda_id, expediente){
 if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
  if(expediente != 0){	
	var url = '<?php echo SERVERURL; ?>php/preclinica/editar.php';		
	
	$.ajax({
		type:'POST',
		url:url,
		data:'id='+agenda_id,
		success: function(valores){
			var datos = eval(valores);
			$('#formulario_agregar_preclinica')[0].reset();
			$('#reg_preclinica').hide();
			$('#edit_preclinica').show();
			$('#formulario_agregar_preclinica #grupo').hide();
			$('#formulario_agregar_preclinica #grupo_profesional_consulta').show();
		    $('#formulario_agregar_preclinica #mensaje').removeClass('error');
		    $('#formulario_agregar_preclinica #mensaje').removeClass('bien');
		    $('#formulario_agregar_preclinica #mensaje').html("");					
			$('#formulario_agregar_preclinica #pro').val('Registro');
			$('#formulario_agregar_preclinica #nombre').val(datos[0]);
            $('#formulario_agregar_preclinica #identidad').val(datos[1]);
            $('#formulario_agregar_preclinica #expediente').val(datos[2]);
			$('#formulario_agregar_preclinica #profesional_consulta').val(datos[3]);
			$('#formulario_agregar_preclinica #fecha').val(datos[4]);
			$("#formulario_agregar_preclinica #expediente").attr('readonly', true);	
			$("#formulario_agregar_preclinica #fecha").attr('readonly', true);				
			$('#formulario_agregar_preclinica #id-registro').val(agenda_id);
			$('#formulario_agregar_preclinica #visita').hide();	
		    $('#formulario_agregar_preclinica #group_alta').hide();
			$('#formulario_agregar_preclinica #visita').html('');
			
			$('#formulario_agregar_preclinica').attr({ 'data-form': 'save' }); 
			$('#formulario_agregar_preclinica').attr({ 'action': '<?php echo SERVERURL; ?>php/preclinica/agregarPreclinicaporUsuario.php' });
			
	     	$('#agregar_preclinica').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
			});
			return false;
		}
	});	
  }else{
	swal({
		title: "Error", 
		text: "Este es un expediente temporal, no se puede almacenar",
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

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function nosePresntoRegistro(id, pacientes_id){
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
		  text: "¿Desea remover este usuario: " + dato + " que no se presento a su cita?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",
		  confirmButtonText: "¡Sí, remover el usuario!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			eliminarRegistro(id, inputValue);
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

function eliminarRegistro(id, comentario){
    var hoy = new Date();
    fecha_actual = convertDate(hoy);
	
	var url = '<?php echo SERVERURL; ?>php/agenda_pacientes/usuario_no_presento.php';
	var fecha = $('#form_main #fecha_i').val();	
	
    if(getMes(fecha)==2){
		swal({
			title: "Acceso Denegado", 
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
		  data:'agenda_id='+id+'&fecha='+fecha+'&comentario='+comentario,
		  success: function(registro){
			  if(registro == 1){
				swal({
					title: "Success", 
					text: "Registro removido correctamente",
					type: "success",
					timer: 3000, //timeOut for auto-close
				});	
                pagination(1);				
			  }else if(registro == 3){
				swal({
					title: "Error", 
					text: "Este registro ya tiene almacenada una ausencia",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});				  
		
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
					text: "Error al mover el registro",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});				  
			  }
  		  }
	   });
	   return false;		
	}else{
		swal({
			title: "Error", 
			text: "No se puede ejecutar esta acción fuera de esta fecha",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});
	}
  }
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

$(document).ready(function() {
	setInterval('pagination(1)',22000); //CADA 8 SEGUNDOS
});

function getMes(fecha){
    var url = '<?php echo SERVERURL; ?>php/preclinica/getMes.php';
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

function evaluarRegistrosPendientes(){
    var url = '<?php echo SERVERURL; ?>php/preclinica/evaluarPendientes.php';
	var string = '';
	
	var fecha = $('#form_main #fecha_i').val();

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
					text: "Se le recuerda que tiene " + datos[0] + " " + string + " de hacer su Preclínica en este mes de " + datos[1] + ". Debe revisar sus registros pendientes para todos los servicios.", 
					type: 'warning', 
					confirmButtonClass: 'btn-warning'
			  });				  
		   }
		}
	});	
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

function getIdentidad(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/getIdentidad.php';
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

function evaluarRegistrosPendientesEmailPreclinica(){
    var url = '<?php echo SERVERURL; ?>php/mail/evaluarPendientes_preclinica.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_agregar_preclinica #medico').html("");
			$('#formulario_agregar_preclinica #medico').html(data);		
        }
     });		
}

$('#formulario_agregar_preclinica #buscar_servicios_preclinica').on('click', function(e){
	listar_servicios_preclinica_buscar();
	 $('#modal_busqueda_servicios').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

$('#formulario_agregar_preclinica #buscar_profesionales_preclinica').on('click', function(e){
	listar_colaboradores_preclinica_buscar();
	 $('#modal_busqueda_colaboradores').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});	 
});

var listar_colaboradores_preclinica_buscar = function(){
	var table_colaboradores_preclinica_buscar = $("#dataTableColaboradores").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/citas/getMedicoTable.php"
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
	table_colaboradores_preclinica_buscar.search('').draw();
	$('#buscar').focus();
	
	view_colaboradores_preclinica_busqueda_dataTable("#dataTableColaboradores tbody", table_colaboradores_preclinica_buscar);
}

var view_colaboradores_preclinica_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();	
		$('#formulario_agregar_preclinica #medico').val(data.colaborador_id);
		$('#modal_busqueda_colaboradores').modal('hide');
	});
}

var listar_servicios_preclinica_buscar = function(){
	var table_servicios_preclinica_buscar = $("#dataTableServicios").DataTable({		
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
	table_servicios_preclinica_buscar.search('').draw();
	$('#buscar').focus();
	
	view_servicios_preclinica_busqueda_dataTable("#dataTableServicios tbody", table_servicios_preclinica_buscar);
}

var view_servicios_preclinica_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		$('#formulario_agregar_preclinica #servicio').val(data.servicio_id);
		$('#modal_busqueda_servicios').modal('hide');
	});
}
</script>