<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$receta_id = $_POST['receta_id'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT CAST(fecha AS DATE) AS fecha
   FROM recetas
   WHERE receta_id = '$receta_id'";

$result = $mysqli->query($consulta) or die($mysqli->error);			  
$consulta2 = $result->fetch_assoc();
$fecha = '';

if($result->num_rows>0){
	$fecha = $consulta2['fecha'];
}

$datos = array(
	0 => $consulta2['fecha'], 				
);

echo json_encode($datos);

$mysqli->close();//CERRAR CONEXIÓN            