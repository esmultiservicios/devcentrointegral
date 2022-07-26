<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$profesional = $_POST['profesional'];
$estado = $_POST['estado'];
$usuario = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional != ""){
		$where = "WHERE p.fecha BETWEEN '$fechai' AND '$fechaf' AND f.colaborador_id = '$profesional' AND p.estado = '$estado'";
	}else if($dato != ""){
		$where = "WHERE p.estado = '$estado' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%' OR f.number LIKE '$dato%')";
	}else{
		$where = "WHERE p.fecha BETWEEN '$fechai' AND '$fechaf' AND p.estado = '$estado'";
	}
}else{
	if($profesional != ""){
		$where = "WHERE p.fecha BETWEEN '$fechai' AND '$fechaf' AND f.colaborador_id = '$profesional' AND p.estado = '$estado' AND p.usuario = '$usuario'";
	}else if($dato != ""){
		$where = "WHERE p.estado = '$estado' AND p.usuario = '$usuario' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%' OR f.number LIKE '$dato%')";
	}else{
		$where = "WHERE p.fecha BETWEEN '$fechai' AND '$fechaf' AND p.estado = '$estado' AND p.usuario = '$usuario'";
	}
}

$query = "SELECT p.facturas_id AS 'facturas_id', p.pagos_id AS 'pagos_id', p.fecha AS 'fecha_pago', p.importe AS 'importe', sc.prefijo AS 'prefijo', f.number AS 'numero', CONCAT(pac.nombre,' ',pac.apellido) AS 'paciente', pac.identidad AS 'identidad', sc.relleno AS 'relleno', tp.nombre AS 'tipo_pago', p.efectivo AS 'efectivo', p.tarjeta AS 'tarjeta', tp.tipo_pago_id AS 'tipo_pago_id'
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN pacientes AS pac
	ON f.pacientes_id = pac.pacientes_id
	INNER JOIN pagos_detalles AS pd
	ON p.pagos_id = pd.pagos_id
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id	
	".$where."
	ORDER BY p.fecha DESC";
$result = $mysqli->query($query) or die($mysqli->error);

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

$registro = "SELECT p.facturas_id AS 'facturas_id', p.pagos_id AS 'pagos_id', p.fecha AS 'fecha_pago', p.importe AS 'importe', sc.prefijo AS 'prefijo', f.number AS 'numero', CONCAT(pac.nombre,' ',pac.apellido) AS 'paciente', pac.identidad AS 'identidad', sc.relleno AS 'relleno', tp.nombre AS 'tipo_pago', p.efectivo AS 'efectivo', p.tarjeta AS 'tarjeta', tp.tipo_pago_id AS 'tipo_pago_id'
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN pacientes AS pac
	ON f.pacientes_id = pac.pacientes_id
	INNER JOIN pagos_detalles AS pd
	ON p.pagos_id = pd.pagos_id
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id	
	".$where."
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2%">No.</th>
			<th width="8%">Fecha</th>
			<th width="18%">Paciente</th>
			<th width="10%">Identidad</th>			
			<th width="14%">Factura</th>
			<th width="10%">Pago Recibido</th>
			<th width="10%">Efectivo</th>
			<th width="10%">Tarjeta</th>
			<th width="16%">Tipo Pago</th>
			<th width="2%">Opciones</th>
			</tr>';
$i = 1;	
$cierre_ = "";			
while($registro2 = $result->fetch_assoc()){ 
	
	$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);

	$tipo_pago = "";
	$efectivo = 0;
	$tarjeta = 0;

	if($registro2['tipo_pago_id'] == 1){
		$efectivo = $registro2['importe'];
	}

	if($registro2['tipo_pago_id'] == 2){
		$tarjeta = $registro2['importe'];
	}

	if($registro2['tipo_pago_id'] == 6){
		$efectivo = $registro2['efectivo'];
		$tarjeta = $registro2['tarjeta'];
	}
	
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td><a style="text-decoration:none" href="javascript:invoicesDetails('.$registro2['facturas_id'].');">'.$registro2['fecha_pago'].'</a></td>	
			<td>'.$registro2['paciente'].'</td>	
			<td>'.$registro2['identidad'].'</td>				
			<td>'.$numero.'</td>
			<td>'.number_format($registro2['importe'],2).'</td>	
			<td>'.number_format($efectivo,2).'</td>	
			<td>'.number_format($tarjeta,2).'</td>	
			<td>'.$registro2['tipo_pago'].'</td>
			<td>		   
				<a style="text-decoration:none;" title = "Editar Usuario" href="javascript:editarRegistro('.$registro2['pagos_id'].');void(0);" class="fas fa-edit fa-lg"></a>
			</td>								
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="10" style="color:#C7030D">No se encontraron resultados, seleccione un profesional para verificar si hay registros almacenados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="10"><b><p ALIGN="center">Total de Registros Encontrados: '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>