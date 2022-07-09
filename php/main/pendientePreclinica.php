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
$nuevafecha = date("Y-m-d", strtotime ( '-1 day' , strtotime ( $fecha_sistema )));

//CONSULTAR USUARIOS
$query = "SELECT COUNT(ag.agenda_id) AS 'total'
	FROM agenda AS ag
	INNER JOIN pacientes AS p
	ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	ON c.puesto_id = pc.puesto_id
	WHERE CAST(ag.fecha_cita AS DATE) BETWEEN '$fecha_inicial' AND '$nuevafecha' AND ag.preclinica = 0";

$result = $mysqli->query($query);	 

$total = 0;

if($result->num_rows>0){
	$consulta2=$result->fetch_assoc();
	$total = $consulta2['total']; 	
} 

echo number_format($total);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>