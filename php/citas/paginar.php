<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$fecha = date('Y-m-d');

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$query = "SELECT DISTINCT p.expediente AS 'expediente', p.identidad As 'identidad', 
         CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.apellido As 'apellido', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', a.observacion AS 'observacion', a.hora as 'hora'
         FROM agenda AS a
         INNER JOIN pacientes AS p
         ON a.pacientes_id = p.pacientes_id
         INNER JOIN colaboradores AS c
         ON a.colaborador_id = c.colaborador_id
         WHERE a.status = 0 AND cast(a.fecha_cita as date) >= '$fecha'
         ORDER BY c.colaborador_id, a.fecha_cita DESC";	
$result = $mysqli->query($query);		 
$nroProductos = $result->num_rows;
	   
    $nroLotes = 5;
    $nroPaginas = ceil($nroProductos/$nroLotes);
    $lista = '';
    $tabla = '';

	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" ="javascript:pagination('.(1).');">Inicio</a></li>';
    }
	
    if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
    }
    
    if($paginaActual < $nroPaginas){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
    }
	
	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($nroPaginas).');">Ultima</a></li>';
    }
  
  	if($paginaActual <= 1){
  		$limit = 0;
  	}else{
  		$limit = $nroLotes*($paginaActual-1);
  	}		  
	   
	   
$registro = "SELECT DISTINCT p.expediente AS 'expediente', p.identidad As 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.apellido As 'apellido', 
         CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', a.observacion AS 'observacion', a.hora as 'hora'
         FROM agenda AS a
         INNER JOIN pacientes AS p
         ON a.pacientes_id = p.pacientes_id
         INNER JOIN colaboradores AS c
         ON a.colaborador_id = c.colaborador_id
         WHERE a.status = 0 AND cast(a.fecha_cita as date) >= '$fecha'
         ORDER BY c.colaborador_id, a.fecha_cita DESC LIMIT $limit, $nroLotes";	   
$result = $mysqli->query($registro);	   

//CREAMOS NUESTRA VISTA Y LA DEVOLVEMOS AL AJAX
  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			  <tr>
                <th width="8.29%">Expediente</th>	
                <th width="14.29%">Identidad</th>					
                <th width="17.29%">Nombre</th>
                <th width="18.29%">Profesional</th>
                <th width="16.29%">Fecha de Cita</th>
				<th width="7.29%">Hora</th>
				<th width="18.29%">Observación</th>
			   </tr>';
$i = 1;					
if($result->num_rows>0){	
	while($registro2 = $result->fetch_assoc()){
	  if ($registro2['expediente'] == 0){
		  $expediente = "TEMP"; 
	  }else{
		  $expediente = $registro2['expediente'];
	  }	

	  if ($registro2['observacion'] == ""){
		 $observacion = "No hay ninguna observación";
	  }else{
		$observacion = $registro2['observacion'];
	  }	 

	  if ($registro2['observacion'] == ""){
		 $observacion = "No hay ninguna observación";
	  }else{
		$observacion = $registro2['observacion'];
	  }	  	  
	  
		$tabla = $tabla.'<tr>
		   <td>'.$expediente.'</td>		
		   <td>'.$registro2['identidad'].'</td>		   
       	   <td>'.$registro2['nombre'].'</td>
		   <td>'.$registro2['colaborador'].'</td>		   
		   <td>'.$registro2['fecha_cita'].'</td>
		   <td>'.$registro2['hora'].'</td>
		   <td>'.$observacion.'</td>		   
	  </tr>';
	}
      $tabla = $tabla.'<tr>
	   <td colspan="15"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
	  </tr>';			
}else{
    $tabla = $tabla.'<tr>
	   <td colspan="15" style="color:#C7030D">No se encontraron resultados, o no ha seleccionado un médico de la lista</td>
	</tr>';		
}      
	
    $tabla = $tabla.'</table>';

    $array = array(0 => $tabla,
    			   1 => $lista);

    echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>