<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$entidad = $_POST['entidad'];
$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];


//CONSULTAMOS SI EL REGISTRO TIENE DATOS
$consulta = "SELECT ".$entidad."_id, nombre
   FROM ".$entidad." 
   WHERE ".$entidad."_id = '$id'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_array();
$nombre = $consulta2['nombre'];
$valor = $consulta[0];

if($valor == 0){
	$query = "DELETE FROM ".$entidad." 
	    WHERE ".$entidad."_id = '$id'";
	$query = $mysqli->query($query);

	if($query){
       //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
       $historial_numero = historial();
       $estado_historial = "Eliminar";
       $observacion_historial = "Se ha eliminado el registro $nombre en la entidad $entidad con código $id";
       $modulo = $lugar_nacimiento = mb_convert_case($entidad, MB_CASE_TITLE, "UTF-8");
       $insert = "INSERT INTO historial 
          VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
       $mysqli->query($insert);	 
       /********************************************/ 		
       echo 1;
    }else{
       echo 2;
    }
}else{
	echo 3;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>