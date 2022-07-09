<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER NOMBRE DEL PUESTO
$query_puesto = "SELECT nombre
   FROM puesto_colaboradores
   WHERE puesto_id = '$id'";
$result = $mysqli->query($query_puesto);  
$consulta_puesto = $result->fetch_assoc();
$puesto_nombre = $consulta_puesto['nombre'];    

//ELIMINAMOS EL REGISTRO
//CONSULTAMOS SI EL PUESTO HA SIDO ASIGNADO A LOS COLABORADORES
$consulta_puesto = "SELECT colaborador_id 
     FROM colaboradores
	 WHERE puesto_id = '$id'";
$result = $mysqli->query($consulta_puesto);	 

if ($result->num_rows>0){
	echo 3;//NO SE PUEDE ELIMINAR EL REGISTRO HAY VALORES ALMACENADOS
}else{
   $delete = "DELETE FROM puesto_colaboradores 
      WHERE puesto_id = '$id'";
   $query = $mysqli->query($delete);   
   
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado_historial = "Eliminar";
   $observacion_historial = "Se ha eliminado el siguiente puesto $puesto_nombre";
   $modulo = "Puestos";
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