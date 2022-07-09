<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$identidad = $_POST['identidad'];

$query = "SELECT pacientes_id 
    FROM pacientes 
	WHERE identidad = '$identidad'";
$result = $mysqli->query($query);
$consulta2 = $result->fetch_assoc();

if($consulta2['pacientes_id'] == ""){
	$pacientes_id = 0;
}else{
	$pacientes_id = $consulta2['pacientes_id'];
}

if($pacientes_id != ""){
	echo 1;
}else{
	echo 2;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>