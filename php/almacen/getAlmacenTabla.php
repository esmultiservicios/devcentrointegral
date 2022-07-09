<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT a.almacen_id AS 'almacen_id', a.nombre AS 'almacen', u.nombre AS 'ubicacion'
	FROM almacen AS a
	INNER JOIN ubicacion AS u
	ON a.ubicacion_id = u.ubicacion_id
	ORDER BY a.nombre ASC";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;
}
 
echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>