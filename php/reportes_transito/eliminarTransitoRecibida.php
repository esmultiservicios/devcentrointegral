<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id']; //TRANSITO RECIBIDA
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];

//OBTENER EXPEDIENTE DE TRANSITO ENVIADA
$query_transito = "SELECT pacientes_id, colaborador_id, servicio_id, fecha
   FROM transito_recibida
   WHERE transito_id = '$id'";
$result = $mysqli->query($query_transito);
$consulta_transito = $result->fetch_assoc();
$pacientes_id = $consulta_transito['pacientes_id'];  
$colaborador_id = $consulta_transito['colaborador_id']; 
$servicio_id = $consulta_transito['servicio_id']; 
$fecha = $consulta_transito['fecha'];    

//OBTENER PACIENTE_ID
$query_paciente = "SELECT expediente, CONCAT(apellido,' ',nombre) AS 'paciente'
   FROM pacientes
   WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($query_paciente);
$consulta_paciente = $result->fetch_assoc();
$expediente = $consulta_paciente['expediente'];  
$nombre_paciente = $consulta_paciente['paciente']; 

//ELIMINAMOS EL REGISTRO
   $delete = "DELETE FROM transito_recibida 
      WHERE transito_id = '$id'";
   $dato = $mysqli->query($delete);

   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado_historial = "Eliminar";
   $observacion_historial = "Se ha eliminado el transito enviado para este usuario: $nombre_paciente con expediente n° $expediente";
   $modulo = "Transito Recibida";
   $insert = "INSERT INTO historial 
      VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
   $mysqli->query($insert);
   /*****************************************************/	   
   
   if($dato){
	   echo 1;
   }else{
	   echo 2;
   }  

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN   
?>