<script>
$('#mostrar_cambiar_contraseña').on('click',function(){
	$('#ModalContraseña').modal({
	      show:true,
		  keyboard: false,
          backdrop:'static'
    });		
	limpiarForm();
});

/*VERIFICAR CONTRASEÑA DE USUARIO ANTERIOR*/
$(document).ready(function(){
	limpiarForm();
    $("#ModalContraseña").on('shown.bs.modal', function(){
        $(this).find('#form-cambiarcontra #contranaterior').focus();
    });
});

$(document).ready(function(e) {
    $('#form-cambiarcontra #repcontra').on('blur', function(){
		if($('#form-cambiarcontra #repcontra').val() != ""){
		  if ($('#form-cambiarcontra #nuevacontra').val() != $('#form-cambiarcontra #repcontra').val()){ 
			swal({
				title: "Error", 
				text: "Contraseñas no coinciden",
				type: "error", 
				confirmButtonClass: "btn-danger",
				allowEscapeKey: false,
				allowOutsideClick: false
			});
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
			$("#form-cambiarcontra #repcontra").css("border-color", "red");
			return false;
		  }else{
			$("#form-cambiarcontra #repcontra").css("border-color", "none");
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', false); 
		  }		
		}else{
			$("#form-cambiarcontra #repcontra").css("border-color", "none");
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true); 
		  }	
	});		
});

$(document).ready(function(e) {
    $('#form-cambiarcontra #repcontra').on('keyup', function(){
		if($('#form-cambiarcontra #repcontra').val() != ""){
		  if ($('#form-cambiarcontra #nuevacontra').val() != $('#form-cambiarcontra #repcontra').val()){ 
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
			$("#form-cambiarcontra #repcontra").css("border-color", "red");
			return false;
		  }else{
			$("#form-cambiarcontra #repcontra").css("border-color", "green");
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', false); 
		  }		
		}else{
			$("#form-cambiarcontra #repcontra").css("border-color", "none");
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true); 
		  }	
	});		
});

function limpiarForm(){
	$('#form-cambiarcontra #contranaterior').val("");
	$('#form-cambiarcontra #nuevacontra').val("");
	$('#form-cambiarcontra #repcontra').val("");
    $('#form-cambiarcontra #mensaje').html("");
	$('#form-cambiarcontra #mayus').show();
	$('#form-cambiarcontra #special').show();
	$('#form-cambiarcontra #numbers').show();
	$('#form-cambiarcontra #lower').show();
	$('#form-cambiarcontra #len').show();	
	$('#form-cambiarcontra #contranaterior').focus();
	$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
	$('#form-cambiarcontra #mensaje_cmabiar_contra').html("");
	$("#form-cambiarcontra #contranaterior").css("border-color", "none");
    $("#form-cambiarcontra #repcontra").css("border-color", "none");
    $("#form-cambiarcontra #nuevacontra").css("border-color", "none");	
}

$(document).ready(function(e) {
    $('#form-cambiarcontra #contranaterior').on('blur', function(){
		if($('#form-cambiarcontra #contranaterior').val() != ""){
		     var url = '<?php echo SERVERURL; ?>php/users/consultar_pass.php';
		 
		     $.ajax({
		       type:'POST',
		       url:url,
		       data:$('#form-cambiarcontra').serialize(),
		       success: function(datos){
			     if (datos == 0){	
						swal({
							title: "Error", 
							text: "La contraseña que ingreso no coincide con la anterior",
							type: "error", 
							confirmButtonClass: "btn-danger",
							allowEscapeKey: false,
							allowOutsideClick: false
						});
						$("#form-cambiarcontra #contranaterior").css("border-color", "red");
						return false;
			     }else{
					 $("#form-cambiarcontra #contranaterior").css("border-color", "green");
				 }
		       }
	         });
	        return false;	
		}
	});
});

function agregaRegistro_contraseña(){	
	var url = '<?php echo SERVERURL; ?>php/users/cambiar_pass.php';
	$.ajax({
		type:'POST',
		url:url,
		data:$('#form-cambiarcontra').serialize(),
		success: function(registro){
			if (registro == 1){
			   $('#form-cambiarcontra')[0].reset();
				swal({
					title: "Success",
					text: "Contraseña cambiada correctamente",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "¡Bien Hecho!",
					closeOnConfirm: false,
					showLoaderOnConfirm: true,
					allowEscapeKey: false,
					allowOutsideClick: false
				}, function () {
				setTimeout(function () {
					window.location = "<?php echo SERVERURL; ?>php/signin_out/signinout.php";
				}, 500);
				});				   
			   return false;				
			}else if (registro == 3){			
				swal({
					title: "Error", 
					text: "No se puede cambiar la contraseña",
					type: "error", 
					confirmButtonClass: "btn-danger",
					allowEscapeKey: false,
					allowOutsideClick: false
				});			   
			   return false;				
			}else{
				swal({
					title: "Error", 
					text: "Error al cambiar los datos, por favor intente mas tarde",
					type: "error", 
					confirmButtonClass: "btn-danger",
					allowEscapeKey: false,
					allowOutsideClick: false
				});				
				return false;
	   		}
		}
	});
	return false;
}

$('#form-cambiarcontra #Modalcambiarcontra_Edit').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action     
	 if ($('#form-cambiarcontra #contranaterior').val()!="" && $('#form-cambiarcontra #nuevacontra').val()!="" && $('#form-cambiarcontra #repcontra').val()!=""){
	    e.preventDefault();
	    agregaRegistro_contraseña() // send to form submit function	 
	 }
});

//VALIDAR CONTRASEÑA
$(function(){
    var mayus = new RegExp("^(?=.*[A-Z])");
	var special = new RegExp("^(?=.*[!@#$%&*¡?¿|°/\+-.:,;()~<>])");
	var numbers = new RegExp("^(?=.*[0-9])");
	var lower = new RegExp("^(?=.*[a-z])");
	var len = new RegExp("^(?=.{8,})");
	
	
    var regExpr = [mayus,special,numbers,lower,len];
	var elementos = [$('#form-cambiarcontra #mayus'),$('#form-cambiarcontra #special'),$('#form-cambiarcontra #numbers'),$('#form-cambiarcontra #lower'),$('#form-cambiarcontra #len')];
	
	$('#form-cambiarcontra #nuevacontra').on("keyup", function(){
		if($('#form-cambiarcontra #nuevacontra').val() != ""){
		   var pass = $('#form-cambiarcontra #nuevacontra').val();
		   var check = 0;
		   
		   for(var i = 0; i < 5; i++){
			  if(regExpr[i].test(pass)){
			  	  elementos[i].hide();
				  check++;
			  }else{
				  elementos[i].show();
			  }
		  }
		  
		  $('#form-cambiarcontra #check').val(check);
		  if(check >= 0 && check <= 2){
			  $('#form-cambiarcontra #mensaje_cmabiar_contra').html("<strong>Contraseña Insegura</strong>").css("color","red");
			  $("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
		  }else if(check >= 3 && check <= 4){
			  $('#form-cambiarcontra #mensaje_cmabiar_contra').html("<strong>Contraseña poco segura</strong>").css("color","orange");
			  $("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
		  }else if(check == 5){
              $('#form-cambiarcontra #mensaje_cmabiar_contra').html("<strong>Contraseña muy segura</strong>").css("color","green");
			  $("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
		  }			
		}else{
			$('#form-cambiarcontra #mensaje_cmabiar_contra').html("");
			$('#form-cambiarcontra #mayus').show();
			$('#form-cambiarcontra #special').show();
			$('#form-cambiarcontra #numbers').show();
			$('#form-cambiarcontra #lower').show();
			$('#form-cambiarcontra #len').show();
			$("#form-cambiarcontra #Modalcambiarcontra_Edit").attr('disabled', true);
		}
	});
});

//MOSTRAR CONTRASEÑA
$(document).ready(function () {
	//CAMPO CONTRASEÑA ANTERIOR
    $('#form-cambiarcontra #show_password1').on('mousedown',function(){
		var cambio = $("#form-cambiarcontra #contranaterior")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#icon1').removeClass('fa-solid fa-eye-slash fa-lg').addClass('fa-solid fa-eye fa-lg');
		}else{
			cambio.type = "password";
			$('#icon1').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
		}
		return false;
    });

    $('#form-cambiarcontra #show_password1').on('mousedown',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		return false;
    });	
	
	//CAMPO NUEVA CONTRASEÑA
    $('#form-cambiarcontra #show_password2').on('mousedown',function(){
		var cambio = $("#form-cambiarcontra #nuevacontra")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#icon2').removeClass('fa-solid fa-eye-slash fa-lg').addClass('fa-solid fa-eye fa-lg');
		}else{
			cambio.type = "password";
			$('#icon2').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
		}
		return false;
    });

    $('#form-cambiarcontra #show_password2').on('click',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		return false;
    });	

    //CAMPO REPETIR CONTRASEÑA
    $('#form-cambiarcontra #show_password3').on('click',function(){
		var cambio = $("#form-cambiarcontra #repcontra")[0];
		if(cambio.type == "password"){
			cambio.type = "text";
			$('#icon3').removeClass('fa-solid fa-eye-slash fa-lg').addClass('fa-solid fa-eye fa-lg');
		}else{
			cambio.type = "password";
			$('#icon3').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
		}
		return false;
    });

    $('#form-cambiarcontra #show_password3').on('click',function(){
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		return false;
    });	
	
    //OCULTAR CONTRASEÑA	
    $('#form-cambiarcontra #show_password1').on('mouseout', function(){
		 $('#icon1').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
         var cambio = $("#form-cambiarcontra #contranaterior")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
		 return false;
    });	
	
    $('#form-cambiarcontra #show_password2').on('mouseout', function(){
		 $('#icon2').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
         var cambio = $("#form-cambiarcontra #nuevacontra")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    });	

    $('#form-cambiarcontra #show_password3').on('mouseout', function(){
		 $('#icon3').removeClass('fa-solid fa-eye fa-lg').addClass('fa-solid fa-eye-slash fa-lg');
         var cambio = $("#form-cambiarcontra #repcontra")[0];
         cambio.type = "password";
		 $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    });			
});
	
</script>