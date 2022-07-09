<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$dato = $_POST['dato'];
$paginaActual = $_POST['partida'];

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$query = "SELECT DATE_FORMAT(CAST(li.fecha_solicitud AS DATE), '%d/%m/%Y') AS 'fecha_solicitud', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', 
    DATE_FORMAT(li.fecha_cita, '%d/%m/%Y') AS 'fecha_cita', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario', s.nombre AS 'servicio'
    FROM lista_espera AS li
    INNER JOIN pacientes AS p
    ON li.pacientes_id = p.pacientes_id
    INNER JOIN colaboradores AS c
    ON li.colaborador_id = c.colaborador_id
    INNER JOIN colaboradores AS c1
    ON li.usuario = c1.colaborador_id
    INNER JOIN servicios AS s
	ON li.servicio = s.servicio_id
    WHERE reprogramo = 'X' AND (p.expediente LIKE '$dato%' OR CONCAT(p.nombre,' ',p.apellido) like '$dato%' OR p.identidad like '$dato%' OR CONCAT(c.nombre,' ',c.apellido) like '$dato%')
    ORDER BY li.fecha_solicitud DESC";
$result = $mysqli->query($query);	
	
$nroProductos  = $result->num_rows;	
	   
    $nroLotes = 5;
    $nroPaginas = ceil($nroProductos/$nroLotes);
    $lista = '';
    $tabla = '';

	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_reprogramaciones('.(1).');">Inicio</a></li>';
    }
	
    if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_reprogramaciones('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
    }
    
    if($paginaActual < $nroPaginas){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_reprogramaciones('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
    }
	
	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_busqueda_reprogramaciones('.($nroPaginas).');">Ultima</a></li>';
    }
  
  	if($paginaActual <= 1){
  		$limit = 0;
  	}else{
  		$limit = $nroLotes*($paginaActual-1);
  	}		  
	   
	   
$registro = "SELECT DATE_FORMAT(CAST(li.fecha_solicitud AS DATE), '%d/%m/%Y') AS 'fecha_solicitud', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', DATE_FORMAT(li.fecha_cita, '%d/%m/%Y') AS 'fecha_cita', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario', s.nombre AS 'servicio'
    FROM lista_espera AS li
    INNER JOIN pacientes AS p
    ON li.pacientes_id = p.pacientes_id
    INNER JOIN colaboradores AS c
    ON li.colaborador_id = c.colaborador_id
    INNER JOIN colaboradores AS c1
    ON li.usuario = c1.colaborador_id
    INNER JOIN servicios AS s
	ON li.servicio = s.servicio_id
    WHERE reprogramo = 'X' AND (p.expediente LIKE '$dato%' OR CONCAT(p.nombre,' ',p.apellido) like '$dato%' OR p.identidad like '$dato%' OR CONCAT(c.nombre,' ',c.apellido) like '$dato%')
    ORDER BY li.fecha_solicitud DESC LIMIT $limit, $nroLotes";	 
$result = $mysqli->query($registro);
	   

//CREAMOS NUESTRA VISTA Y LA DEVOLVEMOS AL AJAX
  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			  <tr>
                <th width="13.29%">Fecha Solicitud</th>	
                <th width="10.29%">Expediente</th>					
                <th width="19.29%">Paciente</th>
                <th width="12.29%">Fecha Cita</th>
                <th width="17.29%">Profesional</th>
				<th width="9.29%">Servicio</th>
				<th width="20.29%">Usuario</th>
			   </tr>';
$i = 1;					
if($result->num_rows>0){	
	while($registro2 = $result->fetch_assoc()){
	  if ($registro2['expediente'] == 0){
		  $expediente = "TEMP"; 
	  }else{
		  $expediente = $registro2['expediente'];
	  }		  
	  
		$tabla = $tabla.'<tr>
		   <td>'.$registro2['fecha_solicitud'].'</td>		
		   <td>'.$expediente.'</td>		   
       	   <td>'.$registro2['paciente'].'</td>
		   <td>'.$registro2['fecha_cita'].'</td>		   
		   <td>'.$registro2['colaborador'].'</td>
		   <td>'.$registro2['servicio'].'</td>
		   <td>'.$registro2['usuario'].'</td>			   
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