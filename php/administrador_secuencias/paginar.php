<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$dato = $_POST['dato'];
$empresa = $_POST['empresa'];
$estado = $_POST['estado'];

if($empresa == 0){
	$where = "WHERE s.estado = '$estado' AND (e.nombre LIKE '%$dato%' OR s.siguiente LIKE '$dato%')";
}else{
	$where = "WHERE s.empresa_id = '$empresa' AND s.estado = '$estado' AND (e.nombre LIKE '%$dato%' OR s.siguiente LIKE '$dato%')";	
}
$query = "SELECT s.secuencias_id AS 'secuencias_id', e.nombre AS 'empresa', s.entidad AS 'tabla', s.prefijo AS 'prefijo', s.sufijo AS 'sufijo', s.relleno AS 'relleno', s.incremento AS 'incremento', s.siguiente AS 'siguiente',
(CASE WHEN s.estado = 1 THEN 'Activo' ELSE 'Inactivo' END) AS 'estado'
	FROM secuencias AS s
	INNER JOIN empresa AS e
	ON s.empresa_id = e.empresa_id
	".$where."
	ORDER BY s.secuencias_id ASC";
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

$registro = "SELECT s.secuencias_id AS 'secuencias_id', e.nombre AS 'empresa', s.entidad AS 'tabla', s.prefijo AS 'prefijo', s.sufijo AS 'sufijo', s.relleno AS 'relleno', s.incremento AS 'incremento', s.siguiente AS 'siguiente',
(CASE WHEN s.estado = 1 THEN 'Activo' ELSE 'Inactivo' END) AS 'estado'
	FROM secuencias AS s
	INNER JOIN empresa AS e
	ON s.empresa_id = e.empresa_id
	".$where."
	ORDER BY s.secuencias_id ASC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2%">No.</th>
			<th width="13%">Empresa</th>				
			<th width="8%">Tabla</th>
			<th width="12%">Prefijo</th>
			<th width="12%">Sufijo</th>
			<th width="10%">Relleno</th>
			<th width="10%">Incremento</th>
			<th width="10%">Siguiente</th>				
			<th width="10%">Estado</th>
            <th width="10%">Opciones</th>				
			</tr>';
$i = 1;				
while($registro2 = $result->fetch_assoc()){ 
    $relleno = $registro2['relleno']; 
	$prefijo = $registro2['prefijo'];
    $siguiente = $registro2['siguiente'];
    $numero = 	$registro2['prefijo'].''.str_pad($siguiente, $relleno, "0", STR_PAD_LEFT);
	
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['empresa'].'</td>	
			<td>'.$registro2['tabla'].'</td>	
			<td>'.$registro2['prefijo'].'</td>
			<td>'.$registro2['sufijo'].'</td>			
			<td>'.$registro2['relleno'].'</td>	
			<td>'.$registro2['incremento'].'</td>
			<td>'.$registro2['siguiente'].'</td>
			<td>'.$registro2['estado'].'</td>			
			<td>
              <a style="text-decoration:none;" data-toggle="tooltip" data-placement="top" title="Editar Usuario" href="javascript:editarRegistro('.$registro2['secuencias_id'].');void(0);" class="fas fa-edit fa-lg"></a>	  			  
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="top" title="Eliminar" href="javascript:modal_eliminar('.$registro2['secuencias_id'].');void(0);" class="fas fa-trash fa-lg"></a>
			</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="11" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="11"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>