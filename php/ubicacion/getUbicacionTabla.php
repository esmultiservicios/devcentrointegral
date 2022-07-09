<?php
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT u.ubicacion_id AS 'ubicacion_id', u.nombre AS 'ubicacion', e.nombre AS 'empresa'
	FROM ubicacion AS u
	INNER JOIN empresa AS e
	ON u.empresa_id = e.empresa_id
	ORDER BY u.nombre ASC";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}
 
echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>