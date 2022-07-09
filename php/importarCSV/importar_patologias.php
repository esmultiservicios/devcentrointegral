<?php
session_start();   
include('../funtions.php');

//CONEXION A DB
$mysqli = connect_mysqli(); 
 
date_default_timezone_set('America/Tegucigalpa');
session_start(); 
  
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
			
	//OBTENER CORRELATIVO
	$correlativo = "SELECT MAX(id) AS max, COUNT(id) AS count 
	   FROM patologia";
	$result = $mysqli->query($correlativo);
	$correlativo2 = $result->fetch_assoc();

	$numero = $correlativo2['max'];
	$cantidad = $correlativo2['count'];

	if ( $cantidad == 0 )
	  $numero = 1;
	else
	  $numero = $numero + 1;

	
	if ($data[0]) { 
		$insert = "INSERT INTO patologia 
		   VALUES('$numero','$data[0]','$data[1]')";
		$mysqli->query($insert);
	}
}
echo 'OK';
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>