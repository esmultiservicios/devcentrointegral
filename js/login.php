<script>
function sf(ID){
     document.getElementById(ID).focus();
}

function redireccionar(){
	if(getUsuarioSistema() == 1 || getUsuarioSistema() == 2){
		window.location="<?php echo SERVERURL; ?>vistas/inicio.php";
	}if(getUsuarioSistema() == 3){
		window.location="<?php echo SERVERURL; ?>vistas/atencion_medica.php";
	}else if(getUsuarioSistema() == 4){
		window.location="<?php echo SERVERURL; ?>vistas/empresas.php";
	}else if(getUsuarioSistema() == 5 || getUsuarioSistema() == 6){
		window.location="<?php echo SERVERURL; ?>vistas/pacientes.php";
	}else{
		window.location="<?php echo SERVERURL; ?>vistas/inicio.php";
	}
} 

function getUsuarioSistema(){
    var url = '<?php echo SERVERURL; ?>php/sesion/sistema_tipo_usuario.php';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          usuario = data;			  		  		  			  
		}
	});
	return usuario;
}
/*
---------------------------------------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------------------------------------
*/
$(document).ready(function(){
	$("#loginform").submit(function(){
		var url = '<?php echo SERVERURL; ?>php/signin_out/signin.php';
		
		$.ajax({
			type:'POST',
     	    url:url,
     	    data:$('#loginform').serialize(), 
			beforeSend: function(){
				swal({
				  title: "",
				  text: "Por favor espere...",
				  imageUrl: '<?php echo SERVERURL; ?>img/gif-load.gif',
				  closeOnConfirm: false,
				  showConfirmButton: false,
				  imageSize: '150x150',
				});
				$("#loginform #acceso").show();
            },		   
		    success: function(resp){
				if (resp == 1){ 
     				$("#loginform #acceso").show();
					setTimeout ("redireccionar()", 500);	
				}else if( resp == 0 ){
					swal({
						title: 'Error', 
						text: 'Usuario y/o contraseña son incorrectos',
						type: 'error', 
						confirmButtonClass: 'btn-danger'
					});	
					$("#loginform #acceso").hide();
					$("#loginform #acceso").html("");
					$("#loginform #usu").focus();					
				}else if( resp == 2 ){
					swal({
						title: 'Error', 
						text: 'Su usuario no se encuentra activo',
						type: 'error', 
						confirmButtonClass: 'btn-danger'
					});	
					$("#loginform #acceso").hide();
					$("#loginform #acceso").html("");
					$("#loginform #usu").focus();
				}
										
			},
			error : function(){
				swal({
					title: 'Error', 
					text: 'No se enviaron los datos, favor corregir',
					type: 'error', 
					confirmButtonClass: 'btn-danger'
				});	
				$("#loginform #acceso").hide();
				$("#loginform #acceso").html("");
				$("#loginform #usu").focus();		
			}
	});
	return false;
	});
	
	$("#forgot_form").submit(function(){
		var url = '<?php echo SERVERURL; ?>php/mail/resetear_login.php';
		$.ajax({
			type:'POST',
     	    url:url,
     	    data:$('#forgot_form').serialize(), 
			beforeSend: function(){

            },		   
		    success: function(resp){
				if (resp == 1){ 
					swal({
						title: "Success", 
						text: 'Contraseña reseteada, se ha enviado a su correo electrónico',
						type: "success", 
					});						
				}else if (resp == 2){ 
					swal({
						title: "Error", 
						text: "Error al resetear la contraseña",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
				}else if (resp == 3){ 
					swal({
						title: "Error", 
						text: "El usuario ingresado no existe",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
				}else{
					swal({
						title: "Error", 
						text: "Error al completar los datos",
						type: "error", 
						confirmButtonClass: 'btn-danger'
					});	
				}
										
			},
			error : function(){
				swal({
					title: "Error", 
					text: "Error al procesar su solicitud de inicio de sesión",
					type: "error", 
					confirmButtonClass: 'btn-danger'
				});				
			}
	});
	return false;
	});	
});

$(function() {
    $('#inicio_sesion').click(function(e) {
		$("#loginform").delay(100).fadeIn(100);
 		$("#forgot_form").fadeOut(100);
		$('#register-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});
	$('#forgot').click(function(e) {
		$("#forgot_form #usu_forgot").focus();
		$("#forgot_form").delay(100).fadeIn(100);
 		$("#loginform").fadeOut(100);
		$('#login-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

});

//MOSTRAR CONTRASEÑA FORMULARIO INGRESO DE SESIÓN
$(document).ready(function () {
    $('#loginform #show_password').on('mousedown',function(){
		var cambio = $("#loginform #inputPassword")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#loginform #icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
		}else{
			cambio.type = "password";
			$('#loginform #icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
		}
    });

    $('#loginform #show_password').on('click',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    });	
	
	//OCULTAR CONTRASEÑA
    $('#loginform #show_password').on('mouseout', function(){
		 $('#loginform #icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
         var cambio = $("#loginform #inputPassword")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		 return false;
    });	
});

//MOSTRAR CONGTRASEÑA FORMULARIO REGISTRO DE USUARIO
$(document).ready(function () {
	//CAMPO CONTRASEÑA
    $('#form_registro #show_password1').on('mousedown',function(){
		var cambio = $("#form_registro #user-pass")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#form-signup #icon1').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
		}else{
			cambio.type = "password";
			$('#form-signup #icon1').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
		}
    });

    $('#form_registro #show_password1').on('click',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    });	
	
	//OCULTAR CONTRASEÑA
    $('#form_registro #show_password1').on('mouseout', function(){
		 $('#form_registro #icon1').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
         var cambio = $("#form_registro #user-pass")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		 return false;
    });	
	
	//CAMPO REPETIR CONTRASEÑA
    $('#form_registro #show_password2').on('mousedown',function(){
		var cambio = $("#form_registro #user-repeatpass")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#form-signup #icon2').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
		}else{
			cambio.type = "password";
			$('#form-signup #icon2').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
		}
    });

    $('#form_registro #show_password2').on('click',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    });	
	
	//OCULTAR CONTRASEÑA
    $('#form_registro #show_password2').on('mouseout', function(){
		 $('#form_registro #icon2').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
         var cambio = $("#form_registro #user-repeatpass")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		 return false;
    });		
});


</script>