<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$transito_id = $_POST['transito_id'];

//CONSULTAMOS FECHA DE REGISTRO
$consulta = "SELECT fecha 
    FROM transito_recibida 
	WHERE transito_id = '$transito_id'";
$result = $mysqli->query($consulta);
$consulta1 = $result->fetch_assoc();
$fecha = $consulta1['fecha'];

echo $fecha;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>