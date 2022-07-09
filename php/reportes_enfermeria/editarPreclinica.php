<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT pre.preclinica_id AS 'preclinica', CONCAT(p.nombre,' ',p.apellido) AS 'nombre', p.expediente AS expediente, pre.fecha AS 'fecha', p.identidad AS 'identidad', pre.pa AS 'pa', pre.fr AS 'fr', pre.fc AS 'fc', pre.t AS 'temperatura', pre.peso AS 'peso', pre.talla AS 'talla', pre.observacion AS 'observacion', CONCAT(c.nombre,' ',c.apellido) AS 'profesional' 
	FROM preclinica AS pre 
	INNER JOIN pacientes AS p 
	ON pre.expediente = p.expediente 
	INNER JOIN colaboradores AS c ON pre.colaborador_id = c.colaborador_id 
	WHERE pre.preclinica_id = '$id'";
$result = $mysqli->query($valores);	 

$expediente = "";
$fecha = "";
$identidad = "";
$nombre = "";
$pa = "";
$fr = "";
$fc = "";
$temperatura = "";
$peso = "";
$talla = "";
$observacion = "";
$profesional = "";

if($result->num_rows>0){
	$valores2 = $result->fetch_assoc();
	$expediente = $valores2['expediente'];
	$fecha = $valores2['fecha'];
	$identidad = $valores2['identidad'];
	$nombre = $valores2['nombre'];
	$pa = $valores2['pa'];
	$fr = $valores2['fr'];
	$fc = $valores2['fc'];
	$temperatura = $valores2['temperatura'];
	$peso = $valores2['peso'];
	$talla = $valores2['talla'];
	$observacion = $valores2['observacion'];
	$profesional = $valores2['profesional'];	
}
 
$datos = array(
				0 => $expediente, 
				1 => $fecha, 
 				2 => $identidad, 
				3 => $nombre,  	
				4 => $pa, 
				5 => $fr,
				6 => $fc, 
				7 => $temperatura,   
				8 => $peso, 
				9 => $talla, 
 				10 => $observacion, 
 				11 => $profesional, 				
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>