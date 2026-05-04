<script>
/****************************************************************************************************************************************************************
	SECUENCIA DE FACTURACIÓN - CAMI
****************************************************************************************************************************************************************/

$(function() {

	funciones();

	$("#modalEliminarSecuenciaFacturacion").on('shown.bs.modal', function() {
		$(this).find('#comentario').focus();
	});

	$("#secuenciaFacturacion").on('shown.bs.modal', function() {
		if ($('#formularioSecuenciaFacturacion #cai').prop('readonly')) {
			$('#formularioSecuenciaFacturacion #fecha_limite').focus();
		} else {
			$('#formularioSecuenciaFacturacion #cai').focus();
		}
	});

	$('#form_main #nuevo_registro').off('click').on('click', function(e) {
		e.preventDefault();

		if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {

			prepararModalNuevoRegistro();

			getEmpresa('', function() {
				getDocumentos('', function() {
					$('#secuenciaFacturacion').modal({
						show: true,
						keyboard: false,
						backdrop: 'static'
					});
				});
			});

			return false;

		} else {
			swal({
				title: "Acceso Denegado",
				text: "No tiene permisos para ejecutar esta acción",
				icon: "error",
				dangerMode: true,
				closeOnEsc: false,
				closeOnClickOutside: false
			});
		}
	});

	$('#reg').off('click').on('click', function(e) {
		e.preventDefault();

		if (validarFormularioRegistro()) {
			agregar();
		} else {
			mostrarErrorCamposVacios();
		}
	});

	$('#edi').off('click').on('click', function(e) {
		e.preventDefault();

		if (validarFormularioEdicion()) {
			agregarRegistro();
		} else {
			mostrarErrorCamposVacios();
		}
	});

	$('#form_main #bs_regis').off('keyup').on('keyup', function() {
		pagination(1);
	});

	$('#form_main #empresa').off('change').on('change', function() {
		pagination(1);
	});

	$('#form_main #estado').off('change').on('change', function() {
		pagination(1);
	});

});

/****************************************************************************************************************************************************************
	PREPARAR MODALES
****************************************************************************************************************************************************************/

function prepararModalNuevoRegistro() {

	$('#formularioSecuenciaFacturacion')[0].reset();

	$('#formularioSecuenciaFacturacion #pro').val("Registro");
	$('#formularioSecuenciaFacturacion #secuencia_facturacion_id').val('');

	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('checked', false);

	$('#reg').show();
	$('#edi').hide();
	$('#delete').hide();

	habilitarCamposSecuenciaSinFacturas();

	$('#alertaSecuenciaFacturas').addClass('d-none');
}

function prepararModalEdicion() {

	$('#formularioSecuenciaFacturacion')[0].reset();

	$('#formularioSecuenciaFacturacion #pro').val("Edición");

	$('#reg').hide();
	$('#edi').show();
	$('#delete').hide();

	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('checked', false);

	habilitarCamposSecuenciaSinFacturas();

	$('#alertaSecuenciaFacturas').addClass('d-none');

	$("#edi").attr('disabled', false);
}

/****************************************************************************************************************************************************************
	VALIDACIONES
****************************************************************************************************************************************************************/

function validarFormularioRegistro() {

	if ($('#formularioSecuenciaFacturacion #empresa').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #documento_id').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #relleno').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #incremento').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #siguiente').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #rango_inicial').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #rango_final').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #fecha_activacion').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #fecha_limite').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion input[name="estado"]:checked').val() == undefined) return false;

	return true;
}

function validarFormularioEdicion() {

	if ($('#formularioSecuenciaFacturacion #secuencia_facturacion_id').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion #fecha_limite').val() == "") return false;
	if ($('#formularioSecuenciaFacturacion input[name="estado"]:checked').val() == undefined) return false;

	return true;
}

function mostrarErrorCamposVacios() {
	swal({
		title: "Error",
		text: "Hay registros en blanco, por favor corregir",
		icon: "error",
		dangerMode: true,
		closeOnEsc: false,
		closeOnClickOutside: false
	});
}

/****************************************************************************************************************************************************************
	GUARDAR NUEVA SECUENCIA
****************************************************************************************************************************************************************/

function agregar() {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/agregar.php';

	$.ajax({
		type: 'POST',
		url: url,
		data: $('#formularioSecuenciaFacturacion').serialize(),
		success: function(response) {

			let registro = response.trim();

			if (registro == "1") {

				$('#formularioSecuenciaFacturacion')[0].reset();

				swal({
					title: "Success",
					text: "Registro almacenado correctamente",
					icon: "success",
					timer: 3000,
					closeOnEsc: false,
					closeOnClickOutside: false
				});

				$('#secuenciaFacturacion').modal('hide');

				pagination(1);
				getEmpresa();

			} else if (registro == "2") {

				swal({
					title: "Error",
					text: "No se pudo guardar el registro",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false,
					closeOnClickOutside: false
				});

			} else if (registro == "3") {

				swal({
					title: "Error",
					text: "Ya existe una secuencia activa para este tipo de documento en esta empresa",
					icon: "warning",
					closeOnEsc: false,
					closeOnClickOutside: false
				});

			} else {

				console.error("Respuesta inesperada:", registro);

				swal({
					title: "Error",
					text: "Error inesperado del servidor",
					icon: "error",
					dangerMode: true
				});
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal({
				title: "Error",
				text: "Error de conexión con el servidor",
				icon: "error"
			});
		}
	});

	return false;
}

/****************************************************************************************************************************************************************
	EDITAR SECUENCIA
****************************************************************************************************************************************************************/

function editarRegistro(secuencia_facturacion_id) {

	if (getUsuarioSistema() != 1 && getUsuarioSistema() != 2) {

		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true,
			closeOnEsc: false,
			closeOnClickOutside: false
		});

		return false;
	}

	prepararModalEdicion();

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/editar.php';

	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: {
			secuencia_facturacion_id: secuencia_facturacion_id
		},
		success: function(response) {

			if (!response.ok) {
				swal({
					title: "Error",
					text: response.mensaje || "No se pudo cargar la información de la secuencia",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false,
					closeOnClickOutside: false
				});

				return false;
			}

			var data = response.data;

			$('#formularioSecuenciaFacturacion #secuencia_facturacion_id').val(data.secuencia_facturacion_id);
			$('#formularioSecuenciaFacturacion #cai').val(data.cai);
			$('#formularioSecuenciaFacturacion #prefijo').val(data.prefijo);
			$('#formularioSecuenciaFacturacion #relleno').val(data.relleno);
			$('#formularioSecuenciaFacturacion #incremento').val(data.incremento);
			$('#formularioSecuenciaFacturacion #siguiente').val(data.siguiente);
			$('#formularioSecuenciaFacturacion #rango_inicial').val(data.rango_inicial);
			$('#formularioSecuenciaFacturacion #rango_final').val(data.rango_final);
			$('#formularioSecuenciaFacturacion #fecha_activacion').val(data.fecha_activacion);
			$('#formularioSecuenciaFacturacion #fecha_limite').val(data.fecha_limite);

			$('#formularioSecuenciaFacturacion input[name="estado"]').prop('checked', false);
			$('#formularioSecuenciaFacturacion input[name="estado"][value="' + data.activo + '"]').prop('checked', true);

			if ($('#formularioSecuenciaFacturacion #comentario').length > 0) {
				$('#formularioSecuenciaFacturacion #comentario').val(data.comentario);
			}

			getEmpresa(data.empresa_id, function() {

				getDocumentos(data.documento_id, function() {

					$('#formularioSecuenciaFacturacion #empresa').selectpicker('val', String(data.empresa_id));
					$('#formularioSecuenciaFacturacion #documento_id').selectpicker('val', String(data.documento_id));

					$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');
					$('#formularioSecuenciaFacturacion #documento_id').selectpicker('refresh');

					verificarBloqueoSecuencia(data.secuencia_facturacion_id, function() {

						$('#formularioSecuenciaFacturacion #empresa').selectpicker('val', String(data.empresa_id));
						$('#formularioSecuenciaFacturacion #documento_id').selectpicker('val', String(data.documento_id));

						$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');
						$('#formularioSecuenciaFacturacion #documento_id').selectpicker('refresh');

						$('#secuenciaFacturacion').modal({
							show: true,
							keyboard: false,
							backdrop: 'static'
						});

					});

				});

			});

			return false;
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal({
				title: "Error",
				text: "No se pudo cargar la información de la secuencia",
				icon: "error",
				dangerMode: true,
				closeOnEsc: false,
				closeOnClickOutside: false
			});
		}
	});

	return false;
}

/****************************************************************************************************************************************************************
CARGA DE EMPRESA Y DOCUMENTOS
****************************************************************************************************************************************************************/

function getEmpresa(empresaSeleccionada, callback) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/getEmpresa.php';

	$.ajax({
		type: "POST",
		url: url,
		async: true,
		success: function(data) {

			$('#form_main #empresa').html(data);
			$('#form_main #empresa').selectpicker('refresh');

			var selectModal = $('#formularioSecuenciaFacturacion #empresa');

			selectModal.empty();
			selectModal.html(data);
			selectModal.selectpicker('refresh');

			if (empresaSeleccionada !== undefined && empresaSeleccionada !== null && empresaSeleccionada !== '') {
				selectModal.selectpicker('val', String(empresaSeleccionada));
				selectModal.val(String(empresaSeleccionada));
			}

			selectModal.selectpicker('refresh');

			if (typeof callback === 'function') {
				callback();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			if (typeof callback === 'function') {
				callback();
			}
		}
	});
}

function getDocumentos(documentoSeleccionado, callback) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/getDocumentos.php';

	$.ajax({
		type: 'GET',
		url: url,
		dataType: 'json',
		success: function(response) {

			var select = $('#formularioSecuenciaFacturacion #documento_id');

			select.empty();
			select.append('<option value="">Seleccione documento</option>');

			if (response.error) {

				console.error(response.mensaje);

				swal({
					title: "Error",
					text: "No se pudieron cargar los tipos de documento",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false,
					closeOnClickOutside: false
				});

				select.selectpicker('refresh');

				if (typeof callback === 'function') {
					callback();
				}

				return false;
			}

			$.each(response, function(index, item) {
				select.append(
					'<option value="' + String(item.documento_id) + '">' + item.nombre + '</option>'
				);
			});

			select.selectpicker('refresh');

			if (documentoSeleccionado !== undefined && documentoSeleccionado !== null && documentoSeleccionado !== '') {
				select.selectpicker('val', String(documentoSeleccionado));
				select.val(String(documentoSeleccionado));
			}

			select.selectpicker('refresh');

			if (typeof callback === 'function') {
				callback();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal({
				title: "Error",
				text: "Error al cargar los tipos de documento",
				icon: "error",
				dangerMode: true,
				closeOnEsc: false,
				closeOnClickOutside: false
			});

			if (typeof callback === 'function') {
				callback();
			}
		}
	});
}

/****************************************************************************************************************************************************************
PAGINACIÓN
****************************************************************************************************************************************************************/

function pagination(partida) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/paginar.php';

	var dato = '';
	var empresa = 0;
	var estado = 1;

	if ($('#form_main #empresa').val() == "" || $('#form_main #empresa').val() == null) {
		empresa = 0;
	} else {
		empresa = $('#form_main #empresa').val();
	}

	if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
		estado = 1;
	} else {
		estado = $('#form_main #estado').val();
	}

	if ($('#form_main #bs_regis').val() == "" || $('#form_main #bs_regis').val() == null) {
		dato = '';
	} else {
		dato = $('#form_main #bs_regis').val();
	}

	$.ajax({
		type: 'POST',
		url: url,
		async: true,
		dataType: 'json',
		data: {
			partida: partida,
			dato: dato,
			empresa: empresa,
			estado: estado
		},
		success: function(array) {

			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);

			if ($('[data-toggle="tooltip"]').length) {
				$('[data-toggle="tooltip"]').tooltip();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			$('#agrega-registros').html(
				'<div class="alert alert-danger">No se pudo cargar la información de secuencias.</div>'
			);

			$('#pagination').html('');
		}
	});

	return false;
}

/****************************************************************************************************************************************************************
	BLOQUEO DE CAMPOS SI YA EXISTEN FACTURAS
****************************************************************************************************************************************************************/

function verificarBloqueoSecuencia(secuencia_facturacion_id, callback) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/verificar_facturas.php';

	$.ajax({
		type: 'POST',
		url: url,
		data: {
			secuencia_facturacion_id: secuencia_facturacion_id
		},
		success: function(response) {

			var resultado = response.trim();

			if (resultado == "1") {
				bloquearCamposSecuenciaConFacturas();
			} else {
				habilitarCamposSecuenciaSinFacturas();
			}

			if (typeof callback === 'function') {
				callback();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			bloquearCamposSecuenciaConFacturas();

			if (typeof callback === 'function') {
				callback();
			}

			swal({
				title: "Aviso",
				text: "No se pudo verificar si la secuencia tiene facturas. Por seguridad, se bloquearon los campos principales.",
				icon: "warning"
			});
		}
	});
}

function bloquearCamposSecuenciaConFacturas() {

	$('#alertaSecuenciaFacturas').removeClass('d-none');

	$('#formularioSecuenciaFacturacion #empresa').prop('disabled', true);
	$('#formularioSecuenciaFacturacion #documento_id').prop('disabled', true);

	$('#formularioSecuenciaFacturacion #cai').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #prefijo').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #relleno').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #incremento').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #siguiente').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #rango_inicial').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #rango_final').prop('readonly', true);
	$('#formularioSecuenciaFacturacion #fecha_activacion').prop('readonly', true);

	$('#formularioSecuenciaFacturacion #fecha_limite').prop('readonly', false);
	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('disabled', false);

	$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');
	$('#formularioSecuenciaFacturacion #documento_id').selectpicker('refresh');

	$('.campo-controlado').addClass('campo-bloqueado');
}

function habilitarCamposSecuenciaSinFacturas() {

	$('#alertaSecuenciaFacturas').addClass('d-none');

	$('#formularioSecuenciaFacturacion #empresa').prop('disabled', false);
	$('#formularioSecuenciaFacturacion #documento_id').prop('disabled', false);

	$('#formularioSecuenciaFacturacion #cai').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #prefijo').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #relleno').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #incremento').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #siguiente').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #rango_inicial').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #rango_final').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #fecha_activacion').prop('readonly', false);
	$('#formularioSecuenciaFacturacion #fecha_limite').prop('readonly', false);
	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('disabled', false);

	$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');
	$('#formularioSecuenciaFacturacion #documento_id').selectpicker('refresh');

	$('.campo-controlado').removeClass('campo-bloqueado');
}

/****************************************************************************************************************************************************************
	MODIFICAR SECUENCIA
****************************************************************************************************************************************************************/

function agregarRegistro() {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/agregarRegistro.php';

	var empresaDisabled = $('#formularioSecuenciaFacturacion #empresa').prop('disabled');
	var documentoDisabled = $('#formularioSecuenciaFacturacion #documento_id').prop('disabled');
	var estadoDisabled = $('#formularioSecuenciaFacturacion input[name="estado"]').prop('disabled');

	$('#formularioSecuenciaFacturacion #empresa').prop('disabled', false);
	$('#formularioSecuenciaFacturacion #documento_id').prop('disabled', false);
	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('disabled', false);

	var datosFormulario = $('#formularioSecuenciaFacturacion').serialize();

	$('#formularioSecuenciaFacturacion #empresa').prop('disabled', empresaDisabled);
	$('#formularioSecuenciaFacturacion #documento_id').prop('disabled', documentoDisabled);
	$('#formularioSecuenciaFacturacion input[name="estado"]').prop('disabled', estadoDisabled);

	$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');
	$('#formularioSecuenciaFacturacion #documento_id').selectpicker('refresh');

	$.ajax({
		type: 'POST',
		url: url,
		data: datosFormulario,
		success: function(response) {

			let registro = response.trim();

			if (registro == "1") {

				swal({
					title: "Success",
					text: "Registro modificado correctamente",
					icon: "success",
					timer: 3000,
					closeOnEsc: false,
					closeOnClickOutside: false
				});

				$('#secuenciaFacturacion').modal('hide');

				pagination(1);

			} else if (registro == "2") {

				swal({
					title: "Error",
					text: "No se pudo modificar el registro",
					icon: "error",
					dangerMode: true
				});

			} else if (registro == "3") {

				swal({
					title: "Error",
					text: "Ya existe otra secuencia activa para este tipo de documento",
					icon: "warning"
				});

			} else if (registro == "4") {

				swal({
					title: "Error",
					text: "El número siguiente ya fue utilizado en facturación",
					icon: "error"
				});

			} else {

				console.error("Respuesta inesperada:", registro);

				swal({
					title: "Error",
					text: "Error inesperado del servidor",
					icon: "error"
				});
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal({
				title: "Error",
				text: "Error de conexión con el servidor",
				icon: "error"
			});
		}
	});

	return false;
}

/****************************************************************************************************************************************************************
	ELIMINAR SECUENCIA
****************************************************************************************************************************************************************/

function modal_eliminar(secuencia_facturacion_id) {

	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {

		swal({
			title: "¿Eliminar registro?",
			text: "Escribe un comentario:",
			content: "input",
			icon: "warning",
			buttons: ["Cancelar", "Eliminar"],
			dangerMode: true
		}).then((comentario) => {

			if (comentario === null) {
				return;
			}

			if (comentario.trim() === "") {
				swal("Error", "El comentario no puede quedar vacío", "error");
				return;
			}

			eliminarRegistroDirecto(secuencia_facturacion_id, comentario);
		});

	} else {

		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true,
			closeOnEsc: false,
			closeOnClickOutside: false
		});
	}
}

function eliminarRegistroDirecto(id, comentario) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/eliminar.php';

	$.ajax({
		type: 'POST',
		url: url,
		data: {
			secuencia_facturacion_id: id,
			comentario: comentario
		},
		success: function(response) {

			let registro = response.trim();

			if (registro == "1") {

				swal("Success", "Registro eliminado correctamente", "success");

				pagination(1);
				getEmpresa();

			} else if (registro == "2") {

				swal("Error", "No se pudo eliminar el registro", "error");

			} else if (registro == "3") {

				swal("Error", "No se puede eliminar: ya existen facturas asociadas", "warning");

			} else {

				console.error("Respuesta inesperada:", registro);

				swal("Error", "Error inesperado del servidor", "error");
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal("Error", "Error de conexión con el servidor", "error");
		}
	});
}

/****************************************************************************************************************************************************************
	CARGA DE EMPRESA Y DOCUMENTOS
****************************************************************************************************************************************************************/

function getEmpresa(empresaSeleccionada, callback) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/getEmpresa.php';

	$.ajax({
		type: "POST",
		url: url,
		async: true,
		success: function(data) {

			$('#form_main #empresa').html(data);
			$('#form_main #empresa').selectpicker('refresh');

			$('#formularioSecuenciaFacturacion #empresa').html(data);

			if (empresaSeleccionada !== undefined && empresaSeleccionada !== null && empresaSeleccionada !== '') {
				$('#formularioSecuenciaFacturacion #empresa').val(String(empresaSeleccionada));
			}

			$('#formularioSecuenciaFacturacion #empresa').selectpicker('refresh');

			if (typeof callback === 'function') {
				callback();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			if (typeof callback === 'function') {
				callback();
			}
		}
	});
}

function getDocumentos(documentoSeleccionado, callback) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/getDocumentos.php';

	$.ajax({
		type: 'GET',
		url: url,
		dataType: 'json',
		success: function(response) {

			var select = $('#formularioSecuenciaFacturacion #documento_id');

			select.empty();
			select.append('<option value="">Seleccione documento</option>');

			if (response.error) {

				console.error(response.mensaje);

				swal({
					title: "Error",
					text: "No se pudieron cargar los tipos de documento",
					icon: "error"
				});

				select.selectpicker('refresh');

				if (typeof callback === 'function') {
					callback();
				}

				return false;
			}

			$.each(response, function(index, item) {
				select.append(
					'<option value="' + item.documento_id + '">' + item.nombre + '</option>'
				);
			});

			if (documentoSeleccionado !== undefined && documentoSeleccionado !== null && documentoSeleccionado !== '') {
				select.val(String(documentoSeleccionado));
			}

			select.selectpicker('refresh');

			if (typeof callback === 'function') {
				callback();
			}
		},
		error: function(xhr) {

			console.error(xhr.responseText);

			swal({
				title: "Error",
				text: "Error al cargar los tipos de documento",
				icon: "error"
			});

			if (typeof callback === 'function') {
				callback();
			}
		}
	});
}

/****************************************************************************************************************************************************************
	PAGINACIÓN
****************************************************************************************************************************************************************/

function pagination(partida) {

	var url = '<?php echo SERVERURL; ?>php/secuencia_facturacion/paginar.php';

	var dato = '';
	var empresa = 0;
	var estado = 1;

	if ($('#form_main #empresa').val() == "" || $('#form_main #empresa').val() == null) {
		empresa = 0;
	} else {
		empresa = $('#form_main #empresa').val();
	}

	if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
		estado = 1;
	} else {
		estado = $('#form_main #estado').val();
	}

	if ($('#form_main #bs_regis').val() == "" || $('#form_main #bs_regis').val() == null) {
		dato = '';
	} else {
		dato = $('#form_main #bs_regis').val();
	}

	$.ajax({
		type: 'POST',
		url: url,
		async: true,
		data: {
			partida: partida,
			dato: dato,
			empresa: empresa,
			estado: estado
		},
		success: function(data) {

			var array = eval(data);

			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);
		}
	});

	return false;
}

/****************************************************************************************************************************************************************
	FUNCIONES GENERALES
****************************************************************************************************************************************************************/

function funciones() {
	pagination(1);
	getEmpresa();
}
</script>