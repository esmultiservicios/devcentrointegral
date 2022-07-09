<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();
header('Content-Type: application/json'); 
header("Content-Type: text/html;charset=utf-8"); 

$id = $_POST['id'];
$colaborador_id = $_POST['colaborador'];
$hora_ = $_POST['hora_nueva'];
$fecha = date('Y-m-d',strtotime($_POST['fecha_citaedit']));
$fecha_cita_cambio = date('Y-m-d',strtotime($_POST['fecha_citaedit1']));   
$hora = date('H:i:s',strtotime($_POST['hora_nueva']));		
$start = date("Y-m-d H:i:s", strtotime($_POST['fecha_citaedit1']));
$hora_h = date('H:i',strtotime($start));
$end = date("Y-m-d H:i:s", strtotime($_POST['fecha_citaeditend']));	
$fecha_sistema = date("Y-m-d H:i:s");
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];
$comentario = trim(ucwords(strtolower(trim($_POST['coment'])), " ")); //strtolower convierte a minuscla toda la oracion y ucwords Convierte a Mayuscula el primer caracter de una palabra
$comentario1 = trim(ucwords(strtolower(trim($_POST['coment1'])), " ")); //strtolower convierte a minuscla toda la oracion y ucwords Convierte a Mayuscula el primer caracter de una palabra
$fecha_consulta = date('Y-m-d');
$hora = $_POST['hora_citaeditend']; 	
  
//CONSULTAR PUESTO COLABORADOR
$consulta_puesto = "SELECT puesto_id 
	 FROM colaboradores  
	 WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_puesto);
$consulta_puesto1 = $result->fetch_assoc(); 
$puesto_colaborador = $consulta_puesto1['puesto_id']; 
$consultar_colaborador_puesto_id = $consulta_puesto1['puesto_id'];

if($comentario==""){
 if ($comentario1 == "No hay ninguna observacion"){
   $comentario = "";	
 }else{
   $comentario = $comentario1;
 }		
}

//CORRELATIVO agenda_cambio
$correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
	FROM agenda_cambio";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
	$numero = $numero + 1;

$consultar_expediente = "SELECT pacientes_id, expediente, fecha_cita, usuario, servicio_id, colaborador_id 
	FROM agenda 
	WHERE agenda_id = '$id'";
$result = $mysqli->query($consultar_expediente);
$consultar_expediente1 = $result->fetch_assoc();
$expediente = $consultar_expediente1['expediente'];
$cita_anterior = $consultar_expediente1['fecha_cita'];
$fecha_cita_anterior_consulta = date('Y-m-d',strtotime($consultar_expediente1['fecha_cita']));
$nueva_cita = $consultar_expediente1['fecha_cita'];
$usuario_anterior = $consultar_expediente1['usuario'];
$pacientes_id = $consultar_expediente1['pacientes_id'];	
$servicio = $consultar_expediente1['servicio_id'];
$colaborador_id_anterior = $consultar_expediente1['colaborador_id'];

$consultar_usuario = "SELECT agenda_id 
	FROM agenda 
	WHERE pacientes_id = '$pacientes_id' AND fecha_cita = '$start' AND fecha_cita_end = '$end' AND status = 0 AND colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_usuario);	
$consultar_usuario1 = $result->fetch_assoc();

$consultar_medico = "SELECT agenda_id 
	 FROM agenda 
	 WHERE colaborador_id = '$colaborador_id' AND fecha_cita = '$start' AND fecha_cita_end = '$end' AND status = 0";
$result = $mysqli->query($consultar_medico);	 
$consultar_medico1 = $result->fetch_assoc();

//CONSULTAR DATOS DE LA JORNADA Y LA CANTIDAD DE NUEVOS Y SUBSIGUIENTES EN servicios_puestos
$consultarJornada = "SELECT j_colaborador_id, nuevos, subsiguientes 
	 FROM  jornada_colaboradores 
	 WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultarJornada);	 
$consultarJornada2 = $result->fetch_assoc();
$consultarJornadaJornada_id = $consultarJornada2['j_colaborador_id'];
$consultarJornadaNuevos = $consultarJornada2['nuevos'];
$consultarJornadaSubsiguientes = $consultarJornada2['subsiguientes'];
$consultaJornadaTotal = $consultarJornadaNuevos + $consultarJornadaSubsiguientes; 	

/*********************************************************************************/
//CONSULTA AÑO, MES y DIA DEL PACIENTE
$nacimiento = "SELECT fecha_nacimiento AS fecha 
	FROM pacientes 
	WHERE $pacientes_id = '$pacientes_id'";
$result = $mysqli->query($nacimiento);
$nacimiento2 = $result->fetch_assoc();
$fecha_nacimiento = $nacimiento2['fecha'];
 
$valores_array = getEdad($fecha_nacimiento);
$anos = $valores_array['anos'];
$meses = $valores_array['meses'];	  
$dias = $valores_array['dias'];	
/*********************************************************************************/	

if($colaborador_id <> $colaborador_id_anterior ){
	//CONSULTAR PRECLINICA DEL USUARIO		 
	$consulta_preclinica = "SELECT preclinica_id 
		 FROM preclinica 
		 WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id_anterior' AND fecha = '$fecha_cita_anterior_consulta' AND servicio_id = '$servicio'";
	$result = $mysqli->query($consulta_preclinica);
	$consulta_preclinica1 = $result->fetch_assoc();
	$preclinica_consulta = $consulta_preclinica1['preclinica_id'];						   
}else{
	//CONSULTAR PRECLINICA DEL USUARIO
	$consulta_preclinica = "SELECT preclinica_id 
		 FROM preclinica 
		 WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha_cita_anterior_consulta' AND servicio_id = '$servicio'";
	$result = $mysqli->query($consulta_preclinica);			 
	$consulta_preclinica1 = $result->fetch_assoc();
	$preclinica_consulta = $consulta_preclinica1['preclinica_id'];
}

if($preclinica_consulta == ""){
	 if ( $consultar_medico1['agenda_id'] == ""){
		if ( $consultar_usuario1['agenda_id'] == ""){
			//CONSULTAMOS SI EL USUARIO ES NUEVO O SUBSIGUIENTE		 
			$consultar_expediente = "SELECT a.agenda_id 
				FROM agenda AS a 
				INNER JOIN colaboradores AS c
				ON a.colaborador_id = c.colaborador_id
				WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_colaborador' AND a.servicio_id = '$servicio' AND a.status = 1";
			$result = $mysqli->query($consultar_expediente);	
			$consultar_expediente1 = $result->fetch_assoc(); 
			$consulta_agenda_id = $consultar_expediente1['agenda_id'];				

			if ($consulta_agenda_id == ""){
			   $paciente = 'N';
			}else{
			   $paciente = 'S';
			}	
			
		//CONSULTAR EXPEDIENTE Y DATOS DEL USAUARIO
		$consulta = "SELECT colaborador_id, expediente, pacientes_id, fecha_cita, fecha_registro, usuario, observacion, servicio_id, status 
			FROM agenda 
			WHERE agenda_id = '$id'";
		$result = $mysqli->query($consulta);
		$consulta1 =  $result->fetch_assoc();
		$usuario_anterior = $consulta1['usuario'];
		$fecha_registro_anterior = $consulta1['fecha_registro'];
		
		//CONSULTAMOS LA CANTIDAD DE USUARIOS NUEVOS AGENDADOS
		$consulta_nuevos = "SELECT COUNT(agenda_id) AS 'total_nuevos' 
			 FROM agenda 
			 WHERE CAST(fecha_cita AS DATE) = '$start' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio' AND paciente = 'N' AND status != 2";
		$result = $mysqli->query($consulta_nuevos);	 
		$consulta_nuevos1 = $result->fetch_assoc();
		$consulta_nuevos_devuelto = $consulta_nuevos1['total_nuevos'];
	  
		if($consulta_agenda_id == ""){
			$consulta_nuevos_devuelto = $consulta_nuevos_devuelto + 1;
		}
	  
		//CONSULTAMOS LA CANTIDAD DE USUARIOS SUBSIGUIENTES AGENDADOS
		$consulta_subsiguientes = "SELECT COUNT(agenda_id) AS 'total_subsiguientes' 
			FROM agenda 
			WHERE CAST(fecha_cita AS DATE) = '$start' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio' AND paciente = 'S'  AND status != 2";
		$result = $mysqli->query($consulta_subsiguientes);	
		$consulta_subsiguientes1 = $result->fetch_assoc();
		$consulta_subsiguientes_devuelto = $consulta_subsiguientes1['total_subsiguientes'];		  
	  
		if($consultar_expediente1['agenda_id'] != ""){
		   $consulta_subsiguientes_devuelto = $consulta_subsiguientes_devuelto + 1;
		}			
		
		//INICIO EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
		$valores_array = getAgendatime($consultarJornadaJornada_id, $servicio, $consultar_colaborador_puesto_id, $consulta_agenda_id, $hora_h, $consulta_nuevos_devuelto, $consultarJornadaNuevos, $consultaJornadaTotal, $consulta_subsiguientes_devuelto);
		$hora = $valores_array['hora'];
		$colores = $valores_array['colores'];
		//FIN EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
				
		$update = "UPDATE agenda 
		SET colaborador_id = '$colaborador_id', observacion = '$comentario', fecha_cita = '$start', fecha_cita_end = '$end', hora = '$hora', usuario = '$usuario', fecha_registro = '$fecha_sistema'
		WHERE agenda_id = '$id' AND preclinica = '0'";	
		$query = $mysqli->query($update);	

		//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado = "Actualizar";
		$observacion = "Se actualizo la fecha de cita o la información de la cita para este registro";
		$modulo = "Citas";
		$insert = "INSERT INTO historial 
			 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio','$start','$estado','$observacion','$usuario','$fecha_registro')";
		$mysqli->query($insert);
		/*****************************************************/				
	   
		if ($query){
		   /*LISTA DE PROGRAMACION DE CITAS*/
		   $correlativo_listaespera= "SELECT MAX(id) AS max, COUNT(id) AS count 
				FROM  lista_espera";
		   $result = $mysqli->query($correlativo_listaespera);
		   $correlativo_listaespera2 = $result->fetch_assoc();

		   $numero_listaespera = $correlativo_listaespera2['max'];
		   $cantidad_listaespera = $correlativo_listaespera2['count'];

		   if ( $cantidad_listaespera == 0 )
			  $numero_listaespera = 1;
		   else
			  $numero_listaespera = $numero_listaespera + 1;	
	  
		   if(dias_transcurridos($fecha_registro,$fecha)<=15 ){
			   $prioridad = 'P';
		   }else{
			  $prioridad = 'N';
		   } 			
						
			$insert = "INSERT INTO lista_espera (id,fecha_solicitud,fecha_inclusion,pacientes_id,edad,colaborador_id,prioridad,fecha_cita,tipo_cita,reprogramo,usuario,servicio) 
			VALUES('$numero_listaespera','$fecha_registro','$fecha_registro','$pacientes_id','$anos','$colaborador_id','$prioridad','$fecha','$paciente','X','$usuario','$servicio')";	
			$mysqli->query($insert);			   			  
			/**********************************************************/	

			//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado = "Agregar";
			$observacion = "Se agrego información a la lista de espera para este registro.";
			$modulo = "Citas";
			$insert = "INSERT INTO historial 
				  VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio','$start','$estado','$observacion','$usuario','$fecha_registro')";
			$mysqli->query($insert);
		/*****************************************************/		
		
		   $status_agenda_cambio = "Editado";
		   $insert = "INSERT INTO agenda_cambio VALUES('$numero','$colaborador_id', '$pacientes_id', '$expediente','$start','$end','$fecha_registro','$usuario_anterior','$usuario','Se le cambio la cita al usuario. Usuario que cambio la cita: $usuario. Fecha anterior: $fecha_registro_anterior. $comentario','$status_agenda_cambio','$fecha_registro')";	
		   $mysqli->query($insert);	

		   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		   $historial_numero = historial();
		   $estado = "Agregar";
		   $observacion = "Se agrego informacion de este registro en la entidad en el historial de cambio de la agenda";
		   $modulo = "Citas";
		   $insert = "INSERT INTO historial 
				VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio','$start','$estado','$observacion','$usuario','$fecha_registro')";
		   $mysqli->query($insert);
		/*****************************************************/			   
		
		   echo 1;//REGISTRO EDITADO Y/O ALMACENADO CORRECTAMENTE
		}else{
			echo 2;//ERROR AL PROCESAR ESTA SOLICITUD
		}
		}else{
			echo 3; //USUARIO YA TIENE CITA AGENDAD ESE DIA
		}			
	}else{
	   echo 4; //EL MEDICO YA TIENE LA HORA OCUPADA
	}
}else{
	echo 5; //YA SE HA PRECLINEADO ESTE USUARIO
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>
