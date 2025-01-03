<?php
session_start();
include '../funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$clientes = $_POST['clientes'];
$profesional = $_POST['profesional'];
$estado = $_POST['estado'];
$usuario = $_SESSION['colaborador_id'];

$busqueda_paciente = "";
$consulta_datos = "";
$profesional_consulta = "";

if($clientes != ""){
	$busqueda_paciente = "AND f.pacientes_id = '$clientes'";
}

if($profesional != ""){
  $profesional_consulta = "AND f.colaborador_id = '$profesional'";
}

$consulta = "SELECT p.pagos_id AS 'pagos_id', p.fecha AS 'fecha_pago', p.importe AS 'importe', sc.prefijo AS 'prefijo', f.number AS 'numero', CONCAT(pac.nombre,' ',pac.apellido) AS 'paciente', pac.identidad AS 'identidad', sc.relleno AS 'relleno', tp.nombre AS 'tipo_pago', p.efectivo AS 'efectivo', p.tarjeta AS 'tarjeta', tp.tipo_pago_id AS 'tipo_pago_id', f.facturas_id
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN pacientes AS pac
	ON f.pacientes_id = pac.pacientes_id
	INNER JOIN tipo_pago AS tp
	ON p.tipo_pago = tp.tipo_pago_id
	WHERE p.estado = '$estado' AND p.fecha BETWEEN '$fechai' AND '$fechaf'
	$busqueda_paciente
	$consulta_datos
	$profesional_consulta
	ORDER BY p.fecha DESC";

$result = $mysqli->query($consulta);

$arreglo = array('data' => []);

while ($data = $result->fetch_assoc()) {
	$numero = $data['prefijo'].''.rellenarDigitos($data['numero'], $data['relleno']);

	$data['numero'] = $numero;

	// Añadir el registro completo al arreglo de datos
    $arreglo['data'][] = $data;
}

echo json_encode($arreglo);

$result->free();  // LIMPIAR RESULTADO
$mysqli->close();  // CERRAR CONEXIÓN
