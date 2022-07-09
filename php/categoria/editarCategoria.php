<?php	
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$categoria_id  = $_POST['categoria_id'];

$query = "SELECT *
	FROM categoria
	WHERE categoria_id = '$categoria_id'";
$result = $mysqli->query($query) or die($mysqli->error);

$nombre = "";
$estado = "";

if($result->num_rows>=0){	
	$valores2 = $result->fetch_assoc();

	$nombre = $valores2['nombre'];
	$estado = $valores2['estado'];
}

$datos = array(
	0 => $nombre,
	1 => $estado,
);	

echo json_encode($datos);