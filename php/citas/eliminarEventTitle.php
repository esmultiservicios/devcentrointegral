<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

header('Content-Type: application/json');    

$id = $_POST['id'];
$comentario = $_POST['comentario'];
$usuario = $_SESSION['colaborador_id'];
	
$consulta_agenda = "SELECT pacientes_id, colaborador_id, expediente, fecha_cita, usuario, servicio_id, colaborador_id, DATEDIFF(NOW(), CAST(fecha_registro AS DATE)) AS 'dias_trans'
	  FROM agenda 
	  WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_agenda);
$consulta_agenda1 = $result->fetch_assoc();
	
$colaborador_id = $consulta_agenda1['colaborador_id'];
$dias_transcurridos = $consulta_agenda1['dias_trans'];
$expediente = $consulta_agenda1['expediente'];
$fecha_cita = $consulta_agenda1['fecha_cita'];
$usuario_anterior = $consulta_agenda1['usuario'];
$servicio_id = $consulta_agenda1['servicio_id'];
$colaborador_id = $consulta_agenda1['colaborador_id'];
$pacientes_id = $consulta_agenda1['pacientes_id'];	
$fecha_registro = date('Y-m-d H:i:s');	
$status = 4;

$fecha = date('Y-m-d',strtotime($consulta_agenda1['fecha_cita']));

if($comentario != ""){
	$comentario_ = "Por el usuario: ".$comentario;
}else{
	$comentario_ = "";
}	

//CORRELATIVO agenda_cambio
$correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
	FROM agenda_cambio";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
	$numero = $numero + 1;
	
$consultar_preclinica = "SELECT preclinica_id 
   FROM preclinica 
   WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND fecha = '$fecha'";
$result = $mysqli->query($consultar_preclinica);
$consultar_preclinica2 = $result->fetch_assoc();
$preclinica_id_consulta = $consultar_preclinica2['preclinica_id'];

if($dias_transcurridos <= 30){
	if($preclinica_id_consulta == ""){
		 $status_agenda_cambio = "Eliminado";

		$insert = "INSERT INTO agenda_cambio 
			  VALUES('$numero','$colaborador_id', '$pacientes_id', '$expediente','$fecha_cita','$fecha_cita','$fecha_registro','$usuario_anterior','$usuario','Se ha eliminado la cita al usuario. Usuario que elimino la cita: $usuario. $comentario_','$status_agenda_cambio','$fecha_registro')";
		$mysqli->query($insert);
		
		 //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		 $historial_numero = historial();
		 $estado = "Agregar";
		 $observacion = "Se agrego informacion de este registro en la entidad en el historial de cambio de la agenda";
		 $modulo = "Citas";
		 $insert = "INSERT INTO historial 
				 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha_cita','$estado','$observacion','$usuario','$fecha_registro')";
		 $mysqli->query($insert);
		 /*****************************************************/				

		$delete = "DELETE FROM lista_espera 
			  WHERE fecha_cita = '$fecha' AND pacientes_id = '$pacientes_id' AND servicio = '$servicio_id' AND colaborador_id = '$colaborador_id'"; 
		$mysqli->query($delete);
		
		 //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		 $historial_numero = historial();
		 $estado = "Eliminar";
		 $observacion = "Se elimino registro de la lista de espera";
		 $modulo = "Citas";
		 $insert = "INSERT INTO historial 
				 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha_cita','$estado','$observacion','$usuario','$fecha_registro')";
		 $mysqli->query($insert);
		 /*****************************************************/			
 
		$delete = "DELETE FROM agenda 
			 WHERE agenda_id = '$id'";
		//CAMBIAMOS EL ESTADO DE LA AGENDA A 3
		   
		$query = $mysqli->query($delete);			

		if ($query){
			 echo 1;//EL REGISTRO ELIMINADO CORRECTAMENTE
			 
			 //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			 $historial_numero = historial();
			 $estado = "Eliminar";
			 $observacion = "Se elimino la cita para este registroa";
			 $modulo = "Citas";
			 $insert = "INSERT INTO historial 
					 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha_cita','$estado','$observacion','$usuario','$fecha_registro')";
			 $mysqli->query($insert);
			 /*****************************************************/
		}else{
		   echo 2;//ERROR AL ELIMINAR EL REGISTRO
		}	
	}else{
		echo 3; //YA SE REALIZO LA PRECLINICA PARA ESTE USUARIO
	}
}else{
   echo 4; //NO SE PUEDE ELIMINAR ESTA CITA, EL SOBRE PASA EL TIEMPO PERMITIDO, EN TODO CASO SE LE DEBE REPROGRAMAR
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN   
?>
