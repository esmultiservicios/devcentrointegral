<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 	

$paginaActual = $_POST['partida'];
$colaborador_id = $_POST['colaborador_id'];

if($colaborador_id == ""){
	$where = "";
}else{
	$where = "WHERE c.colaborador_id = '$colaborador_id'";
}

date_default_timezone_set('America/Tegucigalpa');

$query = "SELECT sc.id AS 'id', c.colaborador_id as 'colaborador_id', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', pc.nombre AS 'puesto', jorn.nombre AS 'jornada', sc.nuevos AS 'nuevos', sc.subsiguientes AS 'subsiguientes' 
	FROM jornada_colaboradores AS sc 
	INNER JOIN colaboradores AS c 
	ON sc.colaborador_id = c.colaborador_id 
	INNER JOIN puesto_colaboradores AS pc 
	ON pc.puesto_id = c.puesto_id 
	INNER JOIN jornada AS jorn ON jorn.jornada_id = sc.j_colaborador_id 
	".$where."
	ORDER by c.colaborador_id";	
 $result = $mysqli->query($query);
 $nroProductos = $result->num_rows;
  
  
 $nroLotes = 3;
 $nroPaginas = ceil($nroProductos/$nroLotes);
 $lista = '';
 $tabla = '';

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:pagination_servicio_colaborador('.(1).');void(0);">Inicio</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:pagination_servicio_colaborador('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
 }

 if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:pagination_servicio_colaborador('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:pagination_servicio_colaborador('.($nroPaginas).');void(0);">Ultima</a></li>';
 }

 if($paginaActual <= 1){
	$limit = 0;
 }else{
	$limit = $nroLotes*($paginaActual-1);
 }


$registro = "SELECT sc.id AS 'id', c.colaborador_id as 'colaborador_id', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', pc.nombre AS 'puesto', jorn.nombre AS 'jornada', sc.nuevos AS 'nuevos', sc.subsiguientes AS 'subsiguientes' 
	FROM jornada_colaboradores AS sc 
	INNER JOIN colaboradores AS c 
	ON sc.colaborador_id = c.colaborador_id 
	INNER JOIN puesto_colaboradores AS pc 
	ON pc.puesto_id = c.puesto_id 
	INNER JOIN jornada AS jorn ON jorn.jornada_id = sc.j_colaborador_id 
	".$where."
	ORDER by c.colaborador_id
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);		


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
					<tr>
					  <th width="21.66%">Nombre</th>
					  <th width="21.66%">Puesto</th>
					  <th width="16.66%">Jornada</th>	
					  <th width="16.66%">Nuevos</th>	
					  <th width="16.66%">Subsiguiente</th>							  
					  <th width="6.6%">Opciones</th>
					</tr>';
					
while($registro2 = $result->fetch_assoc()){						
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['nombre'].'</td>		
	   <td>'.$registro2['puesto'].'</td>
	   <td>'.$registro2['jornada'].'</td>
	   <td>'.$registro2['nuevos'].'</td>
	   <td>'.$registro2['subsiguientes'].'</td>		   
	   <td>
		   <a style="text-decoration:none; "href="javascript:modal_eliminarJornadaColaboradores('.$registro2['colaborador_id'].');void(0);" class="fas fa-trash fa-lg"></a>
	   </td>
  </tr>';
}
	

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="7" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="7"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>