<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$profesional = $_POST['profesional'];

if($profesional == ""){
	$query_nroProductos = "SELECT * 
	   FROM profesion";
}else{
	$query_nroProductos = "SELECT * 
		FROM profesion 
		WHERE nombre LIKE '$profesional%'";
}
		
$result = $mysqli->query($query_nroProductos);	
$nroProductos = $result->num_rows;

$nroLotes = 5;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationPorfesionales('.(1).');">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationPorfesionales('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationPorfesionales('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationPorfesionales('.($nroPaginas).');">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}
	
if($profesional == ""){
	$query = "SELECT * 
		FROM profesion
		ORDER BY profesion_id LIMIT $limit, $nroLotes";		
}else{
	$query = "SELECT * 
		FROM profesion
		WHERE nombre LIKE '$profesional%'
		ORDER BY profesion_id LIMIT $limit, $nroLotes";
}

$result = $mysqli->query($query);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
					<tr>
					   <th width="13.33%">N°</th>
					   <th width="73.33%">Nombre</th>							   
					   <th width="13.33%">Opciones</th>
					</tr>';
						
while($registro2 = $result->fetch_assoc()){
	$tabla = $tabla.'<tr>		
	   <td>'.$registro2['profesion_id'].'</td>
	   <td>'.$registro2['nombre'].'</td>		   
	   <td>
		   <a style="text-decoration:none;" title = "Eliminar Registro" href="javascript:modal_eliminarProfesional('.$registro2['profesion_id'].');" class="fas fa-trash fa-lg"></a>
	   </td>
  </tr>';	
}
	
if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="3" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="3"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}   

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>