<?php
session_start();   
include('../funtions.php');

//CONEXION A DB
$mysqli = connect_mysqli(); 
date_default_timezone_set('America/Tegucigalpa');
  
if ($_FILES['csv']['size'] > 0) {
$csv = $_FILES['csv']['tmp_name'];
$handle = fopen($csv,'r');

/*
  $data[0] corresponde al id_cuenta
  $data[1] corresponde a la cuenta
  $data[2] corresponde a la extensión
*/
$first = false; //Bandera que evalua cuando se llega a la primera fila del documento que se recorre.
while ($data = fgetcsv($handle,1000,",",";") ){
	if( !$first ) { //Si se llega a la primera fila se activa la bandera y no permite guardar el primer registro encontrado
	   $first = true;
	   continue;
	} 	
	
	//CONSULTAR EXISTENCIA DE EXISTENCIA
	$consultar_expediente = "SELECT pacientes_id 
		 FROM pacientes WHERE expediente = '$data[0]'");
	$result = $mysqli->query($consultar_expediente);
	
	if(mysql_num_rows($consultar_expediente)>0){
		$result = $mysqli->query($consultar_expediente2);
		$consultar_expediente2 = $result->fetch_assoc();			
		$pacientes_id = $consultar_expediente2['pacientes_id'];
		$update = "UPDATE pacientes SET status = '1' 
		   WHERE pacientes_id = '$pacientes_id'";
		$mysqli->query($update);
	}else{
		continue;
	}
}
echo 'OK';
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>