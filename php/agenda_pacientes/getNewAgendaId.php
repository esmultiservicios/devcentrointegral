<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id']; 
$colaborador_id = $_POST['colaborador_id']; 
$servicio_id = $_POST['servicio_id']; 
$fecha = date("Y-m-d", strtotime($_POST['fecha']));

//OBTENER EXPEDIENTE DE USUARIO
$consulta_expediente = "SELECT expediente 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_expediente);
$consulta_expediente1 = $result->fetch_assoc();

$expediente = "";

if($result->num_rows>0){
   $expediente = $consulta_expediente1['expediente'];	
}
//CONSULTAR DATOS DE LA AGENDA NUEVA
$consulta_agenda_new = "SELECT agenda_id 
     FROM agenda 
	 WHERE expediente = '$expediente' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND CAST(fecha_cita AS DATE) = '$fecha'";
$result = $mysqli->query($consulta_agenda_new);
$consulta_agenda_new1 = $result->fetch_assoc();

$new_agenda_id = "";

if($result->num_rows>0){
    $new_agenda_id = $consulta_agenda_new1['agenda_id'];	
}

echo $new_agenda_id;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>