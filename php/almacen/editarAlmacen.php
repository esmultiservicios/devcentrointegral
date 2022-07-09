<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$almacen_id  = $_POST['almacen_id'];

$query = "SELECT *
	FROM almacen
	WHERE almacen_id = '$almacen_id'";
$result = $mysqli->query($query) or die($mysqli->error);

$almacen = "";
$ubicacion = "";

if($result->num_rows>=0){	
	$valores2 = $result->fetch_assoc();

	$almacen = $valores2['nombre'];
	$ubicacion = $valores2['ubicacion_id'];
}

$datos = array(
	0 => $almacen,
	1 => $ubicacion,
);	

echo json_encode($datos);