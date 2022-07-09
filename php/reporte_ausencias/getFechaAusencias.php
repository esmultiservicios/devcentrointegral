<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$ausencia_id = $_POST['ausencia_id'];

//CONSULTAMOS FECHA DE REGISTRO
$consulta = "SELECT fecha 
    FROM ausencias 
	WHERE ausencia_id = '$ausencia_id'";
$result = $mysqli->query($consulta);
$consulta1 = $result->fetch_assoc();
$fecha = $consulta1['fecha'];

echo date("Y-m-d", strtotime($fecha));

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>