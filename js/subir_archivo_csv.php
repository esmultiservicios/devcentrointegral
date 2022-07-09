<script>
$(function(){
  $('#subida').submit(function(){
	var comprobar = $('#file-0a').val().length;
	  if(comprobar>0){
		var formulario = $('#subida');
		var selector = $('#first-disabled2').val();
		var url = "";
		var archivos = new FormData();
		
		if (selector == "ata")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_ata.php';	
		else if (selector == "pacientes")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_pacientes.php';
	   	else if (selector == "patologias")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_patologias.php';
	   	else if (selector == "agenda")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_agenda.php'; 
	   	else if (selector == "activos")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_activos.php'; 
	   	else if (selector == "centros_hospitalarios")
		   url = '<?php echo SERVERURL; ?>php/importarCSV/importar_centros_hospitalarios.php' 	   
	   
		for (var i = 0; i < (formulario.find('input[type=file]').length); i++) { 
		     archivos.append((formulario.find('input[type="file"]:eq('+i+')').attr("name")),((formulario.find('input[type="file"]:eq('+i+')')[0]).files[0]));
		}
			 
		$.ajax({
		  url: url,
		  type: 'POST', 
		  data: archivos,
		  contentType: false,
		  processData:false,
		  cache:false,

		  beforeSend : function (){
			  $('#respuesta').html('<img src="<?php echo SERVERURL; ?>img/gif-load.gif" width="5%" height="5%">');	
		  },
			
		success: function(resp){
		  if(resp == 'OK'){
			swal({
				title: "Success", 
				text: "Importación de CSV correctamente",
				type: "success", 
			});			 
			 return false;	 
		  }else{
			swal({
				title: "Error", 
				text: "Error en la importación del CSV",
				type: "error", 
				confirmButtonClass: 'btn-danger'
			});			 
			return false;
          }
		}
	});
	return false;
	}else{
		swal({
			title: "Error", 
			text: "Selecciona un archivo CSV para importar",
			type: "error", 
			confirmButtonClass: 'btn-danger'
		});			
		return false;
	}
	});
});

function bajar(){
	var selector = $('#first-disabled2').val();
	
	if (selector == "ata")
		   window.location = "<?php echo SERVERURL; ?>files/formato_sistema_atas.xlsx";
		else if (selector == "pacientes")
    	   window.location = "<?php echo SERVERURL; ?>files/formato_sistema_pacientes.xlsx"
		else if (selector == "agenda")
		   window.location = "<?php echo SERVERURL; ?>files/formato_sistema_agenda.xlsx"
		else if (selector == "patologias")
		   window.location = "<?php echo SERVERURL; ?>files/formato_sistema_patologias.xlsx"	
		else if (selector == "activos")
		   window.location = "<?php echo SERVERURL; ?>files/formato_activo.xlsx"	
		else if (selector == "pasivos")
		   window.location = "<?php echo SERVERURL; ?>files/formato_pasivo.xlsx"		
        else if (selector == "centros_hospitalarios")
		   window.location = "<?php echo SERVERURL; ?>files/centros_hospitalario_subir.xlsx"		   
}

$('#subida #ejemplo').on('click', function(e){
    e.preventDefault();
    bajar();
});

$(document).ready(function() {
   getTipoArchivo();			
});

function getTipoArchivo(){
    var url = '<?php echo SERVERURL; ?>php/importarCSV/getTipoArchivo.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#subida #first-disabled2').html("");
			$('#subida #first-disabled2').html(data);
			$('#subida #first-disabled2').selectpicker('refresh');
		}			
     });		
}
</script>