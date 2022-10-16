<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$numeroAnterior = 0;
$numeroMaximo = 0;
$contador = 0;
$empresa_id = $_SESSION['empresa_id'];

$queryNumero = "SELECT siguiente AS 'numero'
	FROM secuencia_facturacion
	WHERE activo = 1 AND empresa_id = '$empresa_id'
	ORDER BY siguiente DESC LIMIT 1";
$resultNumero = $mysqli->query($queryNumero) or die($mysqli->error);

if($resultNumero->num_rows>0){
	$consultaNumero = $resultNumero->fetch_assoc();
	if($consultaNumero['numero'] == ""){
		$numeroAnterior = 0;
	}else{
		$numeroAnterior = $consultaNumero['numero'];
	}
}

//CONSULTAMOS EL NUMERO MAXIMO PERMITIDO
$queryNumeroMaximo = "SELECT rango_final AS 'numero'
	FROM secuencia_facturacion
	WHERE activo = 1 AND empresa_id = '$empresa_id'";
$resultNumeroMaximo = $mysqli->query($queryNumeroMaximo) or die($mysqli->error);

if($resultNumeroMaximo->num_rows>0){
	$consultaNumeroMaximo = $resultNumeroMaximo->fetch_assoc();
	$numeroMaximo = $consultaNumeroMaximo['numero'];
}

$facturasPendientes = $numeroMaximo - $numeroAnterior;

//OBTENEMOS LA FECHA LIMITE DE FACTURACION
$querFechaLimite= "SELECT DATEDIFF(fecha_limite, NOW()) AS 'dias_transcurridos', fecha_limite AS 'fecha_limite'
	FROM secuencia_facturacion
	WHERE activo = 1 AND empresa_id = '$empresa_id'";
$resultNFechaLimite = $mysqli->query($querFechaLimite) or die($mysqli->error);

if($resultNFechaLimite->num_rows>0){
	$consultaFechaLimite = $resultNFechaLimite->fetch_assoc();
	$contador = $consultaFechaLimite['dias_transcurridos'];
	$fecha_limite = $consultaFechaLimite['fecha_limite'];
}

$datos = array(
	0 => $facturasPendientes,
	1 => $contador,	
	2 => $fecha_limite,		
);

echo json_encode($datos);

$mysqli->close();//CERRAR CONEXIÓN
?>