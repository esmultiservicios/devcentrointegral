<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$agenda_id = $_POST['agenda_id']; 

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT fecha_cita 
   FROM agenda 
   WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($valores);
$valores2 = $result->fetch_assoc();
$hora = date('H:i',strtotime($valores2['fecha_cita'])); 

echo $hora;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>