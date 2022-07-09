<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];
$paginaActual = $_POST['partida'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$dato = $_POST['dato'];
$profesional = $_POST['profesional'];
$estado = $_POST['estado'];
$usuario = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];

if($estado == 1){
   $in = "IN(2,4)";
}else if($estado == 4){
	$in = "IN(4)";
 }else{
	$in = "IN(3)";
}

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional != ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.colaborador_id = '$profesional' AND f.estado ".$in;
	}else if($dato != ""){
		$where = "WHERE f.estado ".$in." AND (CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR f.number LIKE '$dato%')";
	}else{
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado ".$in;
	}
}else{
	if($profesional != ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.colaborador_id = '$profesional' AND f.estado ".$in." AND f.usuario = '$usuario'";
	}else if($dato != ""){
		$where = "WHERE f.estado ".$in." AND f.usuario = '$usuario' AND (CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR f.number LIKE '$dato%')";
	}else{
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado ".$in." AND f.usuario = '$usuario'";
	}
}

$query = "SELECT f.facturaS_id AS 'factura_id', f.fecha AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre,'',c.apellido) AS 'profesional', sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento', f.tipo_factura
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY f.number DESC";
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

$registro = "SELECT f.facturas_id AS 'facturas_id', f.fecha AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento', f.tipo_factura
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY f.number DESC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2.66%">No.</th>
			<th width="6.66%">Factura</th>
			<th width="6.66%">Fecha</th>
			<th width="6.66%">Identidad</th>
			<th width="10.66%">Paciente</th>			
			<th width="13.66%">Número</th>
			<th width="6.66%">Importe</th>
			<th width="6.66%">ISV</th>
			<th width="6.66%">Descuento</th>
			<th width="6.66%">Neto</th>
			<th width="6.66%">Servicio</th>
			<th width="6.66%">Profesional</th>
			<th width="2.66%">Imprimir</th>
            <th width="2.66%">Cierre</th>			
			<th width="2.66%">Revertir</th>
			</tr>';
$i = 1;	
$cierre_ = "";			
while($registro2 = $result->fetch_assoc()){ 
	$facturas_id = $registro2['facturas_id'];
	//CONSULTAR DATOS DETALLE DE Factura
	$query_detalle = "SELECT precio, descuento, cantidad, isv_valor
		FROM facturas_detalle
		WHERE facturas_id = '$facturas_id'";
	$result_detalles = $mysqli->query($query_detalle) or die($mysqli->error);
	
	$cantidad = 0;
	$descuento = 0;
	$precio = 0;
	$total_precio = 0;
	$total = 0;
	$isv_neto = 0;
	$neto_antes_isv = 0;
	
	while($registrodetalles = $result_detalles->fetch_assoc()){
			$precio += $registrodetalles["precio"];
			$cantidad += $registrodetalles["cantidad"];
			$descuento += $registrodetalles["descuento"];
			$total_precio = $registrodetalles["precio"] * $registrodetalles["cantidad"];
			$neto_antes_isv += $total_precio;
			$isv_neto += $registrodetalles["isv_valor"];	
	}
	
	$total = ($neto_antes_isv + $isv_neto) - $descuento; 
		
	if($registro2['numero'] != ""){
		$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
	}else{
		$numero = "Aún no se ha generado";
	}
	
	$cierre = $registro2['cierre'];
	
	if($cierre == 1){
		$cierre_ = '<a style="text-decoration:none; pointer-events: none; cursor: default;" data-toggle="tooltip" data-placement="right" href="#" class="fas fa-check-double fa-lg" title="La factura ha sido cerrada"></a>';
	}else{
		$cierre_ = '<a style="text-decoration:none; pointer-events: none; cursor: default;" data-toggle="tooltip" data-placement="right" href="#" class="fas fa-check fa-lg" title="No se ha cerrado la factura"></a>';		
	}
	
	
	$tabla = $tabla.'<tr>
			<td>'.$i.'</td> 
			<td>'.$registro2['tipo_documento'].'</td>
			<td><a style="text-decoration:none" href="javascript:invoicesDetails('.$registro2['facturas_id'].');">'.$registro2['fecha1'].'</a></td>	
			<td>'.$registro2['identidad'].'</td>
			<td>'.$registro2['paciente'].'</td>				
			<td>'.$numero.'</td>
            <td>'.number_format($precio,2).'</td>
            <td>'.number_format($isv_neto,2).'</td>			
			<td>'.number_format($descuento,2).'</td>
			<td>'.number_format($total,2).'</td>
            <td>'.$registro2['servicio'].'</td>
            <td>'.$registro2['profesional'].'</td>	
			<td>
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" title = "Crear Factura" href="javascript:printBill('.$registro2['facturas_id'].');void(0);" class="fas fa-print fa-lg"></a>
			</td>
            <td>'.$cierre_.'</td>				
			<td>		
			  <a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" title = "Revertir Factura" href="javascript:modal_rollback('.$registro2['facturas_id'].','.$registro2['pacientes_id'].');void(0);" class="fas fa-undo fa-lg"></a>
			</td>
			</tr>';	
			$i++;				
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="15" style="color:#C7030D">No se encontraron resultados, seleccione un profesional para verificar si hay registros almacenados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="15"><b><p ALIGN="center">Total de Registros Encontrados: '.$nroProductos.'</p></b>
   </tr>';		
}        

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>