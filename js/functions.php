<script>
// JavaScript Document
function nologoneado(usuario){
   if (usuario == ""){
	   redireccionarsalida();
   }	
}

$('#salir_sistema').on('click', function () {
    swal({
        title: "¿Está seguro?",
        text: "¿Realmente desea salir del sistema?",
        icon: "info",
        buttons: {
            cancel: {
                text: "Cancelar",
                value: null,
                visible: true,
                className: "btn btn-secondary",
                closeModal: true
            },
            confirm: {
                text: "¡Sí, deseo salir del sistema!",
                value: true,
                visible: true,
                className: "btn btn-primary",
                closeModal: true
            }
        },
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }).then((willExit) => {
        if (willExit) {
            // Mostrar mensaje de espera con GIF
            swal({
                title: "",
                text: "Por favor espere...",
                icon: "<?php echo SERVERURL; ?>img/gif-load.gif", // Usando 'icon' para la imagen
                buttons: false, // Deshabilitar botones
                closeOnClickOutside: false, // Prevenir que el usuario cierre el modal al hacer clic afuera
            });

            // Simular espera antes de redirigir
            setTimeout(function () {
                redireccionarsalida(); // Realiza la redirección
            }, 1000);
        }
    });
});

function redireccionarsalida() {
    window.location = "<?php echo SERVERURL; ?>vistas/login.php";
}

/* ------------------------------------------------------------------------------------------------
                                     Descargar Archivos
   -------------------------------------------------------------------------------------------------
*/

//Ventana de espera
 function CallServerFunction() {
    $('#myPleaseWait').modal('show');
       $.ajax({
          success: function (data) {
            $('#myPleaseWait').modal('hide');
            },
            error: function () {
            $('#myPleaseWait').modal('hide');
            }
        });
}

//FUCION PARA AGREGAR FILAS A UNA TABLA 
$(function(){
	// Clona la fila oculta que tiene los campos base, y la agrega al final de la tabla
	$("#agregar").on('click', function(){
		$("#tabla tbody tr:eq(0)").clone().removeClass('fila-base').appendTo("#tabla tbody");
	});
 
	// Evento que selecciona la fila y la elimina 
	$(document).on("click",".eliminar",function(){
		var parent = $(this).parents().get(0);
		$(parent).remove();
	});
});

$(document).ready(function(){
	getSaludoSistema();
    setInterval('getSaludoSistema()',180000);
});

$(document).ready(function() {
  //CADA HORA ENVIARA UN CORREO INDICANDO LA CANTIDAD DE REGISTROS PENDIENTES
  //setInterval('evaluarRegistrosPendientesEmail()',3600000);
  
  //CADA HORA ENVIARA UN CORREO INDICANDO LA CANTIDAD DE REGISTROS PENDIENTES EN EL AREA DE PRECLINICA
  //setInterval('evaluarRegistrosPendientesEmailPreclinica()',3600000);
  
  //CADA HORA ENVIARA UN CORREO INDICANDO LA CANTIDAD DE REGISTROS PENDIENTES EN EL AREA DE POSTCLINICA
  //setInterval('evaluarRegistrosPendientesEmailPostclinica()',3600000);
});

function getSaludoSistema(){
    var url = '<?php echo SERVERURL; ?>php/main/getSaludoSistema.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
		  if(data == "Error"){        
            swal({
               title: "Sesión Finalizada",
               text: "Lo sentimos su sesión ha vencido, por favor inicie su sesión nuevamente",
               icon: "warning",
               buttons: {
                  cancel: {
                  text: "Cancelar",
                  visible: true
                  },
                  confirm: {
                  text: "¡Está bien, llévame al Inicio!",
                  }
               },
               dangerMode: true,
               closeOnEsc: false, // Desactiva el cierre con la tecla Esc
               closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
            }).then((willConfirm) => {
               if (willConfirm) {
                  setTimeout(function () {
                     redireccionarsalida();
                  }, 2000);
               }
            });         
		  }else{
			 $('#saludo_sistema').html(data); 
		  }	  
		}
	});
}

function evaluarRegistrosPendientesEmail(){
    var url = '<?php echo SERVERURL; ?>php/mail/evaluarPendientes.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		success: function(valores){	
           		  		  		  			  
		}
	});	
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

function evaluarRegistrosPendientesEmailPostclinica(){
    var url = '<?php echo SERVERURL; ?>php/mail/evaluarPendientes_postclinica.php';
	
	$.ajax({
	    type:'POST',
		url:url,
		success: function(valores){	
           		  		  		  			  
		}
	});	
}

//FROMULARIO DE PACIENTES
$(document).ready(function(e) {
    $('#formulario #correo').on('blur', function(){
	   if ($('#formulario #correo').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= document.getElementById("correo").value;
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario #validate').removeClass('error_email');			
			     $('#formulario #validate').addClass('bien_email').html('En hora buena, el correo electrónico es valido.').show(300);
				 $("#formulario #correo").css("border-color", "green");
			     $("#formulario #reg").attr('disabled', false);
		      }else{
		         $('#formulario #validate').removeClass('bien_email');	
                 $("#formulario #correo").css("border-color", "red");	
                 $("#formulario #correo").focus();					 
			     $('#formulario #validate').addClass('error_email').html('Correo electrónico invalido, por favor corregir.').show(300);
			     $("#formulario #reg").attr('disabled', true);			   
		      } 
           }
         }
         request.send(vars);		 
	   }else{
		   $('#formulario #validate').removeClass('bien_email');
		   $('#formulario #validate').removeClass('error_email');
		   $("#formulario #correo").css("border-color", "none");
		   $('#formulario #validate').html('');
		   $("#formulario #reg").attr('disabled', false);
	   }
	});
});

$(document).ready(function(e) {
    $('#formulario #correo').on('keyup', function(){
	   if ($('#formulario #correo').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= document.getElementById("correo").value;
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario #validate').removeClass('error_email');			
			     $('#formulario #validate').addClass('bien_email').html('En hora buena, el correo electrónico es valido.').show(300);
				 $("#formulario #correo").css("border-color", "green");
			     $("#formulario #reg").attr('disabled', false);
		      }else{
		         $('#formulario #validate').removeClass('bien_email');	
                 $("#formulario #correo").css("border-color", "red");	
                 $("#formulario #correo").focus();					 
			     $('#formulario #validate').addClass('error_email').html('Correo electrónico invalido, por favor corregir.').show(300);
			     $("#formulario #reg").attr('disabled', true);			   
		      } 
           }
         }
         request.send(vars);		 
	   }else{
		   $('#formulario #validate').removeClass('bien_email');
		   $('#formulario #validate').removeClass('error_email');
		   $("#formulario #correo").css("border-color", "none");
		   $('#formulario #validate').html('');
		   $("#formulario #reg").attr('disabled', false);
	   }
	});
});

$(document).ready(function(e) {
    $('#formulario_pacientes #correo').on('blur', function(){
	   if ($('#formulario_pacientes #correo').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= document.getElementById("correo").value;
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario_pacientes #validate').removeClass('error_email');			
			     $('#formulario_pacientes #validate').addClass('bien_email').html('En hora buena, el correo electrónico es valido.').show(300);
				 $("#formulario_pacientes #correo").css("border-color", "green");
			     $("#formulario_pacientes #reg").attr('disabled', false);
		      }else{
		         $('#formulario_pacientes #validate').removeClass('bien_email');	
                 $("#formulario_pacientes #correo").css("border-color", "red");	
                 $("#formulario_pacientes #correo").focus();					 
			     $('#formulario_pacientes #validate').addClass('error_email').html('Correo electrónico invalido, por favor corregir.').show(300);
			     $("#formulario_pacientes #reg").attr('disabled', true);			   
		      } 
           }
         }
         request.send(vars);		 
	   }else{
		   $('#formulario_pacientes #validate').removeClass('bien_email');
		   $('#formulario_pacientes #validate').removeClass('error_email');
		   $("#formulario_pacientes #correo").css("border-color", "none");
		   $('#formulario_pacientes #validate').html('');
		   $("#formulario_pacientes #reg").attr('disabled', false);
	   }
	});
});

$(document).ready(function(e) {
    $('#formulario_pacientes #correo').on('keyup', function(){
	   if ($('#formulario_pacientes #correo').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= document.getElementById("correo").value;
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario_pacientes #validate').removeClass('error_email');			
			     $('#formulario_pacientes #validate').addClass('bien_email').html('En hora buena, el correo electrónico es valido.').show(300);
				 $("#formulario_pacientes #correo").css("border-color", "green");
			     $("#formulario_pacientes #reg").attr('disabled', false);
		      }else{
		         $('#formulario_pacientes #validate').removeClass('bien_email');	
                 $("#formulario_pacientes #correo").css("border-color", "red");	
                 $("#formulario_pacientes #correo").focus();					 
			     $('#formulario_pacientes #validate').addClass('error_email').html('Correo electrónico invalido, por favor corregir.').show(300);
			     $("#formulario_pacientes #reg").attr('disabled', true);			   
		      } 
           }
         }
         request.send(vars);		 
	   }else{
		   $('#formulario_pacientes #validate').removeClass('bien_email');
		   $('#formulario_pacientes #validate').removeClass('error_email');
		   $("#formulario_pacientes #correo").css("border-color", "none");
		   $('#formulario_pacientes #validate').html('');
		   $("#formulario_pacientes #reg").attr('disabled', false);
	   }
	});
});

//FORMULARIO REFERENCIAS (AGREGAR CONFIRMACIÓN REFERENCIA ENVIADA)
$(document).ready(function(e) {
    $('#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta').on('blur', function(){
	   if ($('#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= $('#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta').val();
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario_agregar_respuesta_referencia_enviada #validate').removeClass('error_email');
                 $("#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta").css("border-color", "green");				 
			     $('#formulario_agregar_respuesta_referencia_enviada #validate').addClass('bien_email').html('Correo electrónico valido.').show(300);
			     $("#formulario_agregar_respuesta_referencia_enviada #reg_info_respuesta").attr('disabled', false);
		      }else{
		         $('#formulario_agregar_respuesta_referencia_enviada #validate').removeClass('bien_email');	
                 $("#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta").css("border-color", "red");
                 $("#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta").focus();					 
			     $('#formulario_agregar_respuesta_referencia_enviada #validate').addClass('error_email').html('Correo erroneo.').show(300);
			     $("#formulario_agregar_respuesta_referencia_enviada #reg_info_respuesta").attr('disabled', true);			   
		      } 
           }
         }

         request.send(vars);		 
	   }else{
		   $('#formulario_agregar_respuesta_referencia_enviada #validate').removeClass('bien_email');
		   $("#formulario_agregar_respuesta_referencia_enviada #correo_info_respuesta").css("border-color", "none");	
		   $('#formulario_agregar_respuesta_referencia_enviada #validate').removeClass('error_email');
		   $('#formulario_agregar_respuesta_referencia_enviada #validate').html('');
		   $("#formulario_agregar_respuesta_referencia_enviada #reg_info_respuesta").attr('disabled', false);
	   }
	});
});

//FORMULARIO REFERENCIAS (AGREGAR INFORMACIÓN A RESPUESTA RECIBIDA)
$(document).ready(function(e) {
    $('#formulario_agregar_info_respuesta_enviada #correo_info_respuesta').on('blur', function(){
		
	   if($('#formulario_agregar_info_respuesta_enviada #correo_info_respuesta').val() != ""){
	      var request;
      
	      try{
             request= new XMLHttpRequest();
          }

         catch (tryMicrosoft) {
            try{
               request= new ActiveXObject("Msxml2.XMLHTTP");
            }

            catch (otherMicrosoft){
               try{
                  request= new ActiveXObject("Microsoft.XMLHTTP");
               }

               catch (failed) {
                  request= null;
               }
            }
         }
   
         var url= "<?php echo SERVERURL; ?>php/mail/emailvalidation.php";
         var emailaddress= $('#formulario_agregar_info_respuesta_enviada #correo_info_respuesta').val();
         var vars= "email="+emailaddress;
         request.open("POST", url, true);

         request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

         request.onreadystatechange= function() {
           if (request.readyState == 4 && request.status == 200) {
	           var return_data =  request.responseText;
		   
		      if(return_data == 1){
		         $('#formulario_agregar_info_respuesta_enviada #validate').removeClass('error_email');
                 $("#formulario_agregar_info_respuesta_enviada #correo_info_respuesta").css("border-color", "green");				 
			     $('#formulario_agregar_info_respuesta_enviada #validate').addClass('bien_email').html('Correo electrónico valido.').show(300);
			     $("#formulario_agregar_info_respuesta_enviada #reg_info_respuesta").attr('disabled', false);
		      }else{
		         $('#formulario_agregar_info_respuesta_enviada #validate').removeClass('bien_email');	
                 $("#formulario_agregar_info_respuesta_enviada #correo_info_respuesta").css("border-color", "red");
                 $("#formulario_agregar_info_respuesta_enviada #correo_info_respuesta").focus();					 
			     $('#formulario_agregar_info_respuesta_enviada #validate').addClass('error_email').html('Correo erroneo.').show(300);
			     $("#formulario_agregar_info_respuesta_enviada #reg_info_respuesta").attr('disabled', true);			   
		      } 
           }
         }

         request.send(vars);		 
	   }else{
		   $('#formulario_agregar_info_respuesta_enviada #validate').removeClass('bien_email');
		   $("#formulario_agregar_info_respuesta_enviada #correo_info_respuesta").css("border-color", "none");	
		   $('#formulario_agregar_info_respuesta_enviada #validate').removeClass('error_email');
		   $('#formulario_agregar_info_respuesta_enviada #validate').html('');
		   $("#formulario_agregar_info_respuesta_enviada #reg_info_respuesta").attr('disabled', false);
	   }
	});
});

/**
 * jQuery snow effects.
 *
 * This is a heavily modified, jQuery-adapted, browser-agnostic version of 
 * "Snow Effect Script" by Altan d.o.o. (http://www.altan.hr/snow/index.html).
 *
 * Dustin Oprea (2011)
 */

function __ShowSnow(settings)
{

    var snowsrc = settings.SnowImage;
    var no = settings.Quantity;

    var dx, xp, yp;    // coordinate and position variables
    var am, stx, sty;  // amplitude and step variables
    var i; 

    var doc_width = $(window).width() - 10;
    var doc_height = $(window).height();

    dx = [];
    xp = [];
    yp = [];
    am = [];
    stx = [];
    sty = [];
    flakes = [];
    for (i = 0; i < no; ++i) 
    {
        dx[i] = 0;                        // set coordinate variables
        xp[i] = Math.random()*(doc_width-50);  // set position variables
        yp[i] = Math.random()*doc_height;
        am[i] = Math.random()*20;         // set amplitude variables
        stx[i] = 0.02 + Math.random()/10; // set step variables
        sty[i] = 0.7 + Math.random();     // set step variables

        var flake = $("<div />");

        var id = ("dot" + i);
        flake.attr("id", id);
        flake.css({
                    position: "absolute",
                    zIndex: i,
                    top: "15px",
                    left: "15px"
                });

        flake.append("<img src='" + snowsrc + "'>");
        flake.appendTo("body");

        flakes[i] = $("#" + id);
    }

    var animateSnow;
    animateSnow = function() 
    {  
        for (i = 0; i < no; ++ i) 
        {
            // iterate for every dot
            yp[i] += sty[i];
            if (yp[i] > doc_height - 50) 
            {
                xp[i] = Math.random() * (doc_width - am[i] - 30);
                yp[i] = 0;
                stx[i] = 0.02 + Math.random() / 10;
                sty[i] = 0.7 + Math.random();
            }
      
            dx[i] += stx[i];
            flakes[i].css("top", yp[i] + "px");
            flakes[i].css("left", (xp[i] + am[i] * Math.sin(dx[i])) + "px");
        }

        snowtimer = setTimeout(animateSnow, 10);
    };

	function hidesnow()
    {
		if(window.snowtimer)
            clearTimeout(snowtimer)

        for (i = 0; i < no; i++)
            flakes[i].hide();
	}
		
    animateSnow();
	if (settings.HideSnowTime > 0)
    	setTimeout(hidesnow, settings.HideSnowTime * 1000)
}

(function($) {
    $.fn.snow = function(options) {
  
    var settings = $.extend({
            SnowImage:      undefined,
            Quantity:       7,
            HideSnowTime:   0
        }, options);

    __ShowSnow(settings);

    return this;
  }

})(jQuery);

/*
$(function() {
    var now = new Date();

    if((now.getMonth() == 10 && now.getDate() >= 26) || (now.getMonth() == 11 && now.getDate() <= 31) || (now.getMonth() == 0 && now.getDate() <= 6)){
		$(document).snow({ SnowImage: "<?php echo SERVERURL; ?>img/snow.gif" });
	}			   
});*/


//VERIFICAR AUSENCIA DE USUARIOS
function getFechaAusencias(fecha, colaborador_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getFechaAusencias.php';
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
function getComentarioAusencia(fecha, colaborador_id){
    var url = '<?php echo SERVERURL; ?>php/citas/getComentarioAusencias.php';
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

function confirmar(agenda_id, colaborador_id, servicio_id){
	if($('#form_agenda_main #atencion').val() == 0 || $('#form_agenda_main #atencion').val() == '' || $('#form_agenda_main #atencion').val() == null){
	    modal_agregar_confirmacion(agenda_id, colaborador_id, servicio_id);//llama esta funcion	
	}else if($('#form_agenda_main #atencion').val() == 2){
		modal_agregar_confirmacionAusencia(agenda_id, colaborador_id, servicio_id);
	}else{
		swal({
			title: "Error", 
			text: "Lo sentimos esta opción no esta disponible",
			icon: "error", 
         dangerMode: true,
         closeOnEsc: false, // Desactiva el cierre con la tecla Esc
         closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		  
	}
}

// Función general para mostrar alertas SweetAlert
function mostrarAlerta(tipo, titulo, mensaje) {
    swal({
        title: titulo,
        text: mensaje,
        icon: tipo,  // "success", "error", "warning", "info"
        button: "Aceptar",
        closeOnEsc: false, // Desactiva el cierre con la tecla Esc
        closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera        
    });
}

// Funciones específicas para cada tipo de alerta
function mostrarInfo(titulo, mensaje) {
    mostrarAlerta("info", titulo, mensaje);
}

function mostrarSuccess(titulo, mensaje) {
    mostrarAlerta("success", titulo, mensaje);
}

function mostrarWarning(titulo, mensaje) {
    mostrarAlerta("warning", titulo, mensaje);
}

function mostrarError(titulo, mensaje) {
    mostrarAlerta("error", titulo, mensaje);
}

function getAseguradora(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getAseguradora.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #aseguradora_id').html("");
			$('#formulario_facturacion #aseguradora_id').html(data);
			$('#formulario_facturacion #aseguradora_id').selectpicker('refresh');			
        }
     });		
}

function getFactEmpresas(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getFactEmpresas.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #fact_empresas_id').html("");
			$('#formulario_facturacion #fact_empresas_id').html(data);	
			$('#formulario_facturacion #fact_empresas_id').selectpicker('refresh');		
        }
     });		
}

function getPacientesFacturas(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #pacientes_id').html("");
			 $('#formulario_facturacion #pacientes_id').html(data);	
			 $('#formulario_facturacion #pacientes_id').selectpicker('refresh');		
        }
     });		
}

function getColaboradoresFactura(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getColaborador.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #colaborador_id').html("");
			 $('#formulario_facturacion #colaborador_id').html(data);	
			 $('#formulario_facturacion #colaborador_id').selectpicker('refresh');		
        }
     });		
}

function getServicioFactura(){
    var url = '<?php echo SERVERURL; ?>php/citas/getServicio.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#formulario_facturacion #servicio_id').html("");
  			 $('#formulario_facturacion #servicio_id').html(data);	
 			 $('#formulario_facturacion #servicio_id').selectpicker('refresh');		
        }
     });		
}

// Función general para mostrar alertas SweetAlert con emojis y opciones
function mostrarAlerta(tipo, titulo, mensaje, opciones = []) {
    const opcionesAlerta = {
        title: titulo,
        text: mensaje,
        icon: tipo,  // "success", "error", "warning", "info"
        button: "Aceptar",
        closeOnEsc: false, // Desactiva el cierre con la tecla Esc
        closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
    };

    // Si es un error o advertencia, agregar dangerMode
    if (tipo === "error" || tipo === "warning") {
        opcionesAlerta.dangerMode = true;
    }

    // Si hay opciones, se personaliza el botón
    if (opciones.length > 0) {
        opcionesAlerta.buttons = opciones;
    }

    swal(opcionesAlerta);
}

// Función para alertas con input
function mostrarInput(titulo, mensaje, callback) {
    swal({
        title: titulo,
        text: mensaje,
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
                text: "¡Sí, remover el usuario!",
                closeModal: false,
            },
        },
        dangerMode: true,
        closeOnEsc: false,
        closeOnClickOutside: false,
    }).then((value) => {
        if (value === null || value.trim() === "") {
            swal("¡Necesita escribir algo!", { icon: "error" });
            return false;
        }
        callback(value);  // Llama al callback con el valor del input
    });
}

// Función para alertas de confirmación (Sí/No o Aceptar/Cancelar)
function mostrarConfirmacion(titulo, mensaje, callback) {
    swal({
        title: titulo,
        text: mensaje,
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancelar",
                visible: true
            },
            confirm: {
                text: "¡Sí, eliminar!",
            }
        },
        dangerMode: true,
        closeOnEsc: false,
        closeOnClickOutside: false
    }).then((willConfirm) => {
        if (willConfirm) {
            callback();  // Llama al callback si el usuario confirma
        }
    });
}

// Función para alertas con contenido HTML
function mostrarHTML(titulo, contenidoHTML, callback) {
    swal({
        title: titulo,
        content: {
            element: "span",
            attributes: {
                innerHTML: contenidoHTML
            }
        },
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancelar",
                visible: true
            },
            confirm: {
                text: "¡Sí, continuar!",
            }
        },
        dangerMode: true,
        closeOnEsc: false,
        closeOnClickOutside: false
    }).then((willConfirm) => {
        if (willConfirm) {
            callback();  // Llama al callback si el usuario confirma
        }
    });
}

// Funciones específicas para cada tipo de alerta con emojis
function mostrarInfo(titulo, mensaje) {
    mostrarAlerta("info", titulo, "ℹ️ " + mensaje + " 🔍");
}

function mostrarSuccess(titulo, mensaje) {
    mostrarAlerta("success", titulo, "✅ " + mensaje + " 🎉");
}

function mostrarWarning(titulo, mensaje) {
    mostrarAlerta("warning", titulo, "⚠️ " + mensaje + " 🚨");
}

function mostrarError(titulo, mensaje) {
    mostrarAlerta("error", titulo, "❌ " + mensaje + " 🛑");
}

/* Ejemplo de uso de las alertas
mostrarInput("¿Está seguro?", "¿Desea remover este usuario?", function(comentario) {
    console.log("Comentario: " + comentario);
});

mostrarConfirmacion("¿Estás seguro?", "¿Deseas eliminar este registro?", function() {
    console.log("Registro eliminado");
});

mostrarHTML("¿Estás seguro?", "¿Deseas enviar los <b>SMS</b> de forma masiva?", function() {
    console.log("SMS enviados");
});*/
</script>