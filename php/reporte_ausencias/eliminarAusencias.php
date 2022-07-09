<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];
$comentario = $_POST['comentario'];

//OBTENEMOS LOS DATOS
$consulta = "SELECT expediente, colaborador_id, servicio_id, fecha, pacientes_id, agenda_id
     FROM  ausencias
	 WHERE ausencia_id = '$id'";
$result = $mysqli->query($consulta);

$expediente = "";
$colaborador_id = "";
$servicio_id = "";
$fecha = "";
$pacientes_id = "";
$agenda_id = "";		
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id']; 
	
if($result->num_rows>0){
	$consulta1 = $result->fetch_assoc();
	
	$expediente = $consulta1['expediente'];
	$colaborador_id = $consulta1['colaborador_id'];
	$servicio_id = $consulta1['servicio_id'];
	$fecha = $consulta1['fecha'];
	$pacientes_id = $consulta1['pacientes_id'];
	$agenda_id = $consulta1['agenda_id'];	
}

//OBTENER PACIENTE_ID
$query_paciente = "SELECT CONCAT(apellido,' ',nombre) AS 'paciente'
   FROM pacientes
   WHERE expediente = '$expediente'";
$result = $mysqli->query($query_paciente);
$consulta_paciente = $result->fetch_assoc(); 
$nombre_paciente = $consulta_paciente['paciente']; 

//ELIMINAMOS LA AUSENCIA DEL PACIENTE
$delete = "DELETE FROM ausencias 
 WHERE ausencia_id = '$id'";
$query = $mysqli->query($delete);

if($query){
	echo 1;//REGISTRO ELIMINADO CORRECTAMENTE

	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Eliminar";
	$observacion_historial = "Se ha eliminado la asencia para este usuario: $nombre_paciente con expediente n° $expediente, con el comentario: $comentario";
	$modulo = "Preclinia";
	$insert = "INSERT INTO historial 
	  VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert);
	/*****************************************************/	  

	//ACTUALIZAMOS LA AGENDA
	$update = "UPDATE agenda 
		SET 
			status = '0',
			preclinica = '0'			
		WHERE CAST(fecha_cita AS DATE) = '$fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND expediente = '$expediente'";
	$mysqli->query($update);

	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Actualizar";
	$observacion_historial = "Se ha actualizado el campo estado en la agenda para este usuario: $nombre_paciente con expediente n° $expediente";
	$modulo = "Agenda";
	$insert = "INSERT INTO historial 
	 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert);
	/*****************************************************/		
}else{
	echo 2;//NO SE PUDO ELIMINAR ESTE REGISTRO
}	 

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>