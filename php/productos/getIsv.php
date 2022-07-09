<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT * FROM isv";
$result = $mysqli->query($consulta) or die($mysqli->error);			  

$isv = "";

if($result->num_rows>0){
	$consulta2 = $result->fetch_assoc();
	$isv = $consulta2['nombre'];
}

$datos = array(
	0 => $isv,
);	

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>