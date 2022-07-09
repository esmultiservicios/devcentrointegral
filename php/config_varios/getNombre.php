<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id']; 
$entidad = $_POST['entidad'];

//CONSULTAR NOMBRE
$consulta_nombre = "SELECT nombre
     FROM ".$entidad."
	 WHERE ".$entidad."_id = $id";
$result = $mysqli->query($consulta_nombre);	 
	 	  
$consulta_nombre1 = $result->fetch_assoc();
$nombre = $consulta_nombre1['nombre'];

echo $nombre;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>