<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();
	
$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];
$fecha = date('Y-m-d');

//CONSULTAR NOMBRE PROFESION
$query_profesion = "SELECT nombre
   FROM profesion
   WHERE profesion_id = '$id'";
$result = $mysqli->query($query_profesion);	
$query_profesion1 = $result->fetch_assoc();
$profesion_nombre = $query_profesion1['nombre'];

$query = "SELECT pacientes_id
    FROM pacientes
	WHERE prefesion_id = '$id'";
$result = $mysqli->query($query);	
$consular_profesion2 = $result->fetch_assoc();
$pacientes_id = $consular_profesion2['pacientes_id'];

if($pacientes_id==""){
    $delete = "DELETE FROM profesion 
	    WHERE profesion_id = '$id'";
    $query = $mysqli->query($delete);	
	
    if ($query){
        //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Eliminar";
		$observacion_historial = "Se ha eliminado la profesión $profesion_nombre";
		$modulo_historial = "Profesional";
		$insert = "INSERT INTO historial 
		   VALUES('$historial_numero','$id','0','$modulo_historial','0','0','0','$fecha','$estado','$observacion_historial','$usuario','$fecha_registro')";
		$mysqli->query($insert);
		/*****************************************************/
			
		echo 1;//Registro eliminado correctamente
    }else{
		echo 2; //No se puedo eliminar el registro
    }	
}else{
	echo 3;//Este registro cuenta con información almacenada no se puede eliminar
}
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>