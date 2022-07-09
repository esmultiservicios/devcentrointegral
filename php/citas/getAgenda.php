<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$valor = $_POST['expediente'];
$fecha_cita = $_POST['fecha_cita'];
$colaborador_id = $_POST['colaborador_id'];
$servicio_id = $_POST['servicio_id'];

//CONSULTAR AGENDA
$consulta = "SELECT a.agenda_id
FROM agenda AS a
INNER JOIN pacientes AS p
ON a.pacientes_id = p.pacientes_id
WHERE CAST(a.fecha_cita AS DATE) = '$fecha_cita' AND a.colaborador_id = '$colaborador_id' AND a.servicio_id = '$servicio_id' AND (p.identidad = '$valor' OR p.expediente = '$valor')";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();  
$agenda_id = $consulta2['agenda_id'];

echo $agenda_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>