/*
############################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################
*/
$('.FormularioAjax').submit(function(e){
    e.preventDefault();
	
    var form=$(this);

    var tipo=form.attr('data-form');
    var action=form.attr('action');
    var method=form.attr('method');
    var respuesta=form.children('.RespuestaAjax');
	
    var msjError="<script></script>";
    var formdata = new FormData(this);

    var textoAlerta;
	var type;
	var classButtom;

    if(tipo=="save"){
        textoAlerta="Los datos que enviaras quedaran almacenados en el sistema";
		type = "info";
		classButtom = "btn-primary";		
    }else if(tipo=="delete"){
        textoAlerta="Los datos serán eliminados completamente del sistema";
		type = "warning";
		classButtom = "btn-warning";		
    }else if(tipo=="update"){
        textoAlerta="Los datos del sistema serán actualizados";
		type = "info";		
    }else{
        textoAlerta="¿Quieres realizar la operación solicitada?";
		type = "warning";
		classButtom = "btn-primary";		
		classButtom = "btn-warning";		
    }

	swal({
	  title: "¿Estas seguro?",
	  text: textoAlerta,
	  type: type,
	  showCancelButton: true,
	  confirmButtonClass: classButtom,
	  confirmButtonText: "Aceptar",
	  cancelButtonText: "Cancelar",
	  closeOnConfirm: false
	},
	function(){		
        $.ajax({
            type: method,
            url: action,
            data: formdata ? formdata : form.serialize(),
            cache: false,
            contentType: false,
            processData: false,
            xhr: function(){
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt){
                  if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    percentComplete = parseInt(percentComplete * 100);
                    if(percentComplete<100){
                        respuesta.html('<p class="text-center">Procesado... ('+percentComplete+'%)</p><div class="progress progress-striped active"><div class="progress-bar progress-bar-info" style="width: '+percentComplete+'%;"></div></div>');
				    }else{
					  respuesta.html('<p class="text-center"></p>');
				    }
                  }
                }, false);
                return xhr;
            },
            success: function (data){
				var datos = eval(data);

				if(datos[0] == "Error"){
					swal({
						title: datos[0], 
						text: datos[1],
						type: datos[2],
						confirmButtonClass: datos[3]
					});						
				}else  if(datos[0] == "Guardar"){
					swal({
						title: datos[0], 
						text: datos[1],
						type: datos[2],
						confirmButtonClass: datos[3]
					});						
				}else{
					swal({
						title: datos[0], 
						text: datos[1],
						type: datos[2],
						timer: 3000,
						confirmButtonClass: datos[3]
					});						
				}						
				
				if(datos[4] != ""){
					$('#' + datos[4])[0].reset();
					$('#' + datos[4] + ' #pro').val(datos[5]);
				}
				
				llenarTabla(datos[6]);
				
				if (datos[6] == "AtencionMedica"){
					showFactura(datos[8]);//LLAMAMOS LA FACTURA .-Función se encuenta en myjava_atencioN_medica.js
				}
				
				if (datos[6] == "Facturacion"){
					pago(datos[8]); //LLAMAMOS LA FUNCION PARA REALIZAR EL PAGO .-Función se encuenta en myjava_facturacion.js
					pagination(1);
				}

				if (datos[6] == "Facturacion1"){
					pagination(1);
				}				

				if (datos[6] == "FacturacionCredito"){
					printBill(datos[8]); //LLAMAMOS LA FUNCION PARA IMPRIMIR LA FACTURA .-Función se encuenta en myjava_facturacion.js 	
					pagination(1);					
				}					
				
				if (datos[6] == "GuardarFactura"){
					pagination(1);
				}

				if (datos[6] == "Pagos"){
					printBill(datos[8]); //LLAMAMOS LA FUNCION PARA IMPRIMIR LA FACTURA .-Función se encuenta en myjava_facturacion.js 			
					limpiarTabla();
					pagination(1);
					volver();
					setTimeout(sendMail(datos[8]),5000);					
				}				

				if(datos[9] == "Eliminar"){
					$('#' + datos[7]).modal('hide');
				}
				
				if(datos[9] == "Guardar"){
					$('#' + datos[7]).modal('hide');
				}				
				
				if(datos[6] == "formCita"){
					reportePDF(datos[8]);
					sendEmailReprogramación(datos[8]);
				}
            },
            error: function() {
                respuesta.html(msjError);
            }
        });
        return false;
	});
});


/*##########################################################################################################################################################################################################################################################################################################################*/
/*##########################################################################################################################################################################################################################################################################################################################*/
//INICIO IDIOMA
var idioma_español = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "infoThousands": ",",
    "loadingRecords": "Cargando...",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    },
    "aria": {
        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad",
        "collection": "Colección",
        "colvisRestore": "Restaurar visibilidad",
        "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
        "copySuccess": {
            "1": "Copiada 1 fila al portapapeles",
            "_": "Copiadas %d fila al portapapeles"
        },
        "copyTitle": "Copiar al portapapeles",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Mostrar todas las filas",
            "1": "Mostrar 1 fila",
            "_": "Mostrar %d filas"
        },
        "pdf": "PDF",
        "print": "Imprimir"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Rellene todas las celdas con <i>%d<\/i>",
        "fillHorizontal": "Rellenar celdas horizontalmente",
        "fillVertical": "Rellenar celdas verticalmentemente"
    },
    "decimal": ",",
    "searchBuilder": {
        "add": "Añadir condición",
        "button": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "clearAll": "Borrar todo",
        "condition": "Condición",
        "conditions": {
            "date": {
                "after": "Despues",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual a",
                "not": "No",
                "notBetween": "No entre",
                "notEmpty": "No Vacio"
            },
            "moment": {
                "after": "Despues",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual a",
                "not": "No",
                "notBetween": "No entre",
                "notEmpty": "No vacio"
            },
            "number": {
                "between": "Entre",
                "empty": "Vacio",
                "equals": "Igual a",
                "gt": "Mayor a",
                "gte": "Mayor o igual a",
                "lt": "Menor que",
                "lte": "Menor o igual que",
                "not": "No",
                "notBetween": "No entre",
                "notEmpty": "No vacío"
            },
            "string": {
                "contains": "Contiene",
                "empty": "Vacío",
                "endsWith": "Termina en",
                "equals": "Igual a",
                "not": "No",
                "notEmpty": "No Vacio",
                "startsWith": "Empieza con"
            }
        },
        "data": "Data",
        "deleteTitle": "Eliminar regla de filtrado",
        "leftTitle": "Criterios anulados",
        "logicAnd": "Y",
        "logicOr": "O",
        "rightTitle": "Criterios de sangría",
        "title": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "value": "Valor"
    },
    "searchPanes": {
        "clearMessage": "Borrar todo",
        "collapse": {
            "0": "Paneles de búsqueda",
            "_": "Paneles de búsqueda (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Sin paneles de búsqueda",
        "loadMessage": "Cargando paneles de búsqueda",
        "title": "Filtros Activos - %d"
    },
    "select": {
        "1": "%d fila seleccionada",
        "_": "%d filas seleccionadas",
        "cells": {
            "1": "1 celda seleccionada",
            "_": "$d celdas seleccionadas"
        },
        "columns": {
            "1": "1 columna seleccionada",
            "_": "%d columnas seleccionadas"
        }
    },
    "thousands": "."
} 
//FIN IDIOMA

//INICIO CONVETIR IMAGEN BASE 64
function toDataURL(src, callback, outputFormat) {
  var img = new Image();
  img.crossOrigin = 'Anonymous';
  img.onload = function() {
    var canvas = document.createElement('CANVAS');
    var ctx = canvas.getContext('2d');
    var dataURL;
    canvas.height = this.naturalHeight;
    canvas.width = this.naturalWidth;
    ctx.drawImage(this, 0, 0);
    dataURL = canvas.toDataURL(outputFormat);
    callback(dataURL);
  };
  img.src = src;
  if (img.complete || img.complete === undefined) {
    img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
    img.src = src;
  }
}
//FIN CONVERTIR IMAGEN BASE 64

var imagen;
toDataURL(
  '../img/logo.png',
  function(dataUrl) {
	imagen = dataUrl;
  }
)

//INICIO DATATABLE OPCIONES
var lengthMenu = [[5, 10, 20, 50, 100, -1], [5, 10, 20, 50, 100, 'Todos']];

var dom = "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-5'i><'col-sm-7'p>>";		
//FIN DATATABLE OPCIONES	
/*##########################################################################################################################################################################################################################################################################################################################*/

/*##########################################################################################################################################################################################################################################################################################################################*/

//LLENADO DE TABLAS
$('#invoice-form #notes').keyup(function() {
	var max_chars = 255;
	var chars = $(this).val().length;
	var diff = max_chars - chars;
	
	$('#invoice-form #charNum_notas').html(diff + ' Caracteres'); 
	
	if(diff == 0){
		return false;
	}
});

function llenarTabla(dato){
	if(dato == "formPacientes"){
		pagination(1);
	}
	
	if(dato == "formCita"){
		pagination(1);
	}	
	
	if(dato == "Almacen"){
		listar_almacen();
	}		
	
	if(dato == "Preclinica"){
		pagination(1);
	}	

	if(dato == "Colaboradores"){
		pagination(1);
		puesto();
		getJornadaColaborador();
		servicio();	
	}	

	if(dato == "Puestos"){
		pagination_puestos(1);
		puesto();
		getJornadaColaborador();
		servicio();	
	}	

	if(dato == "Servicios"){
		pagination_servicio(1);
		puesto();
		getJornadaColaborador();
		servicio();	
	}	

	if(dato == "servicioColaboradores"){
		pagination_jornada_colaboradores(1);
		puesto();
		getJornadaColaborador();
		servicio();	
	}	

	if(dato == "ReporteEnfermeria"){
		pagination_preclinica(1);
	}		
	
	if(dato == "Ubicacion"){
		listar_ubicacion();
	}	

	if(dato == "Almacen"){
		listar_almacen();
	}	

	if(dato == "Aseguradora"){
		pagination(1);
	}	

	if(dato == "Productos"){
		listar_productos();
	}		
	
	if(dato == "Facturacion1"){
		pagination(1);
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();		
	}	

	if(dato == "FacturacionCredito"){
		pagination(1);
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();		
	}		

	if(dato == "Facturacion"){
		pagination(1);
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();		
	}	

	if(dato == "GuardarFactura"){
		pagination(1);
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();		
	}		

	if(dato == "FacturaAtenciones"){
		getServicio();
		listar_pacientes_buscar();
		listar_servicios_factura_buscar();
		listar_productos_facturas_buscar();		
		pagination(1);
		limpiarTabla();
		volver();
		cleanFooterValueBill();		
	}		
	
	if(dato == "Usuarios"){
		pagination(1);
	}	

	if(dato == "configuracionVarios"){
		pagination(1);
	}		
	
	if(dato == "formProfesionales"){
		paginationPorfesionales(1);
	}		
	
	if(dato == "Medidas"){
		listar_medidas();
	}	

	if(dato == "Categoria"){
		listar_categoria();
	}		
	
	if(dato == "Movimientos"){
		listar_movimientos();
		listar_productos_buscar();
		getCategoriaProductosMovimientos();
		getCategoriaProductos();
		getCategoriaOperacion();
	}			
	
	if(dato == "AtencionMedica"){
		pagination(1);
		listar_pacientes_buscar();
		listar_servicios_buscar();
		listar_servicios_factura_buscar();
		listar_productos_facturas_buscar();	
	}	

	if(dato == "configuracionVariosemails"){
		pagination(1);
	}	
	
	if(dato == "ReportePagos"){
		pagination(1);
	}		
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip({
	  trigger: "hover"
  })
});

//INICIO MENU FORM PAGOS FACTURAS
$(document).ready(function(){
	$(".menu-toggle2").hide();
	
	//Menu Toggle Script
	$("#menu-toggle1").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
	
	$("#menu-toggle2").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});	

	// For highlighting activated tabs
	$("#tab1").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab1").addClass("active1");
		$("#tab1").removeClass("bg-light");
	});
	
	$("#tab2").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab2").addClass("active1");
		$("#tab2").removeClass("bg-light");
	});
	
	$("#tab3").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab3").addClass("active1");
		$("#tab3").removeClass("bg-light");
	});
	
	$("#tab4").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab4").addClass("active1");
		$("#tab4").removeClass("bg-light");
	});	
})
//FIN MENU FORM PAGOS FACTURAS

$(".menu-toggle1").on("click", function(e){
	e.preventDefault();
	$(".menu-toggle1").hide();
	$(".menu-toggle2").show();
	$("#sidebar-wrapper").hide();
});

$(".menu-toggle2").on("click", function(e){
	e.preventDefault();
	$(".menu-toggle2").hide();
	$(".menu-toggle1").show();
	$("#sidebar-wrapper").show();
});
//FIN MENU FACTURAS