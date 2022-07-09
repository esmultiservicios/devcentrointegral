<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id AS 'colaborador_id', c.nombre AS nombre, c.apellido AS 'apellido', c.identidad AS 'identidad', pc.nombre AS 'puesto', CONCAT(c.nombre,' ',c.apellido) 'colaborador'
  FROM jornada_colaboradores AS jc
  INNER JOIN colaboradores AS c
  ON jc.colaborador_id = c.colaborador_id
  INNER JOIN puesto_colaboradores AS pc
  ON c.puesto_id = pc.puesto_id
  INNER JOIN users AS u
  ON jc.colaborador_id = u.colaborador_id
  WHERE u.estatus = 1";
$result = $mysqli->query($consulta);	

while($data = $result->fetch_assoc()){				
	$arreglo["data"][] = $data;		
}

echo json_encode($arreglo);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>