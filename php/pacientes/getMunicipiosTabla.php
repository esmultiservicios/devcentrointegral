<?php 
session_start(); 
include('../funtions.php');
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$departamento = $_POST['departamento'];
$consulta = "SELECT m.municipio_id AS 'municipio_id', m.nombre AS 'municipio', d.nombre AS 'departamento'
	FROM municipios AS m
	INNER JOIN departamentos AS d
	ON m.departamento_id = d.departamento_id
	WHERE m.departamento_id = '$departamento'";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>