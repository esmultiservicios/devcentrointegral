<script>
/*
------------------------------------------------------------------------------------------------------------------
***********************************CARGAR DATOS A SELECT CORPORACION*********************************************
------------------------------------------------------------------------------------------------------------------
*/
$(function(){
	$('#departamento').on('load', function(){
		var id = $('#departamento').val();
		var url = '<?php echo SERVERURL; ?>php/selects/departamentos_municipios.php';
		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#municipio option').remove();
				$('#municipio').append(data);
			}
		});
		return false;
	});
});

$(document).ready(function() {    
	$('#departamento').on('blur', function(){
		var id = $('#departamento').val();
		var url = '<?php echo SERVERURL; ?>php/selects/departamentos_municipios.php';
		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#municipio option').remove();
				$('#municipio').append(data);
			}
		});
		return false;
	});
}); 

$(function(){
	$('#departamento1').on('load', function(){
		var id = $('#departamento1').val();
		var url = '<?php echo SERVERURL; ?>php/selects/departamentos_municipios.php';
		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#municipio1 option').remove();
				$('#municipio1').append(data);
			}
		});
		return false;
	});
});

$(function(){
	$('#departamento').on('click', function(){
		var id = $('#departamento').val();
		var url = '<?php echo SERVERURL; ?>php/selects/departamentos_municipios.php';
		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,			
			success: function(data){
				$('#municipio option').remove();
				$('#municipio').append(data);
			}
		});
		return false;
	});
});

$(function(){
	$('#departamento1').on('click', function(){
		var id = $('#departamento1').val();
		var url = '<?php echo SERVERURL; ?>php/selects/departamentos_municipios.php';
		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#municipio1 option').remove();
				$('#municipio1').append(data);
			}
		});
		return false;
	});
});

//MODULO ATAS
$(document).ready(function() {    
    $('#exp').keypress(function(){
        //Obtenemos el value del input
		var id = $('#exp').val();
		var url = '<?php echo SERVERURL; ?>php/selects/paciente.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#expediente option').remove();
				$('#expediente').append(data);
			}
		});
      });
   });   

function patologiaCIE10_1(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia1').html(data);
			    $('#patologia1').selectpicker('refresh');					
			}
		});	
	
}

function patologiaCIE10_2(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia2').html(data);
			    $('#patologia2').selectpicker('refresh');				
			}
		});	
	
}

function patologiaCIE10_3(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia3').html(data);
			    $('#patologia3').selectpicker('refresh');				
			}
		});	
	
}

function patologiaCIE10_1_1(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia_1').html(data);
			    $('#patologia_1').selectpicker('refresh');				
			}
		});	
	
}

function patologiaCIE10_2_1(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia_2').html(data);
			    $('#patologia_2').selectpicker('refresh');				
			}
		});	
	
}

function patologiaCIE10_3_1(){
		var id = "";
		var url = '<?php echo SERVERURL; ?>php/selects/diagnostico_CIE_reset.php';

        //Le pasamos el valor del input al ajax
        $.ajax({
            type: "POST",
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
			    $('#patologia_3').html(data);
			    $('#patologia_3').selectpicker('refresh');				
			}
		});	
	
}

 //MODULO REPORTES
$(document).ready(function() { 
$(function(){
	$('#first-disabled2').on('change', function(){
		var id = $('#first-disabled2').val();
		var url = '<?php echo SERVERURL; ?>php/selects/colaboradores.php';

		$.ajax({
			type:'POST',
			url:url,
			data:'id='+id,
			success: function(data){
				$('#first-disabled3').html(data);
				$('#first-disabled3').selectpicker('refresh');
			}
		});
		return false;
	});
}); 
}); 
</script>