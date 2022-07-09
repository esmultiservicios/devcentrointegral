<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];

$consulta = "SELECT expediente, CONCAT(nombre,' ',apellido) AS nombre 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();  
$expediente = $consulta2['expediente'];
$nombre = $consulta2['nombre'];

if($expediente == 0){
	$valor = $nombre;
}else{
	$valor = $nombre." (".$expediente.")";
}

echo $valor;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>