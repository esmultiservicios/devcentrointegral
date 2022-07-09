<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$dato = $_POST['dato'];
$empresa = $_POST['empresa'];
	
if($dato == ""){
	$where = "";
}else{
	$where = "WHERE nombre LIKE '%$dato%'";
}

$query = "SELECT * 
   FROM empresa
   ".$where."
   ORDER BY empresa_id ASC";
$result = $mysqli->query($query);

$nroLotes = 25;
$nroProductos = $result->num_rows;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$registro = "SELECT * 
   FROM empresa
   ".$where."
   ORDER BY empresa_id ASC
   LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2.28%">No.</th>
			<th width="24.28%">Empresa</th>				
			<th width="10.28%">RTN</th>
			<th width="8.28%">Teléfono</th>
			<th width="14.28%">Correo</th>			
			<th width="34.28%">Ubicación</th>
			<th width="4.28%">Opciones</th>
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){  
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['nombre'].'</td>	
			<td>'.$registro2['rtn'].'</td>
			<td>'.$registro2['telefono'].'</td>
			<td>'.$registro2['correo'].'</td>			
			<td>'.$registro2['ubicacion'].'</td>			
			<td>
			  <a style="text-decoration:none;" title = "Editar Usuario" href="javascript:editarRegistro('.$registro2['empresa_id'].');void(0);" class="fas fa-edit fa-lg"></a>	  			  
			  <a style="text-decoration:none;" href="javascript:modal_eliminar('.$registro2['empresa_id'].');void(0);" class="fas fa-trash fa-lg"></a>
			</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="8" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="8"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>