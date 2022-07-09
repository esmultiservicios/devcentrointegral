<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();  

$id = $_POST['id'];
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date('Y-m-d H:i:s');	
$fecha = date('Y-m-d');	

//CONSULTAR EL MEDICO
$consulta = "SELECT colaborador_id
   FROM ausencia_medicos
   WHERE ausencia_id = '$id'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();
$colaborador_id = $consulta2['colaborador_id'];

//ELIMINO EL REGISTRO DE LA AGENDA
$delete = "DELETE FROM ausencia_medicos 
    WHERE ausencia_id = '$id'";

$query = $mysqli->query($delete);

//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
$historial_numero = historial();
$estado = "Eliminar";
$observacion = "Se se elimino la ausencia del medico";
$modulo = "Citas";
$insert = "INSERT INTO historial 
     VALUES('$historial_numero','0','0','$modulo','0','$colaborador_id','0','$fecha','$estado','$observacion','$usuario','$fecha_registro')";
$mysqli->query($insert);
/*****************************************************/	

if($query){
	echo 1;//REGISTRO COMPLETADO CON EXITO
}else{
	echo 2;//ERROR EN ELIMINAR ESTE REGISTRO
}

$mysqli->close();//CERRAR CONEXIÓN
?>