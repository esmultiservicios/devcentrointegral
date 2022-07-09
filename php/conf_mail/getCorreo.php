<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id']; 

//CONSULTAR NOMBRE
$consulta_nombre = "SELECT correo
     FROM correo
	 WHERE correo_id = $id";
$result = $mysqli->query($consulta_nombre);	 
	 	  
$consulta_nombre1 = $result->fetch_assoc();
$correo = $consulta_nombre1['correo'];

echo $correo;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>