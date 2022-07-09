<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#modalAdministradorSecuencias").on('shown.bs.modal', function(){
        $(this).find('#formularioAdministradorSecuencias #empresa').focus();
    });
});

$(document).ready(function(){
    $("#modal_tablas_db").on('shown.bs.modal', function(){
        $(this).find('#formulario_tablas_db #buscar').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	//LLAMADA A LAS FUNCIONES
	funciones();	
	
	//INICIO ABRIR VENTANA MODAL PARA EL REGISTRO DE DESCUENTOS
	$('#form_main #nuevo_registro').on('click',function(e){
		e.preventDefault();
		funciones();
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){	
		    $('#formularioAdministradorSecuencias')[0].reset();
            limpiarSeciencia();	
            //HABILITAR CONTROLES PARA SOLO LECTURA
			$("#formularioAdministradorSecuencias #empresa").attr('disabled', false);
			$("#formularioAdministradorSecuencias #entidad").attr('disabled', false);				
			$("#formularioAdministradorSecuencias #prefijo").attr('readonly', false);
			$("#formularioAdministradorSecuencias #sufijo").attr('readonly', false);
			$("#formularioAdministradorSecuencias #relleno").attr('readonly', false);				
			$("#formularioAdministradorSecuencias #incremento").attr('readonly', false);
			$("#formularioAdministradorSecuencias #siguiente").attr('readonly', false);
			$("#formularioAdministradorSecuencias #estado").attr('disabled', false);
			$("#formularioAdministradorSecuencias #comentario").attr('readonly', false);	
			getEstado();
				
			 $('#reg').show();
			 $('#edi').hide(); 
			 $('#delete').hide(); 			 
			 $('#formularioAdministradorSecuencias #group_comentario').hide();
			 
			 $('#formularioAdministradorSecuencias').attr({ 'data-form': 'save' }); 
			 $('#formularioAdministradorSecuencias').attr({ 'action': '<?php echo SERVERURL; ?>php/administrador_secuencias/agregarAdministradorSecuencias.php' });
			 
			 $('#modalAdministradorSecuencias').modal({
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
	
    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
	$('#form_main #bs_regis').on('keyup',function(){
	  pagination(1);
	}); 

	$('#form_main #servicio').on('change',function(){
	  pagination(1);
	});
	
	$('#form_main #estado').on('change',function(){
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

function editarRegistro(secuencias_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){	
		$('#formularioAdministradorSecuencias')[0].reset();		
		var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/editar.php';

			$.ajax({
			type:'POST',
			url:url,
			data:'secuencias_id='+secuencias_id,
			success: function(valores){
				var array = eval(valores);
				$('#reg').hide();
				$('#edi').show();
				$('#delete').hide(); 			 
				$('#formularioAdministradorSecuencias #pro').val('Edición');
                $('#formularioAdministradorSecuencias #secuencias_id').val(secuencias_id);
				$('#formularioAdministradorSecuencias #empresa').val(array[0]);	
                $('#formularioAdministradorSecuencias #tabla').val(array[1]);				
                $('#formularioAdministradorSecuencias #entidad').val(array[1]);
                $('#formularioAdministradorSecuencias #prefijo').val(array[2]);
				$('#formularioAdministradorSecuencias #sufijo').val(array[3]);	
                $('#formularioAdministradorSecuencias #relleno').val(array[4]);
				$('#formularioAdministradorSecuencias #incremento').val(array[5]);	
                $('#formularioAdministradorSecuencias #siguiente').val(array[6]);
                $('#formularioAdministradorSecuencias #estado').val(array[7]);
				$('#formularioAdministradorSecuencias #comentario').val(array[8]);

				if(valores[7] == 1){
					$('#formularioAdministradorSecuencias #estado').prop('checked', true);
				}else{
					$('#formularioAdministradorSecuencias #estado').prop('checked', false);					
				}
					
				$("#edi").attr('disabled', false);	

				//HABILITAR OBJETOS
				$("#formularioAdministradorSecuencias #prefijo").attr('readonly', false);
				$("#formularioAdministradorSecuencias #sufijo").attr('readonly', false);
				$("#formularioAdministradorSecuencias #relleno").attr('readonly', false);				
				$("#formularioAdministradorSecuencias #incremento").attr('readonly', false);
				$("#formularioAdministradorSecuencias #siguiente").attr('readonly', false);				
				$("#formularioAdministradorSecuencias #estado").attr('disabled', false);
				$("#formularioAdministradorSecuencias #comentario").attr('readonly', false);				
				
                //DESHABILITAR CONTROLES PARA SOLO LECTURA
				$("#formularioAdministradorSecuencias #empresa").attr('disabled', true);
				$("#formularioAdministradorSecuencias #entidad").attr('disabled', true);
				
				$('#formularioAdministradorSecuencias #group_comentario').hide();
								
				$('#formularioAdministradorSecuencias').attr({ 'data-form': 'update' }); 
				$('#formularioAdministradorSecuencias').attr({ 'action': '<?php echo SERVERURL; ?>php/administrador_secuencias/modificarAdministadorSecuencias.php' });
			 
				$('#modalAdministradorSecuencias').modal({
					show:true,
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

function modal_eliminar(secuencias_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2){	
		$('#formularioAdministradorSecuencias')[0].reset();		
		var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/editar.php';

			$.ajax({
			type:'POST',
			url:url,
			data:'secuencias_id='+secuencias_id,
			success: function(valores){
				var array = eval(valores);
				$('#delete').show();
				$('#formularioAdministradorSecuencias #pro').val('Desactivar');
                $('#formularioAdministradorSecuencias #secuencias_id').val(secuencias_id);				
				$('#formularioAdministradorSecuencias #empresa').val(array[0]);	
                $('#formularioAdministradorSecuencias #tabla').val(array[1]);
                $('#formularioAdministradorSecuencias #entidad').val(array[1]);
                $('#formularioAdministradorSecuencias #prefijo').val(array[2]);
				$('#formularioAdministradorSecuencias #sufijo').val(array[3]);	
                $('#formularioAdministradorSecuencias #relleno').val(array[4]);
				$('#formularioAdministradorSecuencias #incremento').val(array[5]);	
                $('#formularioAdministradorSecuencias #siguiente').val(array[6]);
                $('#formularioAdministradorSecuencias #estado').val(array[7]);
				$('#formularioAdministradorSecuencias #comentario').val(array[8]);			
				$("#edi").attr('disabled', false);	

				if(valores[7] == 1){
					$('#formularioAdministradorSecuencias #estado').prop('checked', true);
				}else{
					$('#formularioAdministradorSecuencias #estado').prop('checked', false);					
				}
				
                //DESHABILITAR CONTROLES PARA SOLO LECTURA
				$("#formularioAdministradorSecuencias #empresa").attr('disabled', true);
				$("#formularioAdministradorSecuencias #entidad").attr('disabled', true);				
				$("#formularioAdministradorSecuencias #prefijo").attr('readonly', true);
				$("#formularioAdministradorSecuencias #sufijo").attr('readonly', true);
				$("#formularioAdministradorSecuencias #relleno").attr('readonly', true);				
				$("#formularioAdministradorSecuencias #incremento").attr('readonly', true);
				$("#formularioAdministradorSecuencias #siguiente").attr('readonly', true);
				$("#formularioAdministradorSecuencias #estado").attr('disabled', true);
				$("#formularioAdministradorSecuencias #comentario").attr('readonly', true);		
				
				$('#reg').hide();
				$('#edi').hide();
				$('#delete').show();
				$('#formularioAdministradorSecuencias #group_comentario').show();				
				
				$('#formularioAdministradorSecuencias').attr({ 'data-form': 'delete' }); 
				$('#formularioAdministradorSecuencias').attr({ 'action': '<?php echo SERVERURL; ?>php/administrador_secuencias/eliminarAdministradorSecuencias.php' });
				
				$('#modalAdministradorSecuencias').modal({
					show:true,
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

function limpiarSeciencia(){
   	$('#formularioAdministradorSecuencias #pro').val("Registro");
}

//INICIO FUNCION PARA OBTENER LOS COLABORADORES	
function funciones(){
    pagination(1);
    getEstado();
    getEmpresa();
	getTablas();
}

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/paginar.php';
	var dato = '';
	var profesional = '';
	
    if($('#form_main #empresa').val() == "" || $('#form_main #empresa').val() == null){
		empresa = 0;
	}else{
		empresa = $('#form_main #empresa').val();
	}
	
    if($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null){
		estado = 1;
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
		data:'partida='+partida+'&dato='+dato+'&empresa='+empresa+'&estado='+estado,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE REGISTROS

//INICIO FUNCION PARA OBTENER LA EMPRESA
function getEmpresa(){
    var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/getEmpresa.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #empresa').html("");
			$('#form_main #empresa').html(data);

		    $('#formularioAdministradorSecuencias #empresa').html("");
			$('#formularioAdministradorSecuencias #empresa').html(data);			
        }
     });		
}

function getTablas(){
    var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/getTablasDB.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){			
		    $('#formularioAdministradorSecuencias #entidad').html("");
			$('#formularioAdministradorSecuencias #entidad').html(data);			
        }
     });		
}
//FIN FUNCION PARA OBTENER LA EMPRESA	

//INICIO FUNCION PARA OBTENER EL ESTADO
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/administrador_secuencias/getEstado.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main #estado').html("");
			$('#form_main #estado').html(data);	

		    $('#formularioAdministradorSecuencias #estado').html("");
			$('#formularioAdministradorSecuencias #estado').html(data);				
        }
     });		
}
//FIN FUNCION PARA OBTENER EL ESTADO
//FIN FUNCIONES
/***************************************************************************************************************************************************************************/

/***************************************************************************************************************************************************************************/
$('#formularioAdministradorSecuencias #buscar_tabla_db').on('click', function(e){
	listar_tablas_db_buscar();
	$('#modal_tablas_db').modal({
		show:true,
		keyboard: false,
		backdrop:'static'
	});		 
});

var listar_tablas_db_buscar = function(){
	var table_tablas_db_buscar = $("#dataTableTablas").DataTable({		
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url":"<?php echo SERVERURL; ?>php/administrador_secuencias/getTablesDataTable.php"
		},
		"columns":[
			{"defaultContent":"<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"},
			{"data":"tabla"}	
		],
		"pageLength" : 5,
        "lengthMenu": lengthMenu,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,	
	});	 
	table_tablas_db_buscar.search('').draw();
	$('#buscar').focus();
	
	view_tablas_db_busqueda_dataTable("#dataTableTablas tbody", table_tablas_db_buscar);
}

var view_tablas_db_busqueda_dataTable = function(tbody, table){
	$(tbody).off("click", "button.view");		
	$(tbody).on("click", "button.view", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();		  
		$('#formularioAdministradorSecuencias #tabla').val(data.tabla);
		$('#formularioAdministradorSecuencias #entidad').val(data.tabla);		
		$('#modal_tablas_db').modal('hide');
	});
}
</script>