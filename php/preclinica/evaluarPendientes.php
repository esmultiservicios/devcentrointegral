<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha = date("Y-m-d");

//FECHA
$año = date("Y", strtotime($fecha));
$mes = date("m", strtotime($fecha));
$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));

$dia1 = date('d', mktime(0,0,0, $mes, 1, $año)); //PRIMER DIA DEL MES
$dia2 = date('d', mktime(0,0,0, $mes, $dia, $año)); // ULTIMO DIA DEL MES

$fecha_inicial = date("Y-m-d", strtotime($año."-".$mes."-".$dia1));
$nuevafecha = date("Y-m-d", strtotime ( '-1 day' , strtotime ( $fecha )));

$mes_acutal=nombremes(date("m", strtotime($fecha)));

$consultar_registros = "SELECT COUNT(agenda_id) AS 'total'
FROM agenda 
WHERE status = 0 and cast(fecha_cita as date) BETWEEN '$fecha_inicial' and '$nuevafecha' AND preclinica = 0";
$result = $mysqli->query($consultar_registros);

$consultar_registros2 = $result->fetch_assoc();
$total = $consultar_registros2['total'];

if($fecha == $fecha_inicial){
	$total = 0;
}

$datos = array(
				0 => $total, 
				1 => $mes_acutal,				
				);
				
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>