<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

$query_consulta = "SELECT CONCAT(nombre,' ',apellido) AS 'colaborador', identidad
   FROM colaboradores
   WHERE colaborador_id = '$id'";
$result = $mysqli->query($query_consulta);
$consulta = $result->fetch_assoc();
$colaborador = $consulta['colaborador'];
$identidad = $consulta['identidad'];

//VERIFICAMOS SI EL COLABORADOR NO TIENE UN USUARIO CREADO
$query_registro = "SELECT colaborador_id
 FROM users WHERE colaborador_id = '$id'";
$result_registro = $mysqli->query($query_registro) or die($mysqli->error);

if($result_registro->num_rows==0){
   //ELIMINAMOS EL REGISTRO
   $delete = "DELETE FROM colaboradores 
      WHERE colaborador_id = '$id'";
   $query_colaboradores = $mysqli->query($delete);

   if($query_colaboradores){
      //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
      $historial_numero = historial();
      $estado_historial = "Eliminar";
      $observacion_historial = "Se ha eliminado la información para el colaborador: $colaborador con identidad n° $identidad";
      $modulo = "Colaboradores";
      $insert = "INSERT INTO historial 
         VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
      $mysqli->query($insert);	   
      /********************************************/	
      
      echo 1;//REGISTRO ALMACENADO CORRECTAMENTE
   }else{
      echo 2;//ERROR AL INTENTAR ALMACENAR ESTE REGISTRO
   }
}else{
   echo 3;//ESTE COLABORADOR TIENE ASIGNADO UN USUARIO, NO SE PUEDE ELIMINAR
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>