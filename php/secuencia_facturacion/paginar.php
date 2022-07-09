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
	$where = "WHERE sf.activo = '$estado' AND (e.nombre LIKE '%$dato%' OR sf.siguiente LIKE '$dato%')";
}else{
	$where = "WHERE sf.empresa_id = '$empresa' AND sf.activo = '$estado' AND (e.nombre LIKE '%$dato%' OR sf.siguiente LIKE '$dato%')";	
}
$query = "SELECT sf.secuencia_facturacion_id AS 'secuencia_facturacion_id', e.nombre AS 'empresa', sf.cai AS 'cai', e.rtn AS 'rtn', sf.prefijo AS 'prefijo', sf.siguiente AS 'siguiente', CONCAT(sf.prefijo, '', sf.rango_inicial) AS 'rango_inicial', CONCAT(sf.prefijo, '', sf.rango_final) AS 'rango_final', sf.fecha_limite AS 'fecha_limite', sf.fecha_activacion AS 'fecha_activacion',
(CASE WHEN sf.activo = '1' THEN 'Sí' ELSE 'No' END) AS 'activo',
CAST(sf.fecha_registro AS DATE) AS 'fecha_registro', sf.relleno AS 'relleno'
	FROM secuencia_facturacion AS sf
	INNER JOIN empresa AS e
	ON sf.empresa_id = e.empresa_id
	".$where."
	ORDER BY sf.secuencia_facturacion_id ASC";
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

$registro = "SELECT sf.secuencia_facturacion_id AS 'secuencia_facturacion_id', e.nombre AS 'empresa', sf.cai AS 'cai', e.rtn AS 'rtn', sf.prefijo AS 'prefijo', sf.siguiente AS 'siguiente', CONCAT(sf.prefijo, '', sf.rango_inicial) AS 'rango_inicial', CONCAT(sf.prefijo, '', sf.rango_final) AS 'rango_final', sf.fecha_limite AS 'fecha_limite', sf.fecha_activacion AS 'fecha_activacion',
(CASE WHEN sf.activo = '1' THEN 'Sí' ELSE 'No' END) AS 'activo',
CAST(sf.fecha_registro AS DATE) AS 'fecha_registro', sf.relleno AS 'relleno'
	FROM secuencia_facturacion AS sf
	INNER JOIN empresa AS e
	ON sf.empresa_id = e.empresa_id
	".$where."
	ORDER BY sf.secuencia_facturacion_id ASC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2.09%">No.</th>
			<th width="11.09%">Empresa</th>				
			<th width="23.09%">CAI</th>
			<th width="6.09%">RTN</th>
			<th width="11.09%">Número Siguiente</th>
			<th width="11.09%">Rango Inicial</th>
			<th width="11.09%">Rango Final</th>
			<th width="9.09%">Fecha Activación</th>				
			<th width="6.09%">Fecha Limite</th>
			<th width="2.09%">Activo</th>
            <th width="5.09%">Opciones</th>				
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
			<td>'.$registro2['cai'].'</td>	
			<td>'.$registro2['rtn'].'</td>	
			<td>'.$numero.'</td>	
			<td>'.$registro2['rango_inicial'].'</td>	
			<td>'.$registro2['rango_final'].'</td>				
			<td>'.$registro2['fecha_activacion'].'</td>			
			<td>'.$registro2['fecha_limite'].'</td>	
			<td>'.$registro2['activo'].'</td>				
			<td>
              <a style="text-decoration:none;" data-toggle="tooltip" data-placement="top" title="Editar Usuario" href="javascript:editarRegistro('.$registro2['secuencia_facturacion_id'].');void(0);" class="fas fa-edit fa-lg"></a>	  			  
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="top" title="Eliminar" href="javascript:modal_eliminar('.$registro2['secuencia_facturacion_id'].');void(0);" class="fas fa-trash fa-lg"></a>
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