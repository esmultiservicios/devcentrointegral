<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$paginaActual = $_POST['partida'];
$medico = $_POST['medico'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];

$fecha = date('Y-m-d');

//EJECUTAMOS LA CONSULTA DE BUSQUEDA
if($medico==""){
$query = "SELECT CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', pc.nombre AS 'puesto', am.fecha_ausencia 'ausencia', am.comentario AS 'comentario'
           FROM ausencia_medicos AS am
           INNER JOIN colaboradores AS c
           ON am.colaborador_id = c.colaborador_id
           INNER JOIN puesto_colaboradores AS pc
           ON c.puesto_id = pc.puesto_id
           ORDER BY am.fecha_ausencia, c.colaborador_id DESC";	
}else{
$query = "SELECT CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', pc.nombre AS 'puesto', am.fecha_ausencia 'ausencia', am.comentario AS 'comentario'
           FROM ausencia_medicos AS am
           INNER JOIN colaboradores AS c
           ON am.colaborador_id = c.colaborador_id
           INNER JOIN puesto_colaboradores AS pc
           ON c.puesto_id = pc.puesto_id
		   WHERE am.fecha_ausencia BETWEEN '$fechai' AND '$fechaf' AND am.colaborador_id = '$medico' 
           ORDER BY am.fecha_ausencia, c.colaborador_id DESC";	
}
   $result = $mysqli->query($query);
	$nroProductos = $result->num_rows;
	
    $nroLotes = 3;
    $nroPaginas = ceil($nroProductos/$nroLotes);
    $lista = '';
    $tabla = '';

	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_ausencias('.(1).');">Inicio</a></li>';
    }
	
    if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_ausencias('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
    }
    
    if($paginaActual < $nroPaginas){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_ausencias('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
    }
	
	if($paginaActual > 1){
        $lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_ausencias('.($nroPaginas).');">Ultima</a></li>';
    }
  
  	if($paginaActual <= 1){
  		$limit = 0;
  	}else{
  		$limit = $nroLotes*($paginaActual-1);
  	}		  
	   
if($medico==""){
$registro = "SELECT ausencia_id, CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', pc.nombre AS 'puesto', am.fecha_ausencia 'ausencia', am.comentario AS 'comentario'
           FROM ausencia_medicos AS am
           INNER JOIN colaboradores AS c
           ON am.colaborador_id = c.colaborador_id
           INNER JOIN puesto_colaboradores AS pc
           ON c.puesto_id = pc.puesto_id
           ORDER BY am.fecha_ausencia, c.colaborador_id DESC LIMIT $limit, $nroLotes";
}else{
$registro = "SELECT ausencia_id, CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', pc.nombre AS 'puesto', am.fecha_ausencia 'ausencia', am.comentario AS 'comentario'
           FROM ausencia_medicos AS am
           INNER JOIN colaboradores AS c
           ON am.colaborador_id = c.colaborador_id
           INNER JOIN puesto_colaboradores AS pc
           ON c.puesto_id = pc.puesto_id
		   WHERE am.fecha_ausencia BETWEEN '$fechai' AND '$fechaf' AND am.colaborador_id = '$medico'
           ORDER BY am.fecha_ausencia, c.colaborador_id DESC LIMIT $limit, $nroLotes";
}	   

$result = $mysqli->query($registro);
//CREAMOS NUESTRA VISTA Y LA DEVOLVEMOS AL AJAX
  	$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			  <tr>
                <th width="20%">Colaborador</th>	
                <th width="20">Puesto</th>					
                <th width="20%">Fecha Ausencia</th>
				<th width="20%">Comentario</th>
				<th width="20%">Opciones</th>
			   </tr>';
$i = 1;					
if($result->num_rows>0){	
	while($registro2 = $result->fetch_assoc()){
		$tabla = $tabla.'<tr>	
		   <td>'.$registro2['colaborador'].'</td>		   
       	   <td>'.$registro2['puesto'].'</td>
		   <td>'.$registro2['ausencia'].'</td>	
           <td>'.$registro2['comentario'].'</td>
           <td><a style="text-decoration:none;" title = "Eliminar Registro" href="javascript:eliminarRegistro('.$registro2['ausencia_id'].');void(0);" class="fas fa-trash fa-lg"></a></td>		   
	  </tr>';	  
	}
      $tabla = $tabla.'<tr>
	   <td colspan="15"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
	  </tr>';		
}else{
    $tabla = $tabla.'<tr>
	   <td colspan="15" style="color:#C7030D">No se encontraron resultados.</td>
	</tr>';		
}      
	
    $tabla = $tabla.'</table>';

    $array = array(0 => $tabla,
    			   1 => $lista);

    echo json_encode($array);
	
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>