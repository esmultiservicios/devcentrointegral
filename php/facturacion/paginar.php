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
$clientes = $_POST['clientes'];
$estado = $_POST['estado'];
$usuario = $_SESSION['colaborador_id'];
$usuario = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];

$busqueda_paciente = "";
$consulta_datos = "";

if($estado == 2 || $estado == 4){
	/*if($profesional == "" && $dato == ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND f.usuario = '$colaborador_id'";
	}else if($profesional != "" && $dato == ""){
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND f.usuario = '$colaborador_id'";
	}else if($profesional != "" && $dato != ""){
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND f.usuario = '$colaborador_id' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";
	}else if($profesional == "" && $dato != ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND f.usuario = '$colaborador_id' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";
	}else{
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND f.usuario = '$colaborador_id'";
	}*/

	if($clientes != ""){
		$busqueda_paciente = "AND f.pacientes_id = '$clientes' AND f.usuario = '$colaborador_id'";
	}

	if($dato == !""){
		$consulta_datos = "AND f.usuario = '$colaborador_id' AND (CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR f.number LIKE '$dato%' OR m.number LIKE '$dato%')";
	}
}else{
  /*if($profesional == "" && $dato == ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado'";
	}else if($profesional != "" && $dato == ""){
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado'";
	}else if($profesional != "" && $dato != ""){
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";
	}else if($profesional == "" && $dato != ""){
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.identidad LIKE '$dato%' OR p.apellido LIKE '$dato%')";
	}else{
		$where = "WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado = '$estado'";
	}*/

	if($clientes != ""){
		$busqueda_paciente = "AND f.pacientes_id = '$clientes'";
	}

	if($dato == !""){
		$consulta_datos = "AND (CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR f.number LIKE '$dato%')";
	}
}

$query = "SELECT f.facturas_id AS facturas_id, DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', f.estado AS 'estado', s.nombre AS 'consultorio', sc.prefijo AS 'prefijo', f.number AS 'numero', sc.relleno AS 'relleno'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	$busqueda_paciente
	$consulta_datos
	ORDER BY f.pacientes_id ASC";
$result = $mysqli->query($query) or die($mysqli->error);

$nroLotes = 10;
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

$registro = "SELECT f.facturas_id AS facturas_id, DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', f.estado AS 'estado', s.nombre AS 'consultorio', sc.prefijo AS 'prefijo', f.number AS 'numero', sc.relleno AS 'relleno'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	$busqueda_paciente
	$consulta_datos
	ORDER BY f.pacientes_id ASC
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro) or die($mysqli->error);

$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
			<tr>
			<th width="2.69%">No.</th>
			<th width="5.69%">Fecha</th>
			<th width="10.69%">Factura</th>
			<th width="14.69%">Cliente</th>
			<th width="7.69%">Identidad</th>
			<th width="9.69%">Profesional</th>
			<th width="7.69%">Consultorio</th>
			<th width="7.69%">Importe</th>
			<th width="7.69%">ISV</th>
			<th width="7.69%">Descuento</th>
			<th width="7.69%">Neto</th>
			<th width="5.69%">Estado</th>
			<th width="5.69%">Opciones</th>
			</tr>';
$i = 1;
while($registro2 = $result->fetch_assoc()){
	$facturas_id = $registro2['facturas_id'];
	//CONSULTAR DATOS DEL DE TALLE DE LA FACTURACION
	$query_detalle = "SELECT cantidad, precio, descuento, isv_valor
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

	if($registro2['numero'] == 0){
		$numero = "Aún no se ha generado";
	}else{
		$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
	}

	$estado = $registro2['estado'];
	$factura = "";
	$eliminar = "";
	$pay = "";
	$send_mail = "";
	$pay_credit = "";

	if($estado==1){
		$eliminar = '<a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" href="javascript:deleteBill('.$registro2['facturas_id'].');void(0);" class="fas fa-trash fa-lg" title="Eliminar Factura"></a>';
	}

	if($estado==2 || $estado==3 || $estado==4){
		$factura = '<a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" href="javascript:printBill('.$registro2['facturas_id'].');void(0);" class="fas fa-print fa-lg" title="Imprimir Factura"></a>';
	}

	if($estado == 2){
		$send_mail = '<a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" href="javascript:mailBill('.$registro2['facturas_id'].');void(0);" class="far fa-paper-plane fa-lg" title="Enviar Factura"></a>';
	}

	if($estado == 4){
		$pay_credit = '<a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" href="javascript:pago('.$registro2['facturas_id'].');void(0);" class="fab fa-amazon-pay fa-lg" title="Pagar Factura"></a>';
	}

	$estado_ = "";
	if($estado == 1){
		$estado_ = "Borrador";
	}else if($estado == 2){
		$estado_ = "Pagada";
	}else if($estado == 3){
		$estado_ = "Cancelada";
	}else if($estado == 4){
		$estado_ = "Crédito";
	}else{
		$estado_ = "";
	}

	if($estado==1){
		$pay = '<a style="text-decoration:none;" data-toggle="tooltip" data-placement="right" title = "Realizar Cobro" href="javascript:pay('.$registro2['facturas_id'].');void(0);" class="fas fa-file-invoice fa-lg"></a>';
	}

	$tabla = $tabla.'<tr>
			<td>'.$i.'</td>
			<td>'.$registro2['fecha'].'</td>
			<td>'.$numero.'</td>
			<td>'.$registro2['paciente'].'</td>
			<td>'.$registro2['identidad'].'</td>
			<td>'.$registro2['profesional'].'</td>
			<td>'.$registro2['consultorio'].'</td>
      <td>'.number_format($precio,2).'</td>
      <td>'.number_format($isv_neto,2).'</td>
			<td>'.number_format($descuento,2).'</td>
			<td>'.number_format($total,2).'</td>
			<td>'.$estado_.'</td>
			<td>
			  '.$send_mail.'
			  '.$pay_credit.'
			  '.$pay.''.$factura.'
			  '.$eliminar.'
			</td>
			</tr>';
			$i++;
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="13" style="color:#C7030D">No se encontraron resultados, seleccione un profesional para verificar si hay registros almacenados</td>
	</tr>';
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="13"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';
}

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>
