<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER NOMBRE DE SERVICIO
$query_servicio = "SELECT nombre
  FROM servicios
  WHERE servicio_id = '$id'";
$result = $mysqli->query($query_servicio);  
$consulta_servicio = $result->fetch_assoc();
$servicio_nombre = $consulta_servicio['nombre'];

//CONSULTAMOS SI EL SERIVICIO TIENE ASIGNADA AGENDA
$query_servicio_agenda = "SELECT agenda_id
	FROM agenda
	WHERE servicio_id = '$id'";
$result_servicio_agenda = $mysqli->query($query_servicio_agenda); 
 
if ($result_servicio_agenda->num_rows>0){
	echo 3;//NO SE PUEDE ELIMINAR EL REGISTRO HAY VALORES ALMACENADOS
}else{
   $delete = "DELETE FROM servicios 
      WHERE servicio_id = '$id'"; 
   $query = $mysqli->query($delete); 

   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado_historial = "Eliminar";
   $observacion_historial = "Se ha eliminado el servicio: $servicio_nombre";
   $modulo = "Servicios";
   $insert = "INSERT INTO historial 
       VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	   
   /********************************************/   
   
   if($query){
	   echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
   }else{
	   echo 2;//ERROR AL ELIMINAR EL REGISTRO
   }	
}	

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN 
?>