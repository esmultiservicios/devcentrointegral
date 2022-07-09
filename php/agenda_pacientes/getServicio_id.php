<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$agenda_id = $_POST['agenda_id']; 

//OBTENER EXPEDIENTE DE USUARIO
$consulta_datos = "SELECT servicio_id 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta_datos);
$consulta_datos1 = $result->fetch_assoc();

$servicio_id = "";

if($result->num_rows>0){
	$servicio_id = $consulta_datos1['servicio_id'];
}

echo $servicio_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>