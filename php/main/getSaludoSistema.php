<?php
include('../funtions.php');
session_start(); 
	
//CONEXION A DB
$mysqli = connect_mysqli();

date_default_timezone_set('America/Tegucigalpa');

  $usuario_sistema = $_SESSION['colaborador_id'];
  $query = "SELECT nombre AS 'nombre', apellido AS 'apellido'
      FROM colaboradores 
	  WHERE colaborador_id = '$usuario_sistema'";
  $result = $mysqli->query($query);
  
  if($result->num_rows>0){  
	  $consulta_usuario_sistema2= $result->fetch_assoc(); 
		
	  $nombre_ = explode(" ", trim(ucwords($consulta_usuario_sistema2['nombre']), " "));
	  $nombre_usuario = $nombre_[0];
	  $apellido_ = explode(" ", trim(ucwords($consulta_usuario_sistema2['apellido']), " "));	
	  $nombre_apellido = $apellido_[0];
	  
	  $usuario_sistema_nombre = $nombre_usuario." ".$nombre_apellido;
		   
	  if($usuario_sistema_nombre == ""){
		  $usuario_sistema_nombre = "HSJD";
	  }else{
		  $usuario_sistema_nombre = $usuario_sistema_nombre;
	  }  
	  
	  $hora = date("H:i:s");
	  $hora_mañana1 = date("H:i:s", strtotime("00:00:00"));
	  $hora_mañana2 = date("H:i:s", strtotime("11:59:59"));
	  $hora_tarde1 = date("H:i:s", strtotime("12:00:00"));
	  $hora_tarde2 = date("H:i:s", strtotime("19:59:59"));  
	  $hora_noche1 = date("H:i:s", strtotime("20:00:00"));
	  $hora_noche2 = date("H:i:s", strtotime("23:59:59"));    
	  $saludo = "";

	  $fecha_sistema = date("m");
	  $dia_sistema = date("d");
	  
	  if($fecha_sistema == 12 && $dia_sistema <= 25){
		 if($hora >= $hora_mañana1 && $hora <= $hora_mañana2){
			$saludo = " Feliz Navidad, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_tarde1 && $hora <= $hora_tarde2){
			 $saludo = " Feliz Navidad, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_noche1 && $hora <= $hora_noche2){
			$saludo = " Feliz Navidad, ".$usuario_sistema_nombre;
		 }	  
	  }else if($fecha_sistema == 12 && $dia_sistema >= 26){
		 if($hora >= $hora_mañana1 && $hora <= $hora_mañana2){
			$saludo = " Feliz Año Nuevo, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_tarde1 && $hora <= $hora_tarde2){
			 $saludo = " Feliz Año Nuevo, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_noche1 && $hora <= $hora_noche2){
			$saludo = " Feliz Año Nuevo, ".$usuario_sistema_nombre;
		 }	  
	  }else{
		 if($hora >= $hora_mañana1 && $hora <= $hora_mañana2){
			$saludo = "<i class='fa-solid fa-sun fa-lg'></i>&nbsp;Buenos Días, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_tarde1 && $hora <= $hora_tarde2){
			$saludo = "<i class='fa-solid fa-sun fa-lg'></i>&nbsp;Buenas Tardes, ".$usuario_sistema_nombre;
		 }else if($hora >= $hora_noche1 && $hora <= $hora_noche2){
			$saludo = "<i class='fa-solid fa-moon fa-lg'></i>&nbsp;Buenas Noches, ".$usuario_sistema_nombre;
		 }	  
	  }
	  
	  echo "$saludo"; 
	}else{
		echo "Error";
}	

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>