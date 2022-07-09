<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 	

$paginaActual = $_POST['partida'];
$dato = $_POST['dato'];

$query = "SELECT puesto_id, nombre 
	FROM puesto_colaboradores 
	WHERE nombre LIKE '$dato%'
	ORDER BY puesto_id ASC";
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
  
  
 $nroLotes = 5;
 $nroPaginas = ceil($nroProductos/$nroLotes);
 $lista = '';
 $tabla = '';

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_puestos('.(1).');void(0);">Inicio</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_puestos('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
 }

 if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_puestos('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_puestos('.($nroPaginas).');void(0);">Ultima</a></li>';
 }

 if($paginaActual <= 1){
	$limit = 0;
 }else{
	$limit = $nroLotes*($paginaActual-1);
 }


$registro = "SELECT puesto_id, nombre 
	FROM puesto_colaboradores 
	WHERE nombre LIKE '$dato%'
	ORDER BY puesto_id ASC LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
					<tr>
					  <th width="23.33%">Código</th>
					  <th width="53.33%">Puesto</th>					  
					  <th width="23.33%">Opciones</th>
					</tr>';
					
while($registro2 = $result->fetch_assoc()){						
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['puesto_id'].'</td>		
	   <td>'.$registro2['nombre'].'</td>		   		   		   		   		   		   
	   <td>
		   <a style="text-decoration:none;" title="Eliminar registro" href="javascript:modal_eliminarPuesto('.$registro2['puesto_id'].');void(0);" class="fas fa-trash fa-lg"></a>
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