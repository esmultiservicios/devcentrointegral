<?php 
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT u.id AS id, c.colaborador_id AS colaborador_id, u.username AS username, u.password AS password, u.email AS email, e.empresa_id AS empresa_id, u.type AS tipo, u.estatus AS estatus
FROM users AS u
INNER JOIN colaboradores AS c
ON u.colaborador_id = c.colaborador_id 
INNER JOIN empresa AS e
ON c.empresa_id = e.empresa_id
WHERE u.id = '$id' LIMIT 15";

$result = $mysqli->query($valores);

$valores2 = $result->fetch_assoc();

$datos = array(
				0 => $valores2['colaborador_id'], 
				1 => $valores2['username'], 
 				2 => $valores2['password'],
				3 => $valores2['email'], 
				4 => $valores2['empresa_id'], 
				5 => $valores2['tipo'], 
				6 => $valores2['estatus'], 																																																											
				);
echo json_encode($datos);
?>