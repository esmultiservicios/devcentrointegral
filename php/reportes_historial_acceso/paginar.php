<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];

$paginaActual = $_POST['partida'];
$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$dato = $_POST['dato'];
	
$query = "SELECT DATE_FORMAT(ha.fecha, '%d/%m/%Y %h:%i:%s %p') AS 'fecha', CONCAT(c.nombre,' ',c.apellido) AS 'nombre', ha.ip AS 'ip', ha.acceso AS 'comentario'
   FROM historial_acceso AS ha
   INNER JOIN colaboradores AS c
   ON ha.colaborador_id = c.colaborador_id
   WHERE CAST(ha.fecha AS DATE) BETWEEN '$desde' AND '$hasta' AND (CONCAT(c.nombre,' ',c.apellido) LIKE '%$dato%')
   ORDER BY ha.fecha DESC";
$result = $mysqli->query($query);
$nroProductos = $result->num_rows;
 
$nroLotes = 15;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';	

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_accesos('.(1).');">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_accesos('.($paginaActual-1).');">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_accesos('.($paginaActual+1).');">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:pagination_accesos('.($nroPaginas).');">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}	

$registro = "SELECT DATE_FORMAT(ha.fecha, '%d/%m/%Y %h:%i:%s %p') AS 'fecha', CONCAT(c.nombre,' ',c.apellido) AS 'nombre', ha.ip AS 'ip', ha.acceso AS 'comentario'
   FROM historial_acceso AS ha
   INNER JOIN colaboradores AS c
   ON ha.colaborador_id = c.colaborador_id
   WHERE CAST(ha.fecha AS DATE) BETWEEN '$desde' AND '$hasta' AND (CONCAT(c.nombre,' ',c.apellido) LIKE '%$dato%')
   ORDER BY ha.fecha DESC
   LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="15%">Fecha</th>
			<th width="25%">Nombre</th>
			<th width="25%">IP</th>	
			<th width="35%">Comentario</th>				
			</tr>';			
			
while($registro2 = $result->fetch_assoc()){	
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['fecha'].'</td>		   
	   <td>'.$registro2['nombre'].'</td>
	   <td>'.$registro2['ip'].'</td>
	   <td>'.$registro2['comentario'].'</td>		   
			
	</tr>';	        
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="14" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="15"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>