<?php
session_start();   
include('../funtions.php');

//CONEXION A DB
$mysqli = connect_mysqli(); 
date_default_timezone_set('America/Tegucigalpa');
  
if ($_FILES['csv']['size'] > 0) {
$csv = $_FILES['csv']['tmp_name'];
$handle = fopen($csv,'r');

$first = false; //Bandera que evalua cuando se llega a la primera fila del documento que se recorre.
while ($data = fgetcsv($handle,1000,",",";") ){
	if( !$first ) { //Si se llega a la primera fila se activa la bandera y no permite guardar el primer registro encontrado
	   $first = true;
	   continue;
	} 	
			
	//OBTENER CORRELATIVO
	$correlativo= "SELECT MAX(ata_id) AS max, COUNT(ata_id) AS count 
	   FROM ata";
	$result = $mysqli->query($correlativo);
	$correlativo2 = $result->fetch_assoc();

	$numero = $correlativo2['max'];
	$cantidad = $correlativo2['count'];

	if ($cantidad == 0 )
	   $numero = 1;
	else
	   $numero = $numero + 1;
	
	if ($data[3]=="")
		$data[3]=0;
	
	if ($data[4]=="")
		$data[4]=0;
	
	$codigo = $data[0];
	//CONSULTA DEPARTAMENTO
	$departamento_consulta = "SELECT departamento_id 
	   FROM departamentos 
	   WHERE nombre = '$data[5]'";
	$result = $mysqli->query($departamento_consulta);
	$departamento_consulta1 = $result->fetch_assoc();
	$departamento = $departamento_consulta1['departamento_id'];
	
	//CONSULTA MUNICIPIO
	$municipio_consulta = "SELECT municipio_id 
	   FROM municipios 
	   WHERE nombre = '$data[6]'";
	$result = $mysqli->query($municipio_consulta);
	$municipio_consulta1 = $result->fetch_assoc();
	$municipio = $municipio_consulta1['municipio_id'];	
	
	//CONSULTA PATOLOGIA1
	if ($data[9] == ""){
		$patologia=0;
	}else{
		$patologia_consulta = "SELECT id 
			 FROM patologia 
			 WHERE patologia_id = '$data[9]'";
		$result = $mysqli->query($patologia_consulta);
		$patologia_consulta1 = $result->fetch_assoc();
		$patologia = $patologia_consulta1['id'];						
	}	
	
	//CONSULTA PATOLOGIA2
	if ($data[10] == ""){
		$patologia1 = 0;
	}else{
	   $patologia_consulta_1 = "SELECT id 
		   FROM patologia 
		   WHERE patologia_id = '$data[10]'";
	   $result = $mysqli->query($patologia_consulta_1);
	   $patologia_consulta1_1 = $result->fetch_assoc();
	   $patologia1 = $patologia_consulta1_1['id'];							
	}

	//CONSULTA PATOLOGIA3
	if ($data[11] == ""){
		$patologia2 = 0;
	}else{
	   $patologia_consulta_2 = "SELECT id 
		   FROM patologia 
		   WHERE patologia_id = '$data[11]'";
	   $result = $mysqli->query($patologia_consulta_2);
	   $patologia_consulta1_2 = $result->fetch_assoc();
	   $patologia2 = $patologia_consulta1_2['id'];							
	}		

	//Enviadaa
	if($data[13] ==""){
		$enviadaa = " ";
	}else{
		$enviadaa = $data[13];
	}		
	
	//Recibidade
	if ($data[14] == ""){
		$recibidade = " ";
	}else{
		$recibidade = $data[14];
	}
			
	//CONSULTA SERVICIO
	$servicio_consulta = "SELECT servicio_id 
		 FROM servicios 
		 WHERE nombre = '$data[12]'";
	$result = $mysqli->query($servicio_consulta);
	$servicio_consulta1 = $result->fetch_assoc();
	$servicio = $servicio_consulta1['servicio_id'];		
	
	
	$consulta = "SELECT ata_id 
		 FROM ata 
		 WHERE expediente='$data[1]' AND colaborador_id= '$data[0]' AND fecha = '$data[15]'";
	$result = $mysqli->query($consulta);
	
	if($result->num_rows>0){
		continue;	
	}
	
 //CONSULTAR EXISTENCIA PATOLOGIA
 /*1ER PATOLOGIA*/
 $consultar_patologia1 = "SELECT expediente 
	 FROM ata 
	 WHERE patologia_id = '$data[9]' AND expediente = '$data[1]'";
 $result = $mysqli->query($consultar_patologia1);
 $consultar_patologia1_1 = $result->num_rows;

 if ($consultar_patologia1_1==0){
	$patologiaid_tipo1 = 'N';
 }else{
	$patologiaid_tipo1 = 'S';
 }

 /*2DA PATOLOGIA*/
 if($data[10] != 0){
	$consultar_patologia2 = "SELECT expediente 
		FROM ata 
		WHERE patologia_id1 = '$data[10]' AND expediente = '$data[1]'";
	$result = $mysqli->query($consultar_patologia2);
	$consultar_patologia2_1 = $result->num_rows;

	if ($consultar_patologia2_1==0){
	   $patologiaid_tipo2 = 'N';
	}else{
	   $patologiaid_tipo2 = 'S'; 
	}
 }else{
	$patologiaid_tipo2 = '';
 }
 
 /*3ER PATOLOGIA*/
 if($data[11] != 0){
	  $consultar_patologia3 = "SELECT expediente 
		  FROM ata 
		  WHERE patologia_id2 = '$data[11]' AND expediente = '$data[1]'";
	  $result = $mysqli->query($consultar_patologia3);
	  $consultar_patologia3_1 = $result->num_rows;

	 if ($consultar_patologia3_1==0){
		$patologiaid_tipo3 = 'N';
	 }else{
		$patologiaid_tipo3 = 'S';
	 }
 }else{
	 $patologiaid_tipo3 = '';
 }

  
	if ($data[0]) { 
	   $registro_guardado = "SELECT ata_id 
		   FROM ata 
		   WHERE colaborador_id = '$data[0]' AND expediente = '$data[1]' AND fecha = '$data[15]'";
	   $result = $mysqli->query($registro_guardado);
	   $registro_guardado2 = $result->num_rows;
	   
	   if ($registro_guardado2==0){
			$insert = "INSERT INTO ata 
			  VALUES('$numero','$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$departamento','$municipio','$data[7]','$data[8]','$patologia', '$patologiaid_tipo1', 
			 '$patologia1', '$patologiaid_tipo2', '$patologia2', '$patologiaid_tipo3', '$servicio','$enviadaa','$recibidade', '$data[15]', '$data[16]', '$data[17]', '$data[18]')";
			$mysqli->query($insert);				 
	   }           
	}
}
echo 'OK';
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>