<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha = $_POST['fecha'];
$colaborador_id  = $_POST['colaborador_id'];

$consultar_puesto = "SELECT puesto_id    
    FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto);
$consultar_puesto1 = $result->fetch_assoc();
$consultar_colaborador = $consultar_puesto1['puesto_id'];

if($consultar_colaborador == 1 ){
    if(date('H:i',strtotime($fecha )) >= '13:20'){
	   $hora_ = date('Y-m-d H:i:s', strtotime('- 20 minute', strtotime($fecha)));
       $hora = date('Y-m-d H:i:s', strtotime('+ 40 minute', strtotime($hora_)));	
    }else{
      $hora = date('Y-m-d H:i:s', strtotime('+ 40 minute', strtotime($fecha)));	
    }	
}else{
	$hora = date('Y-m-d H:i:s', strtotime('+ 40 minute', strtotime($fecha)));	
}

echo $hora;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>