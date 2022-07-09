<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$dato = $_POST['dato'];
$paginaActual = $_POST['partida'];

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$query = "SELECT DISTINCT p.expediente AS 'expediente', p.identidad As 'identidad', p.nombre AS 'nombre', 
         p.apellido As 'apellido', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', DATE_FORMAT(CAST(a.fecha_registro AS DATE), '%d/%m/%Y') AS 'fecha_registro', a.observacion AS 'observacion', a.hora as 'hora', a.comentario AS 'comentario', s.nombre AS 'servicio'
         FROM agenda AS a
         INNER JOIN pacientes AS p
         ON a.pacientes_id = p.pacientes_id
         INNER JOIN colaboradores AS c
         ON a.colaborador_id = c.colaborador_id
		 INNER JOIN servicios AS s
		 ON a.servicio_id = s.servicio_id		 
         WHERE a.status = 2 AND (p.expediente LIKE '$dato%' OR CONCAT(p.nombre,' ',p.apellido) like '$dato%' OR p.identidad like '$dato%' OR CONCAT(c.nombre,' ',c.apellido) like '$dato%')
         ORDER BY a.fecha_cita DESC";
$result = $mysqli->query($query);		 
		 
$nroProductos = $result->num_rows; 
	   
    $nroLotes = 5;
    $nroPaginas = ceil($nroProductos/$nroLotes);
    $lista = '';
    $tabla = '';

	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_historial_nopresento('.(1).');">Inicio</a></li>';
    }
	
    if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_historial_nopresento('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
    }
    
    if($paginaActual < $nroPaginas){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_historial_nopresento('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
    }
	
	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_historial_nopresento('.($nroPaginas).');">Ultima</a></li>';
    }
  
  	if($paginaActual <= 1){
  		$limit = 0;
  	}else{
  		$limit = $nroLotes*($paginaActual-1);
  	}		  
	   
	   
$registro = "SELECT DISTINCT p.expediente AS 'expediente', p.identidad As 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', 
         p.apellido As 'apellido', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', DATE_FORMAT(CAST(a.fecha_registro AS DATE), '%d/%m/%Y') AS 'fecha_registro', a.observacion AS 'observacion', a.hora as 'hora', a.comentario AS 'comentario', s.nombre AS 'servicio'
         FROM agenda AS a
         INNER JOIN pacientes AS p
         ON a.pacientes_id = p.pacientes_id
         INNER JOIN colaboradores AS c
         ON a.colaborador_id = c.colaborador_id
		 INNER JOIN servicios AS s
		 ON a.servicio_id = s.servicio_id		 
         WHERE a.status = 2 AND (p.expediente LIKE '$dato%' OR CONCAT(p.nombre,' ',p.apellido) like '$dato%' OR p.identidad like '$dato%' OR CONCAT(c.nombre,' ',c.apellido) like '$dato%')
         ORDER BY a.fecha_cita DESC LIMIT $limit, $nroLotes
	   ";	
$result = $mysqli->query($registro);	   

//CREAMOS NUESTRA VISTA Y LA DEVOLVEMOS AL AJAX
  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			  <tr>
                <th width="11.11%">Expediente</th>	
                <th width="11.11%">Identidad</th>					
                <th width="11.11%">Nombre</th>
                <th width="11.11%">Profesional</th>
                <th width="12.11%">Fecha de Registro</th>				
                <th width="12.11%">Fecha de Cita</th>
				<th width="5.11%">Hora</th>
				<th width="11.11%">Observación</th>
				<th width="11.11%">Comentario</th>
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
	  
	  
	  if ($registro2['comentario'] == ""){
		 $comentario = "No hay ningun comentario";
	  }else{
		$comentario = $registro2['comentario'];
	  }	  	
	  
		$tabla = $tabla.'<tr>
		   <td>'.$expediente.'</td>		
		   <td>'.$registro2['identidad'].'</td>		   
       	   <td>'.$registro2['nombre'].'</td>
		   <td>'.$registro2['colaborador'].' ('.$registro2['servicio'].')</td>
		   <td>'.$registro2['fecha_registro'].'</td>		   
		   <td>'.$registro2['fecha_cita'].'</td>
		   <td>'.$registro2['hora'].'</td>
		   <td>'.$observacion.'</td>
           <td>'.$comentario.'</td>		   
	  </tr>';
	}
      $tabla = $tabla.'<tr>
	   <td colspan="13"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
	  </tr>';		
}else{
    $tabla = $tabla.'<tr>
	   <td colspan="13" style="color:#C7030D">No se encontraron resultados.</td>
	</tr>';		
}      
	
    $tabla = $tabla.'</table>';

    $array = array(0 => $tabla,
    			   1 => $lista);

    echo json_encode($array);
	
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>