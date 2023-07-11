<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$agenda_id = $_POST['agenda_id'];
$colaborador_id = $_POST['colaborador_id'];

//OBTENER FECHA DE LA AGENDA
$consulta_fecha = "SELECT CAST(fecha_cita AS date) AS 'fecha' 
   FROM agenda 
   WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta_fecha) or die($mysqli->error);
$consulta_fecha2 = $result->fetch_assoc();

$fecha_consulta = "";

if($result->num_rows>0){
	$fecha_consulta = $consulta_fecha2['fecha'];
}
//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$valores = "SELECT a.agenda_id AS agenda_id, a.pacientes_id AS 'pacientes_id', a.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', a.hora AS 'hora', a.fecha_cita AS 'fecha',
	   CONCAT(c.nombre, ' ', c.apellido) As doctor, p.telefono1 AS 'telefono', c.colaborador_id AS 'colaborador', a.observacion AS 'observacion', a.comentario as 'comentario', a.status_id AS 'status_id', a.servicio_id AS 'servicio_id'
       FROM agenda AS a 
       INNER JOIN pacientes AS p 
       ON a.pacientes_id = p.pacientes_id 
       INNER JOIN colaboradores AS c 
       ON a.colaborador_id = c.colaborador_id
       WHERE cast(a.fecha_cita as date) = '$fecha_consulta' AND c.colaborador_id = '$colaborador_id' AND a.agenda_id = '$agenda_id'";
$result = $mysqli->query($valores) or die($mysqli->error);	   

$valores2 = $result->fetch_assoc();
 
$fecha = date("d/m/Y H:i:s", strtotime($valores2['fecha']));
$fecha1 = date("Y-m-d", strtotime($valores2['fecha']));

$nombres = $valores2['nombre'].' '.$valores2['apellido'];
$pacientes_id = "";
$servicio_id = "";
$expediente = "";

if($result->num_rows>0){
	$pacientes_id = $valores2['pacientes_id'];
	$servicio_id = $valores2['servicio_id'];
	$expediente = $valores2['expediente'];	
}

if($valores2['expediente'] == 0){
	$expediente = "TEMP";
}

$consulta_lista = "SELECT COUNT(id) AS 'total' 
    FROM lista_espera 
	WHERE pacientes_id = '$pacientes_id' AND reprogramo = 'X'";
$result = $mysqli->query($consulta_lista) or die($mysqli->error);
$consulta_lista1 = $result->fetch_assoc();

$total = "";

if($result->num_rows>0){
	$total = $consulta_lista1['total'];
}

$datos = array(
				0 => $valores2['colaborador'], 
				1 => $expediente, 
 				2 => $nombres,
				3 => $fecha, 	
				4 => $valores2['agenda_id'], 
				5 => $valores2['comentario'],
				6 => $fecha1, 
				7 => $valores2['observacion'], 
                8 => $total,   	
                9 => $valores2['status_id'],			
				);
echo json_encode($datos);
?>