<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['paciente_id'];

//CONSULTA COMENTARIO
$consulta_comentario = "SELECT comentario 
   FROM agenda 
   WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_comentario);
$consulta_comentario1 = $result->fetch_assoc();

if ($consulta_comentario1['comentario'] == ""){
	$comentario = "No hay ninguna comentario";
}else{
	$comentario = $consulta_comentario1['comentario'];
}

echo $comentario;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>