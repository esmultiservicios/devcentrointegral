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
		
		$fecha_registro = date("Y-m-d H:i:s");
		$visible = 1;
		$usuario = $_SESSION['colaborador_id'];
		$numero_atenciones = correlativo("centros_id", "centros_hospitalarios");
		$centros_nombre = mb_convert_case(trim($data[4]), MB_CASE_TITLE, "UTF-8");
		
        $insert = "INSERT INTO centros_hospitalarios 
	          VALUES('$numero_atenciones', '$data[2]', '$data[3]', '$centros_nombre', '$fecha_registro', '$data[6]','$data[2]','$data[3]', '$data[5]', '$data[6]', '$visible', '$usuario')";
	    $mysqli->query($insert);
		
	}
    echo 'OK';
    fclose($handle);
  }  
?>