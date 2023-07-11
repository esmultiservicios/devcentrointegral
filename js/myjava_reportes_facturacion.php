<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#eliminar").on('shown.bs.modal', function(){
        $(this).find('#form_eliminar #motivo').focus();
    });
});

$(document).ready(function(){
    $("#cobros").on('shown.bs.modal', function(){
        $(this).find('#formCobros #comentario').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	//LLAMADA A LAS FUNCIONES
	funciones();

	//INICIO ABRIR VENTANA MODAL PARA EL REGISTRO DE LAS FACTURAS
	$('#form_main_facturacion_reportes #factura').on('click',function(e){
		e.preventDefault();
		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 4){
			if($('#form_main_facturacion_reportes #profesional').val() == "" || $('#form_main_facturacion_reportes #profesional').val() == null){
				profesional = getColaboradorConsultaID();
			}else{
				profesional = $('#form_main_facturacion_reportes #profesional').val();
			}

            $('#formCobros')[0].reset();
			$("#formCobros #generar").attr('disabled', false);
            $('#formCobros #colaborador_id').val(profesional);
			$('#formCobros #fechai').val($('#form_main_facturacion_reportes #fecha_b').val());
			$('#formCobros #fechaf').val($('#form_main_facturacion_reportes #fecha_f').val());
            $('#formCobros #profesional').val(getColaboradorNombre(profesional));
			$('#formCobros #pro').val("Registro");
		    $('#cobros').modal({
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
	        return false;
          }
	});
	//FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE LAS FACTURAS

	//INICIO PARA EL REGISTRO DE COBROS A PROFESIONALES
	$('#formCobros #generar').on('click', function(e){
		 if ($('#formCobros #comentario').val() != ""){
			e.preventDefault();
			agregarCobros();
			return false;
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
	//FIN PARA EL REGISTRO DE COBROS A PROFESIONALES

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
  $('#form_main_facturacion_reportes #estado').on('change',function(){
    pagination(1);
  });

  $('#form_main_facturacion_reportes #clientes').on('change',function(){
    pagination(1);
  });

  $('#form_main_facturacion_reportes #bs_regis').on('keyup',function(){
    pagination(1);
  });

  $('#form_main_facturacion_reportes #fecha_b').on('change',function(){
    pagination(1);
  });

  $('#form_main_facturacion_reportes #fecha_f').on('change',function(){
    pagination(1);
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});
//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

$('#form_eliminar #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){
	e.preventDefault();
	if($('#form_eliminar #motivo').val() != ""){
		rollback();
	}else{
		swal({
			title: "Error",
			text: "Hay registros en blanco, por favor corregir",
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

//INICIO AGRUPAR FUNCIONES DE PACIENTES
function funciones(){
	getEstado();
  getClientes();
  pagination(1);
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

//INICIO OBTENER COLABORADOR CONSULTA
function getColaboradorConsulta(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){
		  var datos = eval(data);
          colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN OBTENER COLABORADOR CONSULTA

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaboradorConsultaID(){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		success: function(valores){
			var datos = eval(valores);
			colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN FUNCION PARA OBTENER LOS COLABORADORES

//FUNCTION PARA OBTENER EL NOMBRE DEL COLABORADOR
function getColaboradorNombre(colaborador_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getColaboradorNombre.php';
    var colaborador_nombre = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		data:'colaborador_id='+colaborador_id,
		success: function(valores){
			colaborador_nombre = valores;
		}
	});
	return colaborador_nombre;
}
//FIN PARA OBTENER EL NOMBRE DEL COLABORADOR

//INICIO PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL
function agregarCobros(){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/agregarCargos.php';

	$.ajax({
		type:'POST',
		url:url,
		data:$('#formCobros').serialize(),
		success: function(registro){
			if(registro == 1){
				swal({
					title: "Success",
					text: "Valores generados correctamente",
					type: "success",
				});
				$('#formCobros #comentario').val("");
				$("#formCobros #generar").attr('disabled', true);
				pagination(1);
				return false;
			}else if(registro == 2){
				swal({
					title: "Error",
					text: "Error, no se puedieron generar los valores, por favor corregir",
					type: "error",
					confirmButtonClass: 'btn-danger'
				});
				return false;
			}else if(registro == 3){
				swal({
					title: "Error",
					text: "Error, este registro ya existe",
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
//FIN PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL

//INICIO PAGINACION DE REGISTROS
function pagination(partida){
  var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/paginar.php';

  var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
  var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();
  var dato =  $('#form_main_facturacion_reportes #bs_regis').val()
  var clientes = $('#form_main_facturacion_reportes #clientes').val()
  var estado = '';

  if($('#form_main_facturacion_reportes #estado').val() == ""){
    estado = 1;
  }else{
    estado = $('#form_main_facturacion_reportes #estado').val();
  }

	$.ajax({
		type:'POST',
		url:url,
		async: true,
		data:'partida='+partida+'&fechai='+fechai+'&fechaf='+fechaf+'&dato='+dato+'&clientes='+clientes+'&estado='+estado,
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});
	return false;
}
//FIN PAGINACION DE REGISTROS

$('#form_main_facturacion_reportes #reporte').on('click', function(e){
    e.preventDefault();
    reporteEXCEL();
});

//INICIO REPORTE DE FACTURACION
function reporteEXCEL(){
	var colaborador = '';
	var desde = $('#form_main_facturacion_reportes #fecha_b').val();
	var hasta = $('#form_main_facturacion_reportes #fecha_f').val();
	var url = '';
	var estado = 1;

    if($('#form_main_facturacion_reportes #profesional').val() == "" || $('#form_main_facturacion_reportes #profesional').val() == null){
		colaborador = '';
	}else{
		colaborador = $('#form_main_facturacion_reportes #profesional').val();
	}

    if($('#form_main_facturacion_reportes #estado').val() == "" || $('#form_main_facturacion_reportes #estado').val() == null){
		estado = 1;
	}else{
		estado = $('#form_main_facturacion_reportes #estado').val();
	}

	url = '<?php echo SERVERURL; ?>php/reporte_facturacion/reporte.php?desde='+desde+'&hasta='+hasta+'&colaborador='+colaborador+'&estado='+estado,

	window.open(url);
}
//FIN REPORTE DE FACTURACION

//INICIO DETALLES DE FACTURA
function invoicesDetails(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/detallesFactura.php';

	$.ajax({
		type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		success:function(data){
		   $('#mensaje_show').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
		   });
		   $('#mensaje_mensaje_show').html(data);
		   $('#bad').hide();
		   $('#okay').show();
		}
	});
}
//FIN DETALLES DE FACTURA

//INICIO ROLLBACK
function modal_rollback(facturas_id, pacientes_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){
		swal({
		  title: "¿Esta seguro?",
		  text: "¿Desea cancelar la factura para este registro: Paciente: " + consultarNombre(pacientes_id) + ". Factura N°:  " + getNumeroFactura(facturas_id) + "?",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  inputPlaceholder: "Comentario",
		  cancelButtonText: "Cancelar",
		  confirmButtonText: "¡Sí, cancelar la factura!",
		  confirmButtonClass: "btn-warning"
		}, function (inputValue) {
		  if (inputValue === false) return false;
		  if (inputValue === "") {
			swal.showInputError("¡Necesita escribir algo!");
			return false
		  }
			rollback(facturas_id, inputValue);
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
		return false;
	}
}

function rollback(facturas_id,comentario){
	var fecha = getFechaFactura(facturas_id);
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/rollback.php';

	if ( fecha <= fecha_actual){
	   $.ajax({
		  type:'POST',
		  url:url,
		  data:'facturas_id='+facturas_id+'&comentario='+comentario,
		  success: function(registro){
			  if(registro == 1){
			    pagination(1);
				swal({
					title: "Success",
					text: "Factura cancelada correctamente",
					type: "success",
				});
			    return false;
			  }else if(registro == 2){
				swal({
					title: "Error",
					text: "Error al cancelar la factura",
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
	}else{
		swal({
			title: "Error",
			text: "No se puede ejecutar esta acción fuera de esta fecha",
			type: "error",
			confirmButtonClass: 'btn-danger'
		});
	}
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

function getNumeroFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getNumeroFactura.php';
	var resp;

	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
          resp = data;
		}
	});
	return resp;
}
//INICIO ROLLBACK

//INICIO GET FECHA FACTURA
function getFechaFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getFechaFactura.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
		  var datos = eval(data);
		  fecha = datos[0];
		}
	});

	return fecha;
}
//FIN GET FECHA FACTURA

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

function printBill(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/facturacion/generaFactura.php?facturas_id='+facturas_id;
    window.open(url);
}
/******************************************************************************************************************************************************************************/
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getEstado.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_facturacion_reportes #estado').html("");
			  $('#form_main_facturacion_reportes #estado').html(data);
        $('#form_main_facturacion_reportes #estado').selectpicker('refresh');
        }
     });
}

function getClientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
				type: "POST",
				url: url,
				success: function(data){
					$('#form_main_facturacion_reportes #clientes').html("");
					$('#form_main_facturacion_reportes #clientes').html(data);
					$('#form_main_facturacion_reportes #clientes').selectpicker('refresh');
				}
     });
}
</script>
