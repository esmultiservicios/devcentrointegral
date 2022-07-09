<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];
$comentario = $_POST['comentario'];

//OBTENEMOS LOS DATOS
$consulta = "SELECT expediente, colaborador_id, servicio_id, fecha, pacientes_id
     FROM preclinica 
	 WHERE preclinica_id = '$id'";
$result = $mysqli->query($consulta);
$consulta1 = $result->fetch_assoc();
$expediente = $consulta1['expediente'];
$colaborador_id = $consulta1['colaborador_id'];
$servicio_id = $consulta1['servicio_id'];
$fecha = $consulta1['fecha'];
$pacientes_id = $consulta1['pacientes_id'];
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];
   
//VERIFICAMOS SI EXISTE ATENCIÓN DEL USUARIO
$consulta_atencion = "SELECT atencion_id 
     FROM atenciones_medicas 
	 WHERE fecha = '$fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND pacientes_id = '$pacientes_id'";
 
$result = $mysqli->query($consulta_atencion);
$consulta_atencion1 = $result->fetch_assoc();
$atencion_id = $consulta_atencion1['atencion_id'];

//CONSULTAR AGENDA_ID
$query_agenda = "SELECT agenda_id
   FROM agenda
   WHERE CAST(fecha_cita AS DATE) = '$fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND expediente = '$expediente'";
$result = $mysqli->query($query_agenda);	   
$consulta_agenda = $result->fetch_assoc();
$agenda_id = $consulta_agenda['agenda_id'];  

//OBTENER PACIENTE_ID
$query_paciente = "SELECT pacientes_id, CONCAT(apellido,' ',nombre) AS 'paciente'
   FROM pacientes
   WHERE expediente = '$expediente'";
$result = $mysqli->query($query_paciente);
$consulta_paciente = $result->fetch_assoc();
$pacientes_id = $consulta_paciente['pacientes_id'];  
$nombre_paciente = $consulta_paciente['paciente']; 

if($atencion_id == ""){
	//ACTUALIZAMOS LA AGENDA
	$update = "UPDATE agenda SET status = '0', preclinica = '0', postclinica = '0' 
	  WHERE CAST(fecha_cita AS DATE) = '$fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND expediente = '$expediente'";
	$mysqli->query($update);

	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Actualizar";
	$observacion_historial = "Se ha actualizado el campo preclínica en la agenda para este usuario: $nombre_paciente con expediente n° $expediente";
	$modulo = "Agenda";
	$insert = "INSERT INTO historial 
	 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert);
	/*****************************************************/		 


	//ELIMINAMOS EL REGISTRO
	$delete = "DELETE FROM preclinica 
	 WHERE preclinica_id = '$id'";
	$dato = $mysqli->query($delete);

	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	$historial_numero = historial();
	$estado_historial = "Eliminar";
	$observacion_historial = "Se ha eliminado la preclínica para este usuario: $nombre_paciente con expediente n° $expediente, con el comentario: $comentario";
	$modulo = "Preclinia";
	$insert = "INSERT INTO historial 
	  VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
	$mysqli->query($insert);
	/*****************************************************/	  

	if($dato){
	 echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
	}else{
	 echo 2;//NO SE PUEDO ELIMINAR EL REGISTRO
	}  	
}else{
   echo 3;//EL USUARIO PRESENTA ATENCIONES EN EL ATA, NO SE PUEDE REALIZAR ESTA ACCIÓN
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>