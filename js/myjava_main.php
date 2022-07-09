<script>
//OBTENER TOTAL DE USUARIOS
//TEMPORALES
function getTemporales(){
    var url = '<?php echo SERVERURL; ?>php/main/getTemporales.php';
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

//ACTIVOS
function getActivos(){
    var url = '<?php echo SERVERURL; ?>php/main/getActivos.php';
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

//PASIVOS
function getPasivos(){
    var url = '<?php echo SERVERURL; ?>php/main/getPasivos.php';
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

//FALLECIDOS
function getFallecidos(){
    var url = '<?php echo SERVERURL; ?>php/main/getFallecidos.php';
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

//ATENCIONES
//PRECLINICA CONSULTA EXTERNA
function getPreclinicaCE(){
    var url = '<?php echo SERVERURL; ?>php/main/getPreclinica_ce.php';
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

//PRECLINICA UNA
function getPreclinicaUNA(){
    var url = '<?php echo SERVERURL; ?>php/main/getPreclinica_una.php';
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

//PENDIENTES CONSULTA EXTERNA
function getPendientesCE(){
    var url = '<?php echo SERVERURL; ?>php/main/getPendientes_ce.php';
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

//PENDIENTES UNA
function getPendientesUNA(){
    var url = '<?php echo SERVERURL; ?>php/main/getPendientes_una.php';
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

//PENDIENTES CLINICA DE DEPOSITO
function getPendientesClinica(){
    var url = '<?php echo SERVERURL; ?>php/main/getPendientes_clinica.php';
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

//INASISTENCIAS CONSULTA EXTERNA
function getInasistenciasCE(){
    var url = '<?php echo SERVERURL; ?>php/main/getInasistencias_ce.php';
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

//INASISTENCIAS UNA
function getInasistenciasUNA(){
    var url = '<?php echo SERVERURL; ?>php/main/getInasistencias_una.php';
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

//INASISTENCIAS CLINICA DE DEPOSITO
function getInasistenciasClinica(){
    var url = '<?php echo SERVERURL; ?>php/main/getInasistencias_clinica.php';
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

//INASISTENCIAS TERAPIA OCUPACIONAL
function getInasistenciasTerapia(){
    var url = '<?php echo SERVERURL; ?>php/main/getInasistencias_terapia.php';
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

//EXTEMPORANEOS CONSULTA EXTERNA
function getExtemporaneosCE(){
    var url = '';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          usuario = "";			  		  		  			  
		}
	});
	return usuario;
}

//EXTEMPORANEOS CONSULTA UNA
function getExtemporaneosUNA(){
    var url = '';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          usuario = "";			  		  		  			  
		}
	});
	return usuario;
}

//EXTEMPORANEOS CONSULTA USUARIO EN CRISIS
function getExtemporaneoCrisis(){
    var url = '';
	var usuario;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){	
          usuario = "";			  		  		  			  
		}
	});
	return usuario;
}

/***************************************************************/
var datos = 0;
var relojito;

function main(){
	var temporal = getTemporales();
	var activo = getActivos();
	var pasivo = getPasivos();
	var fallecido = getFallecidos();
	/***********************************************/
	
	var preclinica_ce = getPreclinicaCE();
	var preclinica_una = getPreclinicaUNA();	
	/***********************************************/	
	var pendientes_ce = getPendientesCE();
	var pendientes_una = getPendientesUNA();
    var pendientes_clinica = getPendientesClinica();	
	/***********************************************/
	var inasistencias_ce = getInasistenciasCE();
	var inasistencias_una = getInasistenciasUNA();
	var inasistencias_clinica = getInasistenciasClinica();
	var inasistencias_terapia = getInasistenciasTerapia();
	/***********************************************/
	var extemporaneos_ce = getExtemporaneosCE();
	var extemporaneos_una = getExtemporaneosUNA();	
	var extemporaneos_crisis = getExtemporaneoCrisis();
	/***********************************************/
	var maida = getPendientesMAIDA();
    var s_h = getPendientesSH();
	/***********************************************/
	
	
	
    //Usuarios	
	$('#temporal').html("Total de Usuarios Temporales: " + temporal);
	$('#activos').html("Total de Usuarios Activos: " + activo);
	$('#pasivos').html("Total de Usuarios Pasivos: " + pasivo);
	$('#fallecidos').html("Total de Usuarios Fallecidos: " + fallecido);
    /***********************************************/
	//ATENCIONES
	$('#preclinica').html("Pendientes en C.E: " + preclinica_ce + "<br/>Pendientes en UNA: " + preclinica_una);
	$('#pendientes').html("Consulta Externa: " + pendientes_ce + "<br/>UNA: " + pendientes_una + "<br/>Clínica de Deposito: "
     	+ pendientes_clinica + "<br/>MAIDA: ");
	$('#inasistencias').html("Consulta Externa: " + inasistencias_ce + "<br/>UNA: " + inasistencias_una + "<br/>Clínica de Deposito: " + inasistencias_clinica + "<br/>Terapia Ocupacional: " + inasistencias_terapia);
	$('#extemporaneos').html("Consulta Externa: " + extemporaneos_ce + "<br/>UNA: " + extemporaneos_una + "<br/>Usuario en Crisis: " + extemporaneos_crisis);
    
	datos++;
    // lógica para obtener y mostrar los datos
    if (datos === 5)
       clearInterval(relojito);	
}

clearInterval(relojito);

relojito = setInterval(function(){
   main();
}, 10000);

$(document).ready(function() {
   main();
});

/***************************************************************/

//PENDIENTES HOSPITALIZACION
function getPendientesMAIDA(){
    var url = '<?php echo SERVERURL; ?>php/main/getPendientes_hospitalizacion_maida.php';
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

function getPendientesSH(){
    var url = '<?php echo SERVERURL; ?>php/main/getPendientes_hospitalizacion_sh.php';
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
</script>