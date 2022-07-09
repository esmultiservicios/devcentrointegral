<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//CONSULTAMOS LOS DATOS DEL CORREO A ELIMINAR
$consulta = "SELECT correo, server
	FROM correo
	WHERE correo_id = '$id'";
$result = $mysqli->query($consulta) or die($mysqli->error);	

$correo = "";
$servidor = "";

if($result->num_rows>0){
	$consultar_puesto1 = $result->fetch_assoc();
	$correo = $consultar_puesto1['correo'];
	$servidor = $consultar_puesto1['server'];
}

$query = "DELETE FROM correo
	WHERE correo_id = '$id'";
$query = $mysqli->query($query);

if($query){
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
   $historial_numero = historial();
   $estado_historial = "Eliminar";
   $observacion_historial = "Se ha eliminado el correo $correo, servidor: $servidor con código $id";
   $modulo = "Configurar Correo";
   $insert = "INSERT INTO historial 
	  VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	 
   /********************************************/ 		
   echo 1;
}else{
   echo 2;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>