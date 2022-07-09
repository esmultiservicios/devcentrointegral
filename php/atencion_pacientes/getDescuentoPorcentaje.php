<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$descuento_id = $_POST['descuento_id'];
$agenda_id = $_POST['agenda_id'];

//OBTENER DATOS DE LA AGENDA DEL PACIENTE
$query_agenda = "SELECT CAST(fecha_cita AS DATE) AS 'fecha_cita', colaborador_id AS 'colaborador_id'
   FROM agenda
   WHERE agenda_id = '$agenda_id'";
$result_agenda = $mysqli->query($query_agenda) or die($mysqli->error); 
$consultaAgenda = $result_agenda->fetch_assoc();
 
$fecha_cita = '';
$colaborador_id = '';

if($result_agenda->num_rows>0){
	$fecha_cita = $consultaAgenda['fecha_cita'];
	$colaborador_id = $consultaAgenda['colaborador_id'];
}

//OBTENEMOS EL MONTO DEL DESCUENTO PARA ESTE COLABORADOR
$query_descuento = "SELECT porcentaje
     FROM descuento_profesional
	 WHERE descuento_id = '$descuento_id' AND colaborador_id = '$colaborador_id'";
$result_descuento_monto = $mysqli->query($query_descuento) or die($mysqli->error); 
$consultaDescuentoMonto = $result_descuento_monto->fetch_assoc();

$porcentaje = '';

if($result_descuento_monto->num_rows>0){
	$porcentaje = $consultaDescuentoMonto['porcentaje'];
}

echo $porcentaje;

$mysqli->close();//CERRAR CONEXIÓN
?>