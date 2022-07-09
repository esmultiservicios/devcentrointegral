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
			
	//OBTENER CORRELATIVO
	$correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
	   FROM agenda";
	$result = $mysqli->query($correlativo);
	$correlativo2 = $result->fetch_assoc();

	$numero = $correlativo2['max'];
	$cantidad = $correlativo2['count'];

	if ( $cantidad == 0 )
	  $numero = 1;
	else
	  $numero = $numero + 1;
  
	$consulta = "SELECT agenda_id 
		 FROM agenda 
		 WHERE expediente='$data[0]' AND fecha_cita = '$data[3]'";
	$result = $mysqli->query($consulta);
	
	if($result->num_rows>0){
		continue;	
	}
	
	//CONSULTAR PACIENTE ID
	$consulta_pacienteid = "SELECT pacientes_id 
		FROM pacientes 
		WHERE expediente = '$data[0]'";
	$result = $mysqli->query($consulta_pacienteid);
	$consulta_pacienteid1 = $result->fetch_assoc();
	
	if ($consulta_pacienteid1['pacientes_id']==""){
		$paciente_id = 0;			
	}else{
		$paciente_id = $consulta_pacienteid1['pacientes_id'];			
	}
	
	//CONSULTA SERVICIO
	$servicio_consulta = "SELECT servicio_id 
		 FROM servicios 
		 WHERE nombre = '$data[10]'";
	$result = $mysqli->query($servicio_consulta);
	$servicio_consulta1 = $result->fetch_assoc();
	$servicio = $servicio_consulta1['servicio_id'];	
	
	$hora_ = date('H:i:s',strtotime($data[2])); 
			
	$fecha_cita = date('Y-m-d H:i:s', strtotime($data[3]));
	$fecha_cita_end = date('Y-m-d H:i:s', strtotime($data[4]));
	$fecha_registro = date('Y-m-d H:i:s', strtotime($data[5]));				

	
	if ($data[0]) { 
		$insert =  "INSERT INTO agenda 
			VALUES('$numero','$paciente_id','$data[0]','$data[1]','$data[2]','$fecha_cita','$fecha_cita_end','$fecha_registro','$data[6]','$data[7]','$data[8]','$data[9]','$servicio','$data[11]')";
		$mysqli->query($insert);
		//echo "INSERT INTO agenda VALUES('$numero','$paciente_id','$data[0]','$data[1]','$data[2]','$fecha_cita','$fecha_cita_end','$fecha_registro','$data[6]','$data[7]','$data[8]','$data[9]','$servicio','$data[11]')";
	}		
}

echo 'OK';
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>