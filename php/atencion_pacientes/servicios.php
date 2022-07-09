<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$agenda_id = $_POST['agenda_id'];

$consulta = "SELECT servicio_id
	FROM agenda
		wWHERE agenda_id = '$agenda_id'"; 
$result = $mysqli->query($consulta) or die($mysqli->error);
$consulta2 = $result->fetch_assoc(); 
$servicio_id = $consulta2['servicio_id'];
 
echo $servicio_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>