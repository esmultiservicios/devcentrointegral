<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$fecha = $_POST['fecha'];
$colaborador_id = $_SESSION['colaborador_id'];
$status = 1;

$sql = "SELECT agenda_id
    FROM agenda
	WHERE pacientes_id = '$pacientes_id' AND CAST(fecha_cita AS DATE) = '$fecha' AND colaborador_id = '$colaborador_id' AND status = '$status'"; 
$result = $mysqli->query($sql) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$agenda_id = '';

if($result->num_rows>0){
	 $agenda_id = $consulta2['agenda_id'];
}	
	
echo $agenda_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>