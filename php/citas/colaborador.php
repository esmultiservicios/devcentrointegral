<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$id = $_POST['agenda_id'];
$consulta = "SELECT colaborador_id 
   FROM agenda 
   WHERE agenda_id = '$id'"; 
$result = $mysqli->query($consulta);
  
if($result->num_rows>0){
   $consulta1 = $result->fetch_assoc();
   echo $consulta1['colaborador_id'];
}else{
	echo "Error";
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>