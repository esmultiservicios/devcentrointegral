<?php
session_start();   
include "../php/funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 
$proceso = $_POST['pro'];
$agenda_id = $_POST['agenda_id'];

$comentario = cleanStringStrtolower($_POST['comentario']);
$fecha_anterior1 = $_POST['fecha_a'];
$colaborador_id = $_POST['id-registro'];
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];

//OBTENER DATOS DE USUARIO
$consulta = "SELECT pacientes_id, expediente, servicio_id
    FROM agenda
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta) or die($mysqli->error);	
$consulta2 = $result->fetch_assoc();

$pacientes_id = "";
$expediente = "";
$servicio_id = "";

if($result->num_rows>0){
	$pacientes_id = $consulta2['pacientes_id'];
	$expediente = $consulta2['expediente'];
	$servicio_id = $consulta2['servicio_id'];	
}

$update = "UPDATE agenda SET comentario = '$comentario' 
   WHERE agenda_id = '$agenda_id'";
$mysqli->query($update) or die($mysqli->error);

//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
$historial_numero = historial();
$estado = "Actualizar";
$observacion_historial = "Se ha agregado un nuevo comentario para este registro";
$modulo = "Agenda";
$insert = "INSERT INTO historial 
     VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha_registro','$estado','$observacion_historial','$usuario','$fecha_registro')";	 
$mysqli->query($insert);
/*****************************************************/		

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>