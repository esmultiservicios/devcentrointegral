<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['agenda_id'];

//CONSULTA COMENTARIO
$consulta_expediente = "SELECT expediente 
   FROM agenda 
   WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_expediente);
$consulta_expediente1 = $result->fetch_assoc();

if ($consulta_expediente1['expediente'] == 0){
	$expediente = "TEMP";
}else{
	$expediente = $consulta_expediente1['expediente'];
}

echo $expediente;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>