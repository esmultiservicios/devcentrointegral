<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$productos_id = $_POST['productos_id'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT isv 
	FROM productos 
	WHERE productos_id = '$productos_id'";
$result = $mysqli->query($consulta) or die($mysqli->error);			  

$isv = "";
if($result->num_rows>0){
	$consulta2 = $result->fetch_assoc();
	$isv = $consulta2['isv'];
}

$datos = array(
	0 => $isv,
);	

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>