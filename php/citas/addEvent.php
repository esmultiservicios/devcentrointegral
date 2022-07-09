<?php 
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

header('Content-Type: application/json');
$usuario = $_SESSION['colaborador_id'];   

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$correlativo = "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
	FROM agenda";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
	$numero = $numero + 1;

$pacientes_id = $_POST['paciente_id'];	
$color = $_POST['color'];
$fecha_cita = $_POST['fecha_cita'];
$fecha_start = date("Y-m-d", strtotime($fecha_cita));
$fecha_cita_end = $_POST['fecha_cita_end'];	
$hora = $_POST['hora'];	
$medico = $_POST['medico'];	
$unidad = $_POST['unidad'];		
$observacion = ucwords(strtolower($_POST['obs']), " ");	
$fecha_sistema = date("Y-m-d H:i:s");
$fecha_registro = date("Y-m-d H:i:s");
$colaborador_id = $_POST['medico'];	
$fecha_consulta = date('Y-m-d');	
$servicio = $_POST['serv'];
$preclinica = 1;
	
$consultar_expediente= "SELECT expediente, CONCAT(nombre,' ',apellido) AS nombre 
	FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consultar_expediente);
$consultar_expediente1 = $result->fetch_assoc();

$expediente = $consultar_expediente1['expediente'];
$nombre = $consultar_expediente1['nombre'];	

//CONSULTA SI EL USUARIO TIENE CITA EN ESE DIA, PARA NO VOLVERLO A AGENDAR
$consultar_usuario = "SELECT a.agenda_id 
FROM agenda AS a
INNER JOIN colaboradores AS c
ON a.colaborador_id = c.colaborador_id
WHERE a.pacientes_id = '$pacientes_id' AND cast(a.fecha_cita as DATE) = '$fecha_start' AND c.puesto_id = '$unidad'";
$result = $mysqli->query($consultar_usuario);
	
$consultar_usuario1 = $result->fetch_assoc();

//CONSULTAMOS LA ULTIMA ATENCION DEL USUARIO ALMACENADA EN EL ATA PARA COMPARARLA CON LA ULTIMA DEPURACIÓN
$consulta_ultima_atencion = "SELECT atencion_id, pacientes_id, fecha
	FROM atenciones_medicas 
	WHERE pacientes_id = '$pacientes_id'
	ORDER BY fecha DESC LIMIT 1";
$result = $mysqli->query($consulta_ultima_atencion);
$consulta_ultima_atencion2 = $result->fetch_assoc();	 
$consulta_ultima_atencion_fecha = $consulta_ultima_atencion2['fecha'];	

//CONSULTA SI EL MEDICO TIENE ESPACIO EN ESE DIA Y EN ESA HORA EN ESPECIFICO PARA PODERLO AGENDAR
$consultar_medico = "SELECT agenda_id 
	 FROM agenda 
	 WHERE colaborador_id = '$medico' AND fecha_cita = '$fecha_cita' AND fecha_cita_end = '$fecha_cita_end' AND status = 0";
$result = $mysqli->query($consultar_medico);
$consultar_medico1 = $result->fetch_assoc();	
	
//CONSULTAR NOMBRE DE PROFESIONAL
$consulta_nombre_profesional = "SELECT CONCAT(nombre,' ',apellido) AS nombre 
	FROM colaboradores 
	WHERE colaborador_id = '$medico'";
$result = $mysqli->query($consulta_nombre_profesional);
$consulta_nombre_profesional2 = $result->fetch_assoc();	
$nombre_colaborador = $consulta_nombre_profesional2['nombre'];		

//CONSULTRA NOMBRE DE SERVICIO
$consulta_nombre_servicio = "SELECT nombre 
	FROM servicios 
	WHERE servicio_id = '$servicio'";
$result = $mysqli->query($consulta_nombre_servicio);
$consulta_nombre_servicio2 = $result->fetch_assoc();	
$nombre_servicio = $consulta_nombre_servicio2['nombre'];

if($pacientes_id !=0 || $usuario != 0){
if($consultar_medico1['agenda_id'] == ""){
   if($consultar_usuario1['agenda_id'] == ""){
	  
	  //CONSULTAMOS SI EL USUARIO ES NUEVO O SUBSIGUIENTE	
	  //CONSULTAR PUESTO COLABORADOR
	  $consulta_puesto = "SELECT puesto_id 
		  FROM colaboradores 
		  WHERE colaborador_id = '$colaborador_id'";
	  $result = $mysqli->query($consulta_puesto);
	  $consulta_puesto1 = $result->fetch_assoc(); 
	  $puesto_colaborador = $consulta_puesto1['puesto_id'];

	  $consultar_expediente = "SELECT a.agenda_id 
		 FROM agenda AS a 
		 INNER JOIN colaboradores AS c
		 ON a.colaborador_id = c.colaborador_id
		 WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_colaborador' AND a.servicio_id = '$servicio' AND a.status = 1";
	  $result = $mysqli->query($consultar_expediente);
	  $consultar_expediente1 = $result->fetch_assoc();  
		  
	  if ($consultar_expediente1['agenda_id']== ""){
		  $paciente = 'N';
	  }else{
		  $paciente = 'S';
	  }			
		
	if($pacientes_id != 0){		 
		 $insert = "INSERT INTO agenda 
			  VALUES('$numero', '$pacientes_id', '$expediente', '$colaborador_id', '$hora', '$fecha_cita', '$fecha_cita_end', '$fecha_registro', '0', '$color', '$observacion','$usuario','$servicio','','0','0','2','$paciente','0')";
		$query = $mysqli->query($insert);

		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Agregar";
		$observacion = "Se agendo una cita para este registro";
		$modulo = "Citas";
		$insert = "INSERT INTO historial 
			 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$colaborador_id','$servicio','$fecha_start','$estado','$observacion','$usuario','$fecha_registro')";
		$mysqli->query($insert);
		/*****************************************************/	
	}
		 
  /*********************************************************************************/
  //CONSULTA AÑO, MES y DIA DEL PACIENTE
  $nacimiento = "SELECT fecha_nacimiento AS fecha 
	  FROM pacientes 
	  WHERE pacientes_id = '$pacientes_id'";
  $result = $mysqli->query($nacimiento);

$fecha_nacimiento = ""; 
$anos = 0;
$meses = 0;	  
$dias = 0;	
	  
  if($result->num_rows>0){
	  $nacimiento2 = $result->fetch_assoc();
	  $fecha_nacimiento = $nacimiento2['fecha'];

	  $valores_array = getEdad($fecha_nacimiento);
	  $anos = $valores_array['anos'];
	  $meses = $valores_array['meses'];	  
	  $dias = $valores_array['dias'];
  }

/*********************************************************************************/			 
	if ($query){				   
		   /*LISTA DE PROGRAMACION DE CITAS*/		   
		   $correlativo_listaespera = "SELECT MAX(id) AS max, COUNT(id) AS count 
			  FROM  lista_espera";
		   $result = $mysqli->query($correlativo_listaespera);
		   $correlativo_listaespera2 = $result->fetch_assoc();

		   $numero_listaespera = $correlativo_listaespera2['max'];
		   $cantidad_listaespera = $correlativo_listaespera2['count'];

		   if ( $cantidad_listaespera == 0 )
			 $numero_listaespera = 1;
		  else
			  $numero_listaespera = $numero_listaespera + 1;	

		  if(dias_transcurridos($fecha_registro,$fecha_cita)<=15 ){
			  $prioridad = 'P';
		  }else{
			  $prioridad = 'N';
		  }
		  
		  if($pacientes_id != 0 && $servicio != 0){
			  $insert = "INSERT INTO lista_espera (id,fecha_solicitud,fecha_inclusion,pacientes_id,edad,colaborador_id,prioridad,fecha_cita,tipo_cita,reprogramo,usuario,servicio) VALUES('$numero_listaespera','$fecha_registro','$fecha_registro','$pacientes_id','$anos','$colaborador_id','$prioridad','$fecha_cita','$paciente','','$usuario','$servicio')";
			  $mysqli->query($insert);  

			  //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			  $historial_numero = historial();
			  $estado = "Agregar";
			  $observacion = "Se agrego registro de este usuario en la lista de espera";
			  $modulo = "Citas";
			  $insert = "INSERT INTO historial 
				 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$colaborador_id','$servicio','$fecha_start','$estado','$observacion','$usuario','$fecha_registro')";
			  $mysqli->query($insert);
			  /*****************************************************/						  
		  }
				
		  /**********************************************************/											
		  
		  if ($expediente == 0){
			$exp = "TEMP"; 
		  }else{
			 $exp = $expediente;
		  }					  
		  echo '{"id":"'.$numero.'","title":"'.$exp."-".$nombre.'","start":"'.$fecha_cita.'","end":"'.$fecha_cita_end.'","color":"'.$color.'"}';
	   }else{
		  echo 1;//NO SE PUEDO ALMACENAR EL REGISTRO
	   }
   }else{
	  echo 2; //ESTE USUARIO YA TIENE CITA AGENDADA EN ESE DIA
   }			
}else{
	 echo 3; //EL MÉDICO YA TIENE ESTA HORA OCUPADA
}
}else{
 echo 4; //NO SE PUEDE ALMACENAR EL REGISTRO YA QUE EL CAMBPO pacientes_id ESTA EN BLANCO POR QUE NO EXISTE EL REGISTRO
  }

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>