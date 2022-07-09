<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id']; 

//CONSULTAR PACIENTE ID
$consulta_paciente = "SELECT telefono1, telefono2
   FROM pacientes 
   WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_paciente);
$consulta_paciente1 = $result->fetch_assoc();

$telefono1 = "";
$telefono2 = "";

if($result->num_rows>0){
	$telefono1 = $consulta_paciente1['telefono1'];
    $telefono2 = $consulta_paciente1['telefono2'];
}

$telefono = $telefono1;

if ($telefono2 != ""){
	$telefono .= ",".$telefono2; 
}

echo $telefono;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>