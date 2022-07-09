<?php
session_start();   
include('../funtions.php');

//CONEXION A DB
$mysqli = connect_mysqli(); 
 
date_default_timezone_set('America/Tegucigalpa');
$usuario = $_SESSION['colaborador_id'];
  
if ($_FILES['csv']['size'] > 0) {
$csv = $_FILES['csv']['tmp_name'];
$handle = fopen($csv,'r');

$first = false; //Bandera que evalua cuando se llega a la primera fila del documento que se recorre.
while ($data = fgetcsv($handle,1000,",",";") ){
	if( !$first ) { //Si se llega a la primera fila se activa la bandera y no permite guardar el primer registro encontrado
	   $first = true;
	   continue;
	} 	
	
	//CONSULTAR EXISTENCIA DE USUASUARIO
	$nombre_devuelto = explode(" ", $data[4]);
	
	if(isset($nombre_devuelto[0])){
		$apellido1 = $nombre_devuelto[0];
	}else{
		$apellido1 = "";
	}
	
	if(isset($nombre_devuelto[1])){
		$apellido2 = $nombre_devuelto[1];
	}else{
		$apellido2 = "";
	}

	if(isset($nombre_devuelto[2])){
		$nombre1 = $nombre_devuelto[2];
	}else{
		$nombre1 = "";
	}
	
	if(isset($nombre_devuelto[3])){
		$nombre2 = $nombre_devuelto[3];
	}else{
		$nombre2 = "";
	}		
				
	$fecha_depuracion = date("Y-m-d", strtotime($data[1]));
	$expediente = $data[2];
	$identidad = $data[3];		
	$nombre = $nombre1.' '.$nombre2;
	$apellido = $apellido1.' '.$apellido2;		
	$sexo = $data[5];
	$diagnostico = mb_convert_case($data[6], MB_CASE_TITLE, "UTF-8");
	$fecha_ultima_consulta = date("Y-m-d", strtotime($data[7]));
			
	$fecha_nacimiento = date('Y-m-d');
	$telefono = "";
	$telefono1 = "";
	$departamento = 0;
	$municipio = 0;
	$localidades = "";
	$responsables = "";
	$parentescos = "";
	$telefonoresp = "";
	$telefonoresp1 = "";
	$fecha_re = date('Y-m-d');
	$usuario = $_SESSION['colaborador_id'];		
	
	/**********************************/
	//ACTUALIZAR ENTIDAD DE USUARIOS DEPURADOS
	/*************************************************************************************************************************************************/
	//OBTENER CORRELATIVO USUARIOS DEPURADOS

	$correlativo_depurados = "SELECT DISTINCT MAX(depurado_id) AS max, COUNT(depurado_id) AS count 
	   FROM depurados";
	$result = $mysqli->query($correlativo_depurados);
	$correlativo_depurados2 = $result->fetch_assoc();

	$numero_depurados = $correlativo_depurados2['max'];
	$cantidad_depurados = $correlativo_depurados2['count'];

	if ( $cantidad_depurados == 0 )
	  $numero_depurados = 1;
	else
	  $numero_depurados = $numero_depurados + 1;			
	
	$fecha_depuracion = date("Y-m-d", strtotime($data[1]));
	$fecha_cita = date("Y-m-d", strtotime($data[7]));	
		
	//CONSULTAMOS LA EXISTENCIA DEL USUARIO EN LA ENTIDAD PACIENTES
	$consulta_usuario = "SELECT pacientes_id 
	   FROM pacientes 
	   WHERE expediente = '$expediente'";
	$result = $mysqli->query($consulta_usuario);
	$consulta_usuario2 = $result->fetch_assoc();
	$pacientes_id = $consulta_usuario2['pacientes_id'];	

	if($pacientes_id == ""){//SI EL USUARIO NO EXISTE SE ACTUALIZAN LOS DATOS DEL MISMO
	   $correlativo= "SELECT DISTINCT MAX(pacientes_id) AS max, COUNT(pacientes_id) AS count 
		  FROM pacientes";
	   $result = $mysqli->query($correlativo);
	   $correlativo2 = $result->fetch_assoc();

	   $numero = $correlativo2['max'];
	   $cantidad = $correlativo2['count'];

	   if ( $cantidad == 0 )
		 $numero = 1;
	   else
		  $numero = $numero + 1;
   
		$insert = "INSERT INTO pacientes 
			 VALUES('$numero', '$expediente', '$nombre', '$apellido', '$identidad', '$sexo', '$fecha_nacimiento','$telefono',
				 '$telefono1','$departamento','$municipio','$localidades','$responsables','$parentescos', '$telefonoresp', '$telefonoresp1' ,'$fecha_re','$usuario','2')";	
		$mysqli->query($insert);					 
	
		//CONSULTA EXISTENCIA DE EXPEDIENTE EN DEPURADOS
		$consulta_expediente = "SELECT depurado_id 
			FROM depurados 
			WHERE expediente = '$expediente'";
		$result = $mysqli->query($consulta_expediente);
		$consulta_expediente2 = $result->fetch_assoc();
		$depurado_id = $consulta_expediente2['depurado_id'];				
		
		if($depurado_id  == ""){
			$insert = "INSERT INTO depurados 
				VALUES('$numero_depurados','$fecha_depuracion','$numero','$expediente','$diagnostico','$fecha_cita','2','$usuario','0','0','Usuario Pasivo')";	
			$query = $mysqli->query($insert);				
		}
		
	}else{//SI EL USUARIO EXISTE SE ACTUALIZA EL ESTATUS CAMBIANDOLO A PASIVO = 2
		//CONSULTAMOS LA EXISTENCIA DEL USUARIO EN LA ENTIDAD PACIENTES
	   $consulta_usuario = "SELECT pacientes_id 
			FROM pacientes 
			WHERE expediente = '$expediente'";
	   $result = $mysqli->query($consulta_usuario);
	   $consulta_usuario2 = $result->fetch_assoc();
	   $pacientes_id = $consulta_usuario2['pacientes_id'];	
	
		$update = "UPDATE pacientes SET status = '2' 
			WHERE pacientes_id = '$pacientes_id'";
		$mysqli->query($update);	

		//CONSULTA EXISTENCIA DE EXPEDIENTE EN DEPURADOS
		$consulta_expediente = "SELECT depurado_id 
			FROM depurados 
			WHERE expediente = '$expediente'";
		$result = $mysqli->query($consulta_expediente);
		$consulta_expediente2 = $result->fetch_assoc();
		$depurado_id = $consulta_expediente2['depurado_id'];				
		
		if($depurado_id  == ""){
			$insert = "INSERT INTO depurados 
				VALUES('$numero_depurados','$fecha_depuracion','$pacientes_id','$expediente','$diagnostico','$fecha_cita','2','$usuario','0','0','Usuario Pasivo')";
			$query = $mysqli->query($insert);				
		}				
	}	 
}
echo 'OK';
fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>