<?php
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$preclinica_id = $_POST['preclinica_id'];

//CONSULTAMOS FECHA DE REGISTRO
$consulta = "SELECT fecha 
    FROM preclinica 
	WHERE preclinica_id = '$preclinica_id'";
$result = $mysqli->query($consulta);
$consulta1 = $result->fetch_assoc();
$fecha = $consulta1['fecha'];

echo date("Y-m-d", strtotime($fecha));

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>