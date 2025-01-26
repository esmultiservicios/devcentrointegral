<script>
$(document).ready(function() {
    getSexo();
    getStatus();
    getResponsables();
    getDepartamentos();
    getPais();

    $('#form_main #bs_regis').on('keyup', function() {
        listar_pacientes();
    });

    $('#formulario_profesiones #profesionales_buscar').on('keyup', function() {
        paginationPorfesionales(1);
    });

    $('#form_main #estado').on('change', function() {
       listar_pacientes();
    });

    $('#formulario_agregar_expediente_manual #identidad_ususario_manual').on('keyup', function() {
        busquedaUsuarioManualIdentidad();
    });

    $('#formulario_agregar_expediente_manual #expediente_usuario_manual').on('keyup', function() {
        busquedaUsuarioManualExpediente();
    });
	
	listar_pacientes();
});

function addPacientes() {
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
		getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
		$('#formulario_pacientes #reg').show();
		$('#formulario_pacientes #edi').hide();
		cleanPacientes();
		getResponsables();
		$('#formulario_pacientes #grupo_expediente').hide();
		$('#formulario_pacientes')[0].reset();
		$('#formulario_pacientes #pro').val('Registro');
		$("#formulario_pacientes #fecha").attr('readonly', false);
		$('#formulario_pacientes #validate').removeClass('bien_email');
		$('#formulario_pacientes #validate').removeClass('error_email');
		$("#formulario_pacientes #correo").css("border-color", "none");
		$('#formulario_pacientes #validate').html('');
		$("#formulario_pacientes #identidad").attr('readonly', false);

		$('#formulario_pacientes').attr({
			'data-form': 'save'
		});
		$('#formulario_pacientes').attr({
			'action': '<?php echo SERVERURL; ?>php/pacientes/agregarPacientes.php'
		});
		$('#modal_pacientes').modal({
			show: true,
			keyboard: false,
			backdrop: 'static'
		});
		return false;
	} else {
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true
		});
	}
}

function addProfesion(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
		getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
		$('#formulario_profesiones #reg').show();
		$('#formulario_profesiones #edi').hide();
		$('#formulario_profesiones')[0].reset();
		$('#formulario_profesiones #proceso').val('Registro');
		paginationPorfesionales(1);
		$('#formulario_profesiones').attr({
			'data-form': 'save'
		});
		$('#formulario_profesiones').attr({
			'action': '<?php echo SERVERURL; ?>php/pacientes/agregar_profesional.php'
		});
		$('#modal_profesiones').modal({
			show: true,
			keyboard: false,
			backdrop: 'static'
		});
		return false;
	} else {
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true
		});
	}
}
	
function getDepartamentos() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getDepartamentos.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #departamento_id').html("");
            $('#formulario_pacientes #departamento_id').html(data);
            $('#formulario_pacientes #departamento_id').selectpicker('refresh');
        }
    });
}

function getMunicipio() {
    var url = '../php/pacientes/getMunicipio.php';

    var departamento_id = $('#formulario_pacientes #departamento_id').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'departamento_id=' + departamento_id,
        success: function(data) {
            $('#formulario_pacientes #municipio_id').html("");
            $('#formulario_pacientes #municipio_id').html(data);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');
        }
    });
}

$(document).ready(function() {
    $('#formulario_pacientes #departamento_id').on('change', function() {
        var url = '../php/pacientes/getMunicipio.php';

        var departamento_id = $('#formulario_pacientes #departamento_id').val();

        $.ajax({
            type: 'POST',
            url: url,
            data: 'departamento_id=' + departamento_id,
            success: function(data) {
                $('#formulario_pacientes #municipio_id').html("");
                $('#formulario_pacientes #municipio_id').html(data);
                $('#formulario_pacientes #municipio_id').selectpicker('refresh');
            }
        });
        return false;
    });
});

function getMunicipioEditar(departamento_id, municipio_id) {
    var url = '../php/pacientes/getMunicipio.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'departamento_id=' + departamento_id,
        success: function(data) {
            $('#formulario_pacientes #municipio_id').html("");
            $('#formulario_pacientes #municipio_id').html(data);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');

            $('#formulario_pacientes #municipio_id').val(municipio_id);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');
        }
    });
    return false;
}

function getPais() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getPais.php';
    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {

            $('#formulario_pacientes #pais_id').html("");
            $('#formulario_pacientes #pais_id').html(data);            
            $('#formulario_pacientes #pais_id').selectpicker('refresh');
        }
    });
}

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function() {
    $("#modal_pacientes").on('shown.bs.modal', function() {
        $(this).find('#formulario_pacientes #name').focus();
    });
});

$(document).ready(function() {
    $("#modal_profesiones").on('shown.bs.modal', function() {
        $(this).find('#formulario_profesiones #profesionales_buscar').focus();
    });
});

$(document).ready(function() {
    $("#agregar_expediente_manual").on('shown.bs.modal', function() {
        $(this).find('#formulario_agregar_expediente_manual #identidad_ususario_manual').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_pais").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_pais #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_departamentos").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_departamentos #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_municipios").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_municipios #buscar').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#reg_manual').on('click', function(
    e) { // delete event clicked // We don't want this to act as a link so cancel the link action
    e.preventDefault();
    if ($('#formulario_agregar_expediente_manual #expediente_usuario_manual').val() != "" || $(
            '#formulario_agregar_expediente_manual #identidad_ususario_manual').val() != "") {
        registrarExpedienteManual();
    } else {
        swal({
            title: "Error",
            text: "Hay registros en blanco, por favor corregir",
            icon: "error",
            dangerMode: true
        });
        return false;
    }
});

$('#convertir_manual').on('click', function(
    e) { // add event submit We don't want this to act as a link so cancel the link action
    e.preventDefault();
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
        getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
        convertirExpedientetoTemporal();
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            icon: 'error',
            dangerMode: true
        });
    }
});

function asignarExpedienteaRegistro(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/agregar_expediente.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        success: function(registro) {
            swal.close();
            showExpediente(pacientes_id);
            listar_pacientes();
            listar_pacientes();
            return false;
        }
    });
    return false;
}

function getStatus() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getStatus.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#form_main #estado').html("");
            $('#form_main #estado').html(data);
			$('#form_main #estado').selectpicker('refresh');
        }
    });
}

function getResponsables() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getResponsables.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #responsable_id').html("");
            $('#formulario_pacientes #responsable_id').html(data);
        }
    });
}

function showExpediente(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getExpediente.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Por favor intentelo de nuevo más tarde",
                    icon: "error",
                    dangerMode: true
                });
            } else {
                $('#mensaje_show').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                $('#mensaje_mensaje_show').html(data);
                $('#bad').hide();
                $('#okay').show();
            }
        }
    });
}

function modal_eliminarProfesional(profesional_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estás seguro?",
            text: "¿Desea eliminar este registro?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true,
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!"
                }
            },
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete === true) {
                eliminarProfesional(profesional_id);
            }
        });
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true
        });
    }
}

function modal_eliminar(pacientes_id) {
    if (consultarExpediente(pacientes_id) != 0 && (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 ||
            getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6)) {
        var nombre_usuario = consultarNombre(pacientes_id);
        var expediente_usuario = consultarExpediente(pacientes_id);
        var dato;

        if (expediente_usuario == 0) {
            dato = nombre_usuario;
        } else {
            dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
        }

        swal({
            title: "¿Estás seguro?",
            text: "¿Desea eliminar este registro: " + dato + "?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true,
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",

                }
            },
            dangerMode: false
        }).then((willDelete) => {
            if (willDelete === true) {
                eliminarRegistro(pacientes_id);
            }
        });
    } else if (consultarExpediente(pacientes_id) == 0 && (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 ||
            getUsuarioSistema() == 3 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6)) {
        var nombre_usuario = consultarNombre(pacientes_id);
        var expediente_usuario = consultarExpediente(pacientes_id);
        var dato;

        if (expediente_usuario == 0) {
            dato = nombre_usuario;
        } else {
            dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
        }

        swal({
            title: "¿Estás seguro?",
            text: "¿Desea eliminar este registro: " + dato + "?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true,
                    className: "btn-secondary"
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                    className: "btn-warning"
                }
            },
            dangerMode: true
        }).then((result) => {
            if (result === true) {
                eliminarRegistro(pacientes_id);
            }
        });
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            icon: 'error',
            dangerMode: true
        });
        return false;
    }
}

function cleanPacientes() {
    $('#formulario_pacientes #validate').removeClass('bien_email');
    $('#formulario_pacientes #validate').removeClass('error_email');
    $('#formulario_pacientes #validate').html('');
    $("#formulario #correo").css("border-color", "none");
}

function editarRegistro(pacientes_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 ||
        getUsuarioSistema() == 6) {
        var url = '<?php echo SERVERURL; ?>php/pacientes/editar.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: 'pacientes_id=' + pacientes_id,
            success: function(valores) {
                var datos = eval(valores);
                $('#formulario_pacientes #reg').hide();
                $('#formulario_pacientes #edi').show();
                $('#formulario_pacientes #pro').val('Edición');
                $('#formulario_pacientes #grupo_expediente').show();
                $('#formulario_pacientes #pacientes_id').val(pacientes_id);
                $('#formulario_pacientes #name').val(datos[0]);
                $('#formulario_pacientes #lastname').val(datos[1]);
                $('#formulario_pacientes #telefono1').val(datos[2]);
                $('#formulario_pacientes #telefono2').val(datos[3]);

                $('#formulario_pacientes #sexo').val(datos[4]);
                $('#formulario_pacientes #sexo').selectpicker('refresh');

                $('#formulario_pacientes #correo').val(datos[5]);
                $('#formulario_pacientes #edad').val(datos[6]);
                $('#formulario_pacientes #expediente').val(datos[7]);
                $('#formulario_pacientes #direccion').val(datos[8]);
                $('#formulario_pacientes #responsable').val(datos[9]);
                $('#formulario_pacientes #responsable_id').val(datos[10]);
                $('#formulario_pacientes #fecha_nac').val(datos[11]);
                $('#formulario_pacientes #identidad').val(datos[12]);

                $('#formulario_pacientes #departamento_id').val(datos[13]);
                $('#formulario_pacientes #departamento_id').selectpicker('refresh');

                $('#formulario_pacientes #municipio_id').val(datos[14]);
                $('#formulario_pacientes #municipio_id').selectpicker('refresh');

                getMunicipioEditar(datos[13], datos[14]);
                $("#formulario_pacientes #fecha").attr('readonly', true);
                $("#formulario_pacientes #expediente").attr('readonly', true);
                $("#formulario_pacientes #identidad").attr('readonly', true);
                $('#formulario_pacientes #validate').removeClass('bien_email');
                $('#formulario_pacientes #validate').removeClass('error_email');
                $("#formulario_pacientes #correo").css("border-color", "none");
                $('#formulario_pacientes #validate').html('');

                cleanPacientes();
                $('#formulario_pacientes').attr({
                    'data-form': 'update'
                });
                $('#formulario_pacientes').attr({
                    'action': '<?php echo SERVERURL; ?>php/pacientes/editarPacientes.php'
                });
                $('#modal_pacientes').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                return false;
            }
        });
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            icon: 'error',
            dangerMode: true
        });
        return false;
    }
}

function eliminarProfesional(id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/eliminar_profesional.php';
    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                paginationPorfesionales(1);
                $('#modal_profesiones').modal('hide');
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro, cuenta con información almacenada",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al completar el registro",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            }
        }
    });
    return false;
}

function eliminarRegistro(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/eliminar.php';
    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + pacientes_id,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                listar_pacientes();
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro, cuenta con información almacenada",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al completar el registro",
                    icon: "error",
                    dangerMode: true
                });
                return false;
            }
        }
    });
    return false;
}

function convertirExpedientetoTemporal() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/convertirExpedienteTemporal.php';
    var pacientes_id = $('#formulario_agregar_expediente_manual #pacientes_id').val();

    $.ajax({
        type: "POST",
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: true,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Usuario convertido",
                    text: "El usuario se ha convertido a temporal",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                });
                $('#agregar_expediente_manual').modal('hide');
                $('#formulario_agregar_expediente_manual #expediente_manual').val('TEMP');
                $('#formulario_agregar_expediente_manual #temporal').hide();
                $('#convertir_manual').hide();
                $('#reg_manual').show();
                listar_pacientes();
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "No se puede procesar su solicitud",
                    icon: "error",
                    confirmButtonClass: "btn-danger"
                });
                return false;
            }
        }
    });
}

function registrarExpedienteManual() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/agregarExpedienteManual.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: $('#formulario_agregar_expediente_manual').serialize(),
        success: function(registro) {
            if (registro == 1) {
                $('#formulario_agregar_expediente_manual #pro_manual').val('Registro');
                swal({
                    title: "Success",
                    text: "Registro completado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                $('#agregar_expediente_manual').modal('hide');
                listar_pacientes();
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se pudo guardar el registro, por favor verifique la información",
                    icon: "error",
                    dangerMode: true
                });
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Error al ejecutar esta acción",
                    icon: "error",
                    dangerMode: true
                });
            } else if (registro == 4) {
                swal({
                    title: "Error",
                    text: "Error en los datos",
                    icon: "error",
                    dangerMode: true
                });
            } else {
                swal({
                    title: "Error",
                    text: "Error al guardar el registro",
                    icon: "error",
                    dangerMode: true
                });
            }
        }
    });
    return false;
}

function busquedaUsuarioManualIdentidad() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarIdentidad.php';

    var identidad = $('#formulario_agregar_expediente_manual #identidad_ususario_manual').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'identidad=' + identidad,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Este numero de Identidad ya existe, por favor corriga el numero e intente nuevamente",
                    icon: "error",
                    confirmButtonClass: "btn-danger"
                });
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', true);
                return false;
            } else {
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', false);
            }
        }
    });
}

function busquedaUsuarioManualExpediente() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarExpediente.php';

    var expediente = $('#formulario_agregar_expediente_manual #expediente_usuario_manual').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'expediente=' + expediente,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Este numero de Expediente ya existe, por favor corriga el numero e intente nuevamente",
                    icon: "error",
                    confirmButtonClass: "btn-danger"
                });
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', true);
                return false;
            } else {
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', false);
            }
        }
    });
}

function consultarExpediente(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getExpedienteInformacion.php';
    var resp;

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: false,
        success: function(data) {
            resp = data;
        }
    });
    return resp;
}

function consultarNombre(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';
    var resp;

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: false,
        success: function(data) {
            resp = data;
        }
    });
    return resp;
}

function modal_agregar_expediente_manual(id, expediente) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 ||
        getUsuarioSistema() == 6) {
        $('#formulario_agregar_expediente_manual')[0].reset();
        var url = '<?php echo SERVERURL; ?>php/pacientes/buscarUsuario.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: 'id=' + id,
            success: function(valores) {
                var datos = eval(valores);
                if (expediente == 0) {
                    $("#formulario_agregar_expediente_manual #temporal").hide();
                } else {
                    $("#formulario_agregar_expediente_manual #temporal").show();
                }
                $("#formulario_agregar_expediente_manual #pacientes_id").val(id);
                $("#formulario_agregar_expediente_manual #expediente").val(expediente);
                $("#formulario_agregar_expediente_manual #name_manual").val(datos[0]);
                $("#formulario_agregar_expediente_manual #identidad_manual").val(datos[1]);

                $('#formulario_agregar_expediente_manual #sexo_manual').val(datos[2]);
                $('#formulario_agregar_expediente_manual #sexo_manual').selectpicker('refresh');

                $("#formulario_agregar_expediente_manual #fecha_manual").val(datos[3]);
                $("#formulario_agregar_expediente_manual #edad_manual").val(datos[6]);
                $("#formulario_agregar_expediente_manual #expediente_manual").val(datos[5]);
                $("#formulario_agregar_expediente_manual #edad_manual").show();
                $('#formulario_agregar_expediente_manual #pro').val('Registrar');
                $('#formulario_agregar_expediente_manual #mensaje').removeClass('error');
                $('#formulario_agregar_expediente_manual #mensaje').removeClass('bien');
                $('#formulario_agregar_expediente_manual #mensaje').removeClass('alerta');
                $('#formulario_agregar_expediente_manual #mensaje').html("");
                $("#reg_manual").show();
                $("#convertir_manual").hide();
                $('#agregar_expediente_manual').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                return false;
            }
        });
        return false;
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true
        });
    }
}

function modal_agregar_expediente(pacientes_id, expediente) {
    var nombre_usuario = consultarNombre(pacientes_id);
    var expediente_usuario = consultarExpediente(pacientes_id);
    var dato;

    if (expediente_usuario == 0) {
        dato = nombre_usuario;
    } else {
        dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
    }

    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5 ||
        getUsuarioSistema() == 6) {
        if (expediente == "" || expediente == 0) {
            swal({
                title: "¿Estás seguro?",
                text: "¿Desea asignarle un numero de expediente a este usuario: " + dato + "?",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancelar",
                        visible: true,
                    },
                    confirm: {
                        text: "¡Sí, asignar el expediente!",
                    }
                },
                dangerMode: false
            }).then((result) => {
                if (result === true) {
                    asignarExpedienteaRegistro(pacientes_id);
                }
            });
        } else {
            swal({
                title: "Error",
                text: "Este usuario: " + dato + " ya tiene un expediente asignado",
                icon: "error",
                dangerMode: true
            });
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true
        });
        return false;
    }
}

function paginationPorfesionales(partida) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/paginarProfesionales.php';
    var profesional = $('#formulario_profesiones #profesionales_buscar').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&profesional=' + profesional,
        success: function(data) {
            var array = eval(data);
            $('#agrega_registros_profesionales').html(array[0]);
            $('#pagination_profesionales').html(array[1]);
        }
    });
    return false;
}

function getSexo() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getSexo.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #sexo').html("");
            $('#formulario_pacientes #sexo').html(data);
            $('#formulario_pacientes #sexo').selectpicker('refresh');

            $('#formulario_agregar_expediente_manual #sexo_manual').html("");
            $('#formulario_agregar_expediente_manual #sexo_manual').html(data);
            $('#formulario_agregar_expediente_manual #sexo_manual').selectpicker('refresh');
        }
    });
}

/*INICIO AUTO COMPLETAR*/
/*INICIO SUGGESTION NOMBRE*/
$(document).ready(function() {
    $('#formulario #name').on('keyup', function() {
        if ($('#formulario #name').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_name').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #name').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_name').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_name').fadeIn(1000).html("");
            $('#formulario #suggestions_name').fadeOut(1000);
        }
    });
});

//OCULTAR EL SUGGESTION
$(document).ready(function() {
    $('#formulario #name').on('blur', function() {
        $('#formulario #suggestions_name').fadeOut(1000);
    });
});

$(document).ready(function() {
    $('#formulario #name').on('click', function() {
        if ($('#formulario #name').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_name').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #name').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_name').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_name').fadeIn(1000).html("");
            $('#formulario #suggestions_name').fadeOut(1000);
        }
    });
});
/*FIN SUGGESTION NOMBRE*/

/*INICIO SUGGESTION APELLIDO*/
$(document).ready(function() {
    $('#formulario #lastname').on('keyup', function() {
        if ($('#formulario #lastname').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_apellido').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #lastname').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_apellido').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_apellido').fadeIn(1000).html("");
            $('#formulario #suggestions_apellido').fadeOut(1000);
        }
    });
});

//OCULTAR EL SUGGESTION
$(document).ready(function() {
    $('#formulario #lastname').on('blur', function() {
        $('#formulario #suggestions_apellido').fadeOut(1000);
    });
});

$(document).ready(function() {
    $('#formulario #lastname').on('cli', function() {
        if ($('#formulario #lastname').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_apellido').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #lastname').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_apellido').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_apellido').fadeIn(1000).html("");
            $('#formulario #suggestions_apellido').fadeOut(1000);
        }
    });
});
/*FIN SUGGESTION APELLIDO*/
/*FIN AUTO COMPLETAR*/

function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-');
}

//SÍ
$(document).ready(function() {
    $('#formulario_agregar_expediente_manual #respuestasi').on('click', function() {
        $("#convertir_manual").show();
        $("#reg_manual").hide();
    });
});

//NO
$(document).ready(function() {
    $('#formulario_agregar_expediente_manual #respuestano').on('click', function() {
        $("#convertir_manual").hide();
        $("#reg_manual").show();
    });
});

$('#form_main #limpiar').on('click', function(e) {
    e.preventDefault();
    $('#form_main #bs_regis').val("");
    $('#form_main #bs_regis').focus();
    getSexo();
    listar_pacientes();
    getStatus();
});

var listar_pacientes = function(){
	var estado = "";
    var paciente = "";

    if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
        estado = 1;
    } else {
        estado = $('#form_main #estado').val();
    }

    if ($('#form_main #tipo').val() == "" || $('#form_main #tipo').val() == null) {
        paciente = 1;
    } else {
        paciente = $('#form_main #tipo').val();
    }
	
	var table_pacientes  = $("#dataTablePacientesMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/pacientes/llenarDataTablePacientes.php",
            "data": function(d) {
                d.estado = estado;
                d.paciente = paciente;
            }		
		},		
		"columns":[
			{"data": "paciente"},
			{
				"data": "expediente",
				"render": function(data, type, row) {
					return '<a href="#" class="showExpedienteLink">' + data + '</a>';
				}
			},
			{"data": "identidad"},
			{"data": "edad"},			
			{"data": "telefono1"},
			{"data": "identidad"},
			{"data": "localidad"},			
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item showExpediente" href="#"><i class="fas fa-eye fa-lg"></i> Información del Paciente</a>' +
							'<a class="dropdown-item addExpediente" href="#"><i class="fas fa-plus fa-lg"></i> Agregar Expediente</a>' +
							'<a class="dropdown-item addIdentidad" href="#"><i class="fas fa-edit fa-lg"></i> Editar Identidad Paciente</a>' +
							'<a class="dropdown-item editar" href="#"><i class="fas fa-user-edit fa-lg"></i> Editar Paciente</a>' +
							'<a class="dropdown-item delete" href="#"><i class="fas fa-trash fa-lg"></i> Eliminar Paciente</a>' +
						'</div>' +
					'</div>'
			}
		],		
        "lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,			
		"buttons":[		
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Pacientes',
				className: 'btn btn-info',
				action: 	function(){
					listar_pacientes();
				}
			},		
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Crear Pacientes',
				titleAttr: 'Agregar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					addPacientes();
				}
			},	
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Crear Profesion',
				titleAttr: 'Agregar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					addProfesion();
				}
			},		
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				title: 'Reporte Pacientes',
				className: 'btn btn-success',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },				
			},
			{
				extend: 'pdf',
				orientation: 'landscape',
				text: '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				title: 'Reporte Pacientes',
				className: 'btn btn-danger',
				exportOptions: {
					modifier: {
						page: 'current' // Solo exporta las filas visibles en la página actual
					},
					columns: [0, 1, 2, 3, 4, 5, 6] // Define las columnas a exportar
				},
				customize: function(doc) {
					// Asegúrate de que `imagen` contenga la cadena base64 de la imagen
					doc.content.splice(1, 0, {
						margin: [0, 0, 0, 12],
						alignment: 'left',
						image: imagen, // Usando la variable que ya tiene la imagen base64
						width: 170, // Ajusta el tamaño si es necesario
						height: 45 // Ajusta el tamaño si es necesario
					});
				}
			},
			{
				extend: 'print',
				text: '<i class="fas fa-print fa-lg"></i> Imprimir',  // Correcta colocación del icono
				titleAttr: 'Imprimir',
				title: 'Reporte Pacientes',
				className: 'btn btn-secondary',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                },
			}
		]		
	});	 
	table_pacientes.search('').draw();
	$('#buscar').focus();
	
	show_expediente_link_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	show_expediente_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	add_expediente_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	add_identidad_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	edit_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
	delete_paciente_dataTable("#dataTablePacientesMain tbody", table_pacientes);
}

var show_expediente_link_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.showExpedienteLink");
	$(tbody).on("click", "a.showExpedienteLink", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		showExpediente(data.pacientes_id);
	});
}

var show_expediente_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.showExpediente");
	$(tbody).on("click", "a.showExpediente", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		showExpediente(data.pacientes_id);
	});
}

var add_expediente_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.addExpediente");
	$(tbody).on("click", "a.addExpediente", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_agregar_expediente(data.pacientes_id, data.expediente_);
	});
}

var add_identidad_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.addIdentidad");
	$(tbody).on("click", "a.addIdentidad", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_agregar_expediente_manual(data.pacientes_id);		
	});
}

var edit_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.editar");
	$(tbody).on("click", "a.editar", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		editarRegistro(data.pacientes_id);	
	});
}

var delete_paciente_dataTable = function(tbody, table){
	$(tbody).off("click", "a.delete");
	$(tbody).on("click", "a.delete", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
	
		modal_eliminar(data.pacientes_id);

	});
}
</script>