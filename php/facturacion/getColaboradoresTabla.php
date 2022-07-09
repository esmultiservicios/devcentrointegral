<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id, CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', c.identidad AS 'identidad', pc.nombre AS 'puesto'
FROM jornada_colaboradores AS jc
INNER JOIN colaboradores AS c
ON jc.colaborador_id = c.colaborador_id
INNER JOIN puesto_colaboradores AS pc
ON c.puesto_id = pc.puesto_id
WHERE c.estatus = 1
ORDER BY CONCAT(c.nombre,' ',c.apellido)";
$result = $mysqli->query($consulta);	

$arreglo = array();

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>