<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente'];

$query = "SELECT pacientes_id 
    FROM pacientes 
	WHERE expediente = '$expediente'";
$result = $mysqli->query($query);
$consulta2 = $result->fetch_assoc();

$pacientes_id = $consulta2['pacientes_id'];

if($pacientes_id != ""){
	echo 1;
}else{
	echo 2;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>