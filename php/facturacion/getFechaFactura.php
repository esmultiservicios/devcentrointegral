<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT fecha
   FROM facturas
   WHERE facturas_id = '$facturas_id'";

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
?>


               
			   
               