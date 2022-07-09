<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

header('Content-Type: application/json');   

$fecha_registro = date('Y-m-d');
$sql = "SELECT a.agenda_id AS agenda_id, a.pacientes_id AS pacientes_id, a.expediente AS expediente, CONCAT(p.nombre,' ',p.apellido) AS nombre, a.fecha_cita AS start, a.fecha_cita_end AS end, a.color AS color 
	  FROM agenda AS a
	  INNER JOIN pacientes AS p
	  ON a.pacientes_id = p.pacientes_id
	  WHERE a.status = 0 AND cast(a.fecha_cita as date) >= '$fecha_registro'
	  ORDER BY a.pacientes_id, a.fecha_cita";	
$result = $mysqli->query($sql);		  

$events = array();

while ($row = $result->fetch_assoc()) {
	if ($row['expediente'] == 0){
		$expediente = "TEMP"; 
	}else{
		$expediente = $row['expediente'];
	}
	
	$start = explode(" ", $row['start']);
	$end = explode(" ", $row['end']);
	
	if($start[1] == '00:00:00'){
		$start = $start[0];
	}else{
		$start = $row['start'];
	}
	if($end[1] == '00:00:00'){
				$end = $end[0];
	}else{
		$end = $row['end'];
	}		
					
	$e = array();
	$e['id'] = $row['agenda_id'];
	$e['title'] = $expediente."-".$row['nombre'];
	$e['start'] = $start;
	$e['end'] = $end;
	$e['color'] = $row['color'];

	array_push($events, $e);
}     

echo json_encode($events);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN   
?>