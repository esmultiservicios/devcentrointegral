<?php 
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$colaborador_id = $_POST['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER DATOS DE LA ENTIDAD USUARIO
$query = "SELECT CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', u.username AS 'username'
   FROM users AS u
   INNER JOIN colaboradores AS c
   ON u.colaborador_id = c.colaborador_id
   WHERE u.colaborador_id = '$colaborador_id'";
$result = $mysqli->query($query);
$consulta = $result->fetch_assoc();
$colaborador = $consulta['colaborador'];
$username = $consulta['username'];

//VERIFICAMOS QUE NO EXISTAN REGISTROS DE ESTE USUARIO EN EL SISTEMA
$consultar_cita = "SELECT agenda_id
	FROM agenda
	WHERE colaborador_id = '$colaborador_id'";
$result_cita = $mysqli->query($consultar_cita);

if($result_cita->num_rows==0 ){
	//ELIMINAMOS EL REGISTRO
	if ($colaborador_id != $_SESSION['id']){ //VERIFICAMOS EL USUARIO A ELIMINAR YA QUE UNO MISMO NO SE PUEDE ELIMINAR
	   $delete = "DELETE FROM users 
		  WHERE colaborador_id = '$colaborador_id'";
	   $query = $mysqli->query($delete);
	   
	   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
	   $historial_numero = historial();
	   $estado_historial = "Eliminar";
	   $observacion_historial = "Se ha eliminado el siguiente usuario $colaborador del sistema con username $username";
	   $modulo = "Usuarios";
	   $insert = "INSERT INTO historial 
		  VALUES('$historial_numero','0','0','$modulo','$colaborador_id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
	   $mysqli->query($insert);	   
	   /********************************************/   
	   
	   if($query){
		  echo 1;//EL REGISTRO SE HA GUARDADO CORRECTAMENTE
	   }else{
		  echo 2;//ERROR EN ALMACENAR EL REGISTRO
	   }   
	}else{
		echo 3;//NO SE PUEDE ELIMINAR SU PROPIO USUARIO
	}	
}else{
	echo 4;//ESTE REGISTRO CUENTA CON INFORMACIÓN, NO SE PUEDE ELIMINAR
}
?>