<?php   
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

header('Content-Type: application/json');
$usuario = $_SESSION['colaborador_id'];   	
$agenda_id = $_POST['agenda_id'];
$status_id = $_POST['status_id'];  
$comentario = $_POST['comentario'];  
$fecha_registro = date("Y-m-d H:i:s");   
	
//OBTENER DATOS DE USUARIO
$consulta = "SELECT pacientes_id, expediente, servicio_id, CAST(fecha_cita AS DATE) AS 'fecha_cita', colaborador_id
   FROM agenda
   WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta);	
$consulta2 = $result->fetch_assoc();
$pacientes_id = $consulta2['pacientes_id'];
$expediente = $consulta2['expediente'];
$servicio_id = $consulta2['servicio_id'];
$fecha_cita = $consulta2['fecha_cita'];  
$colaborador_id = $consulta2['colaborador_id'];
	
$update = "UPDATE agenda SET status_id = '$status_id', comentario = '$comentario ' 
   WHERE agenda_id = '$agenda_id'";
$query = $mysqli->query($update);

//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
$historial_numero = historial();
$estado = "Agregar";
$observacion = "Se reprogramo la fecha de cita para este registro";
$modulo = "Citas";
$insert = "INSERT INTO historial 
	VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha_cita','$estado','$observacion','$usuario','$fecha_registro')";
$mysqli->query($insert);
/*****************************************************/	       


//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
$historial_numero = historial();
$estado = "Agregar";
$observacion = "Se agrego un nuevo estatus en la reprogramación de la cita para este registro";
$modulo = "Citas";
$insert = "INSERT INTO historial 
	VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha_cita','$estado','$observacion','$usuario','$fecha_registro')";
$mysqli->query($insert);
/*****************************************************/		   

if($query){
   echo 1;
}else{
   echo 2;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>