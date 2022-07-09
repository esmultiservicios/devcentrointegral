<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_POST['colaborador_id'];
$agenda_id = $_POST['agenda_id'];
$tipo_tarifa = $_POST['tipo_tarifa'];

//OBTENER EL SERVICIO
$query_servicio = "SELECT servicio_id
   FROM agenda
   WHERE agenda_id = '$agenda_id'";
$result_servicio = $mysqli->query($query_servicio) or die($mysqli->error);
$consulta_servicio = $result_servicio->fetch_assoc();

$servicio_id = '';

if($result_servicio->num_rows>0){
    $servicio_id = $consulta_servicio['servicio_id'];
}
//OBTENER EL MONTO DE LA CONSULTA SEGUN EL COLABORADOR_ID
$consulta = "SELECT monto
    FROM tarifas
	WHERE colaborador_id = '$colaborador_id' AND tarifas_tipo_id = '$tipo_tarifa'";
$result = $mysqli->query($consulta) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$monto = '';

if($result->num_rows>0){
    $monto = $consulta2['monto'];
}

echo $monto;
 
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>