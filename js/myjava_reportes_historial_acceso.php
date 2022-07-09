<script>
$(document).ready(function() {
   getColaborador();
   pagination_accesos(1);
});

$(document).ready(function() {
  $('#form_main #fecha_i').on('change', function(){	
     pagination_accesos(1);
  });
});

$(document).ready(function() {
  $('#form_main #fecha_f').on('change', function(){	
     pagination_accesos(1);
  });
});

$(document).ready(function() {
  $('#form_main #bs-regis').on('keyup', function(){	
     pagination_accesos(1);
  });
});

function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/reportes_historial_acceso/getColaborador.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_main #colaborador').html("");
			$('#form_main #colaborador').html(data);
		}			
     });	
}

$('#form_main #reporte').on('click', function(e){
    e.preventDefault();
    reporteEXCEL();
});

function pagination_accesos(partida){
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var dato = $('#form_main #bs-regis').val();
	
	var url = '<?php echo SERVERURL; ?>php/reportes_historial_acceso/paginar.php';		

	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida+'&desde='+desde+'&hasta='+hasta+'&dato='+dato,	
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);			
		}
	});
	return false;	
}

function reporteEXCEL(){
	var desde = $('#form_main #fecha_i').val();
	var hasta = $('#form_main #fecha_f').val();
	var dato = $('#form_main #bs-regis').val();
	
	var url = '<?php echo SERVERURL; ?>php/reportes_historial_acceso/reporte.php?desde='+desde+'&hasta='+hasta+'&dato='+dato;
	    
	window.open(url);	
}
</script>