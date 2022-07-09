<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'paciente', identidad, expediente, email
FROM pacientes
WHERE expediente != 0
ORDER BY CONCAT(nombre,' ',apellido)";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = eliminar_acentos($data);		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>