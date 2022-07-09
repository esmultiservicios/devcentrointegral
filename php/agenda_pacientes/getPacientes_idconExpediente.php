<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente']; 

//CONSULTAR PACIENTE ID
$consulta_paciente = "SELECT pacientes_id 
    FROM pacientes 
	WHERE expediente = '$expediente'";
$result = $mysqli->query($consulta_paciente);
$consulta_paciente1 = $result->fetch_assoc();

$pacientes_id = "";

if($result->num_rows>0){
	$pacientes_id = $consulta_paciente1['pacientes_id'];
}

echo $pacientes_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>