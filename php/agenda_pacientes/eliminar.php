<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();  

header('Content-Type: application/json');    

$id = $_POST['agenda_id_cita'];
$comentario = $_POST['comentario'];
$usuario = $_SESSION['colaborador_id'];
$status = 4;
	
$consulta_agenda = "SELECT pacientes_id, colaborador_id, expediente, fecha_cita, usuario, servicio_id, colaborador_id, DATEDIFF(NOW(), CAST(fecha_registro AS DATE)) AS 'dias_trans' 
	 FROM agenda 
	 WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_agenda) or die($mysqli->error);
$consulta_agenda1 = $result->fetch_assoc();
	
$colaborador_id = "";
$expediente = "";
$fecha_cita = "";
$usuario_anterior = "";
$servicio_id = "";
$colaborador_id = "";
$pacientes_id = "";	
$dias_transcurridos = 0;	

if($result->num_rows>0){
	$colaborador_id = $consulta_agenda1['colaborador_id'];
	$expediente = $consulta_agenda1['expediente'];
	$fecha_cita = $consulta_agenda1['fecha_cita'];
	$usuario_anterior = $consulta_agenda1['usuario'];
	$servicio_id = $consulta_agenda1['servicio_id'];
	$colaborador_id = $consulta_agenda1['colaborador_id'];
	$pacientes_id = $consulta_agenda1['pacientes_id'];	
	$dias_transcurridos = $consulta_agenda1['dias_trans'];	
}

$fecha_registro = date('Y-m-d H:i:s');

//OBTENER NOMBRE DEL Usuario
$query_consulta = "SELECT CONCAT(apellido,' ',nombre) AS 'paciente', identidad
   FROM pacientes
   WHERE pacientes_id = '$pacientes_id'";   
$result_consulta = $mysqli->query($query_consulta) or die($mysqli->error);
$consulta_usuario = $result_consulta->fetch_assoc();

$nombre_paciente = "";
$identidad = "";	

if($result_consulta->num_rows>0){
	$nombre_paciente = $consulta_usuario['paciente'];
	$identidad = $consulta_usuario['identidad'];		
}

$fecha = date('Y-m-d',strtotime($consulta_agenda1['fecha_cita']));	

if($comentario != ""){
	$comentario_ = "Por el usuario: ".$comentario;
}else{
	$comentario_ = "";
}	

$consultar_preclinica = "SELECT preclinica_id 
   FROM preclinica 
   WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND fecha = '$fecha'";
$result = $mysqli->query($consultar_preclinica) or die($mysqli->error);
$consultar_preclinica2 = $result->fetch_assoc();

$preclinica_id_consulta = "";

if($result->num_rows>0){
      $preclinica_id_consulta = $consultar_preclinica2['preclinica_id'];	
}

if($dias_transcurridos <= 30){	
	if ($preclinica_id_consulta == ""){
		 $delete = "DELETE FROM lista_espera 
			 WHERE fecha_cita = '$fecha' AND pacientes_id = '$pacientes_id' AND servicio = '$servicio_id' AND colaborador_id = '$colaborador_id'"; 
		$mysqli->query($delete) or die($mysqli->error);

		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Eliminar";
		$observacion_historial = "Se ha eliminado la lsita de espera para este registro";
		$modulo = "Agenda";
		$insert = "INSERT INTO historial 
			VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado','$observacion_historial','$usuario','$fecha_registro')";
		$mysqli->query($insert) or die($mysqli->error);
		/*****************************************************/			
	
		//CORRELATIVO agenda_cambio
		$correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
			FROM agenda_cambio";
		$result = $mysqli->query($correlativo) or die($mysqli->error);
		$correlativo2 = $result->fetch_assoc();

		$numero = $correlativo2['max'];
		$cantidad = $correlativo2['count'];

		if ( $cantidad == 0 )
		   $numero = 1;
		else
		   $numero = $numero + 1;

		$status_agenda_cambio = "Eliminado";
					 
		 $insert = "INSERT INTO agenda_cambio 
			  VALUES('$numero','$colaborador_id', '$pacientes_id', '$expediente','$fecha_cita','$fecha_cita','$fecha_registro','$usuario_anterior','$usuario','Se ha eliminado la cita al usuario. Usuario que elimino la cita: $usuario. $comentario_','$status_agenda_cambio','$fecha_registro')";
		$mysqli->query($insert) or die($mysqli->error);
		
		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Agregar";
		$observacion_historial = "Se ha guardado el historial de cambio de la agenda para este registro";
		$modulo = "Agenda";
		$insert = "INSERT INTO historial 
			VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado','$observacion_historial','$usuario','$fecha_registro')";
		$mysqli->query($insert) or die($mysqli->error);
		/*****************************************************/				
		
		$delete = "DELETE FROM agenda
			  WHERE agenda_id = '$id'";
		$query = $mysqli->query($delete) or die($mysqli->error);

		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Eliminar";
		$observacion_historial = "Se ha eliniado la agenda para este usuario $nombre_paciente con identidad numero: $identidad";
		$modulo = "Agenda";
		$insert = "INSERT INTO historial 
			VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado','$observacion_historial','$usuario','$fecha_registro')";
		$mysqli->query($insert) or die($mysqli->error);
		/*****************************************************/		
		
		if($query){
			$datos = array(
				0 => "Eliminado", 
				1 => "Registro Eliminado Correctamente", 
				2 => "success",
				3 => "btn-primary",
				4 => "form-eliminarcita",
				5 => "EliminarCita",
				6 => "formCita",
				7 => "eliminar_cita",
				8 => "",
				9 => "Eliminar", //PERMITE CERRAR EL MODAL SEGUN EL INDICADOR este indicador esta en main.js
			);
		}else{
			$datos = array(
				0 => "Error", 
				1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
		}
   }else{
		$datos = array(
			0 => "Error", 
			1 => "Lo sentimos este registro ha realizado su preclinica, por favor verificar", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",		
		);
	}
}else{
	echo 4; //NO SE PUEDE ELIMINAR ESTA CITA, EL SOBRE PASA EL TIEMPO PERMITIDO, EN TODO CASO SE LE DEBE REPROGRAMAR		
}
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$result_consulta->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>
