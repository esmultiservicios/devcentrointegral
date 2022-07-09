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
	$correlativo= "SELECT MAX(pacientes_id) AS max, COUNT(pacientes_id) AS count 
	   FROM pacientes";
	$result = $mysqli->query($correlativo);
	$correlativo2 = $result->fetch_assoc();

	$numero = $correlativo2['max'];
	$cantidad = $correlativo2['count'];

	if ( $cantidad == 0 )
	  $numero = 1;
	else
	  $numero = $numero + 1;

	$nombre = ucwords(strtolower($data[1]), " "); //strtolower convierte a minuscla toda la oracion y ucwords Convierte a Mayuscula el primer caracter de una palabra
	$apellido = ucwords(strtolower($data[2]));
	$telefono = str_replace("-","",$data[6]);
	$telefono1 = str_replace("-","",$data[7]);
	
	//CONSULTA DEPARTAMENTO
	if ($data[8] == ""){
		$departamento = 0;
	}else{
	   $departamento_consulta = "SELECT departamento_id 
		   FROM departamentos 
		   WHERE nombre = '$data[8]'";
	   $result = $mysqli->query($departamento_consulta);
	   $departamento_consulta1 = $result->fetch_assoc();
	   $departamento = $departamento_consulta1['departamento_id'];						
	}

	//CONSULTA MUNICIPIO
	if ($data[9] == ""){
		$municipio = 0;
	}else{
	   $municipio_consulta = "SELECT municipio_id 
		   FROM municipios 
		   WHERE nombre = '$data[9]'";
	   $result = $mysqli->query($municipio_consulta);
	   $municipio_consulta1 = $result->fetch_assoc();
	   $municipio = $municipio_consulta1['municipio_id'];			
	}
	
	if($data[11] == ""){
		$responsable = " ";
	}else{
		$responsable = ucwords(strtolower($data[11]), " ");
	}
	
	if($data[12] == ""){
		$parentesco = " ";
	}else{
		$parentesco = ucwords(strtolower($data[12]), " ");
	}
	
	$localidad = ucwords(strtolower($data[10]), " ");
	
	$consulta = "SELECT nombre 
		 FROM pacientes 
		 WHERE expediente='$data[0]'";
	$result = $mysqli->query($consulta);
	
	if($result->num_rows>0){
		continue;	
	}
	
	if ($data[0]){ 
		$insert = "INSERT INTO pacientes VALUES('$numero','$data[0]','$nombre','$apellido','$data[3]','$data[4]','$data[5]','$telefono',
			 '$telefono1','$departamento','$municipio','$localidad','$responsable','$parentesco','$data[13]','$data[14]','$data[15]', '$data[16]', '$data[17]')";
		$mysqli->query($insert);
	}
}
echo "OK";
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>