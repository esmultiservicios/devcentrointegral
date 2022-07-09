<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];

//OBTENEMOS LOS VALORES DEL REGISTRO
$valores = "SELECT c.colaborador_id As 'codigo', c.nombre AS 'nombre', c.apellido AS 'apellido', c.puesto_id AS 'puesto_id', c.empresa_id AS 'empresa_id', c.identidad AS 'identidad', c.estatus AS 'estatus'
      FROM colaboradores AS c
      INNER JOIN empresa AS e
      ON c.empresa_id = e.empresa_id
      INNER JOIN puesto_colaboradores AS p
      ON c.puesto_id = p.puesto_id 
      WHERE c.colaborador_id = '$id'
	  ORDER BY c.colaborador_id ASC LIMIT 15";
$result = $mysqli->query($valores);	  

$valores2 = $result->fetch_assoc();


$datos = array(
				0 => $valores2['nombre'], 
				1 => $valores2['apellido'], 
 				2 => $valores2['empresa_id'],
				3 => $valores2['puesto_id'],
				4 => $valores2['identidad'],
				5 => $valores2['estatus'],				
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>