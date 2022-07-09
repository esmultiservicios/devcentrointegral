<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id AS 'colaborador_id', CONCAT(c.nombre,' ',c.apellido) 'colaborador'
              FROM jornada_colaboradores AS jc
              INNER JOIN colaboradores AS c
              ON jc.colaborador_id = c.colaborador_id
              INNER JOIN puesto_colaboradores AS pc
              ON c.puesto_id = pc.puesto_id
              INNER JOIN users AS u
              ON jc.colaborador_id = u.colaborador_id
			  ORDER BY jc.id 
              LIMIT 1";
$result = $mysqli->query($consulta) or die($mysqli->error);			  
$consulta2 = $result->fetch_assoc();

$colaborador_id = '';

if($result->num_rows>0){
	$colaborador_id = $consulta2['colaborador_id'];
}

$datos = array(
				0 => $consulta2['colaborador_id'],
				1 => $consulta2['colaborador'],
			  );
echo json_encode($datos);

$mysqli->close();//CERRAR CONEXIÓN
?>


               
			   
               