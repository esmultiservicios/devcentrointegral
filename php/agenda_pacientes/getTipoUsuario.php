<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id']; 
$colaborador_id = $_POST['colaborador_id'];
$servicio = $_POST['servicio_id'];
$expediente = $_POST['expediente'];
$paciente = '';

//CONSULTAR PUESTO COLABORADOR			  
$consultar_puesto = "SELECT puesto_id 
   FROM colaboradores 
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto);
$consultar_puesto1 = $result->fetch_assoc();

$consultar_colaborador_puesto_id = "";

if($result->num_rows>0){
	$consultar_colaborador_puesto_id = $consultar_puesto1['puesto_id'];
}

$consultar_expediente = "SELECT a.agenda_id AS 'agenda_id'
	FROM agenda AS a
	INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
	WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio' AND c.puesto_id = '$consultar_colaborador_puesto_id' AND a.status = 1";
$result = $mysqli->query($consultar_expediente);
$consultar_expediente1 = $result->fetch_assoc();

$consulta_agenda_id = $consultar_expediente1['agenda_id'];

if($consulta_agenda_id == ""){
	$paciente = 'N';
}else{
	$paciente = 'S';
}

if($expediente >=1 && $expediente <=14000){
	$paciente = 'S';
}

echo $paciente;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>