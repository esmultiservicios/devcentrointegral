<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$ubicacion_id  = $_POST['ubicacion_id'];

$query = "SELECT *
	FROM ubicacion
	WHERE ubicacion_id = '$ubicacion_id'";
$result = $mysqli->query($query) or die($mysqli->error);

$ubicacion = "";
$empresa_id = "";

if($result->num_rows>=0){	
	$valores2 = $result->fetch_assoc();

	$ubicacion = $valores2['nombre'];
	$empresa_id = $valores2['empresa_id'];
}

$datos = array(
	0 => $ubicacion,
	1 => $empresa_id,
);	

echo json_encode($datos);