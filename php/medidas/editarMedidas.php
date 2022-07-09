<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$medida_id  = $_POST['medida_id'];

$query = "SELECT *
	FROM medida
	WHERE medida_id = '$medida_id'";
$result = $mysqli->query($query) or die($mysqli->error);

$nombre = "";
$descripcion = "";

if($result->num_rows>=0){	
	$valores2 = $result->fetch_assoc();

	$nombre = $valores2['nombre'];
	$descripcion = $valores2['descripcion'];
}

$datos = array(
	0 => $nombre,
	1 => $descripcion,
);	

echo json_encode($datos);