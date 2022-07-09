<?php
include('../funtions.php');

session_start(); 	
//CONEXION A DB
$mysqli = connect_mysqli();

date_default_timezone_set('America/Tegucigalpa');
$fecha_sistema = date("Y-m-d");

$año = date("Y", strtotime($fecha_sistema));
$mes = date("m", strtotime($fecha_sistema));
$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));

$dia1 = date('d', mktime(0,0,0, $mes, 1, $año)); //PRIMER DIA DEL MES
$dia2 = date('d', mktime(0,0,0, $mes, $dia, $año)); // ULTIMO DIA DEL MES

$fecha_inicial = date("Y-m-d", strtotime($año."-".$mes."-".$dia1));
$fecha_final = date("Y-m-d", strtotime($año."-".$mes."-".$dia2));

//CONSULTAR USUARIOS
$query = "SELECT COUNT(ausencia_id) AS 'total' 
     FROM ausencias 
	 WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final'";
$result = $mysqli->query($query);	 

$consulta2=$result->fetch_assoc();

$total = $consulta2['total'];  

echo number_format($total);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>