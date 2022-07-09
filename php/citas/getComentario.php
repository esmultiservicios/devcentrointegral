<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['paciente_id'];

//CONSULTA COMENTARIO
$consulta_comentario = "SELECT observacion 
   FROM agenda WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_comentario);
$consulta_comentario1 = $result->fetch_assoc();

if ($consulta_comentario1['observacion'] == ""){
	$observacion = "No hay ninguna observacion";
}else{
	$observacion = $consulta_comentario1['observacion'];
}

echo $observacion;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>