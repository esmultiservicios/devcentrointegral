<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$entidad = $_POST['entidad'];
$id = $_POST['id'];

//CONSULTAR REGISTROS
$consulta = "SELECT * FROM ".$entidad." 
    WHERE ".$entidad."_id = $id";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_array();
$nombre = $consulta2[1];

$datos = array(
				0 => $entidad , 
			    1 => $nombre,															
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>