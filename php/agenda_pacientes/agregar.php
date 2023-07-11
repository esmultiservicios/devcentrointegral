<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();
$proceso = $_POST['pro'];

$agenda_id = $_POST['agenda_id'];
$colaborador_id = $_POST['id-registro'];
$expediente = $_POST['expediente'];
$fecha_anterior1 = $_POST['fecha_a'];
$fecha_anterior_2 = date("Y-m-d", strtotime($_POST['fecha_a']));
$nueva_fecha1 = $_POST['fecha_n'];
$observacion = cleanStringStrtolower($_POST['observacion']); 
$usuario_cambio = $_SESSION['colaborador_id'];
$fecha_cambio = date('Y-m-d H:i:s');
$hora_h = date('H:i',strtotime($_POST['hora_nueva']));
$hora_ = date('H:i',strtotime($_POST['hora_nueva']));
$nueva_fecha = date("Y-m-d H:i:s", strtotime($nueva_fecha1." ".$hora_));
$fecha_cita_end = date('Y-m-d H:i:s', strtotime('+ 60 minute', strtotime($nueva_fecha)));
$fecha_registro = date("Y-m-d H:i:s");
$color_repro = "#FF5733";
$status_repro = $_POST['status_repro'];

//CONSULTAR ESTADO DE USUSARIO 1. ACTIVO 2. PASIVO
$consulta_estado_usuario = "SELECT estado 
    FROM pacientes
	WHERE expediente = '$expediente'";
$result = $mysqli->query($consulta_estado_usuario) or die($mysqli->error);	
$consulta_estado_usuario2 = $result->fetch_assoc();

$estado_usuario = "";

if($result->num_rows>0){
	$estado_usuario = $consulta_estado_usuario2['estado'];
}
//CONSULTAR FFECHA ANTERIOR DE CITA
$consulta_fecha_anterior = "SELECT fecha_cita, usuario, status, servicio_id 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta_fecha_anterior) or die($mysqli->error);
$consulta_fecha_anterior1 = $result->fetch_assoc();

$fecha_anterior = "";
$fecha_cita_anterior = "";
$status_anterior = "";
$id_usuario = "";
$servicio_usuario = "";

if($result->num_rows>0){
	$fecha_anterior = date("Y-m-d H:i:s", strtotime($consulta_fecha_anterior1['fecha_cita']));
	$fecha_cita_anterior = date("Y-m-d", strtotime($consulta_fecha_anterior1['fecha_cita']));
	$status_anterior = $consulta_fecha_anterior1['status'];
	$id_usuario = $consulta_fecha_anterior1['usuario'];
	$servicio_usuario = $consulta_fecha_anterior1['servicio_id'];	
}
//CONSULTAR PUESTO COLABORADOR
$consultasr_puesto = "SELECT puesto_id 
    FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultasr_puesto) or die($mysqli->error);
$consultar_puesto1 = $result->fetch_assoc();

$puesto_id = "";

if($result->num_rows>0){
	$puesto_id = $consultar_puesto1['puesto_id'];
}
//CONSULTAR DATOS DE LA JORNADA Y LA CANTIDAD DE NUEVOS Y SUBSIGUIENTES EN servicios_puestos
$consultarJornada = "SELECT j_colaborador_id, nuevos, subsiguientes
    FROM jornada_colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultarJornada) or die($mysqli->error);
$consultarJornada2 = $result->fetch_assoc();

$consultarJornadaJornada_id = "";
$consultarJornadaNuevos = "";
$consultarJornadaSubsiguientes = "";
$consultaJornadaTotal = "";

if($result->num_rows>0){
	$consultarJornadaJornada_id = $consultarJornada2['j_colaborador_id'];
	$consultarJornadaNuevos = $consultarJornada2['nuevos'];
	$consultarJornadaSubsiguientes = $consultarJornada2['subsiguientes'];
	$consultaJornadaTotal = $consultarJornadaNuevos + $consultarJornadaSubsiguientes;	
}

//VERIFICAR DISPONIBILIDAD DEL MEDICO
$consulta_disponibilidad = "SELECT agenda_id 
     FROM agenda 
	 WHERE colaborador_id = '$colaborador_id' AND fecha_cita = '$nueva_fecha' AND fecha_cita_end = '$fecha_cita_end'";	
$result = $mysqli->query($consulta_disponibilidad) or die($mysqli->error);

//CONSULTAR EXPEDIENTE Y DATOS DEL USAUARIO
$consulta = "SELECT colaborador_id, expediente, pacientes_id, fecha_cita, usuario, servicio_id 
   FROM agenda WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consulta);
$consulta1 =  $result->fetch_assoc();

$expediente = "";
$pacientes_id = "";
$servicio = "";

if($result->num_rows>0){
	$expediente = $consulta1['expediente'];
	$pacientes_id = $consulta1['pacientes_id'];
	$servicio = $consulta1['servicio_id'];	
}
//CONSULTAR PUESTO DE COLABORADOR
$consultar_puesto = "SELECT puesto_id 
	 FROM colaboradores 
	 WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto);
$consultar_puesto1 = $result->fetch_assoc();

$consultar_colaborador = "";

if($result->num_rows>0){
	$consultar_colaborador = $consultar_puesto1['puesto_id'];
}
//CONSULTAR DISPONIBILIDAD PARA SABER SI EL USUARIO ES NUEVO O SUBSIGUIENTE
$consultar_expediente = "SELECT a.agenda_id 
		FROM agenda AS a
		INNER JOIN colaboradores AS c
		ON a.colaborador_id = c.colaborador_id
		WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio' AND c.puesto_id = '$consultar_colaborador' AND a.status = 1";
$result = $mysqli->query($consultar_expediente);
$consultar_expediente1 = $result->fetch_assoc(); 

$consulta_agenda_id = "";

if($result->num_rows>0){
	$consulta_agenda_id = $consultar_expediente1['agenda_id'];
}

//CONSULTAMOS LA CANTIDAD DE USUARIOS NUEVOS AGENDADOS
$consulta_nuevos = "SELECT COUNT(agenda_id) AS 'total_nuevos' 
	 FROM agenda 
	 WHERE CAST(fecha_cita AS DATE) = '$nueva_fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_usuario' AND paciente = 'N' AND status != 2";
$result = $mysqli->query($consulta_nuevos);
$consulta_nuevos1 = $result->fetch_assoc();

$consulta_nuevos_devuelto = "";

if($result->num_rows>0){
	$consulta_nuevos_devuelto = $consulta_nuevos1['total_nuevos'];
}	
  
if ($consulta_agenda_id == ""){
   $consulta_nuevos_devuelto = $consulta_nuevos_devuelto + 1;
}
	  
//CONSULTAMOS LA CANTIDAD DE USUARIOS SUBSIGUIENTES AGENDADOS
$consulta_subsiguientes = "SELECT COUNT(agenda_id) AS 'total_subsiguientes' 
	FROM agenda 
	WHERE CAST(fecha_cita AS DATE) = '$nueva_fecha' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_usuario' AND paciente = 'S'  AND status != 2";
$result = $mysqli->query($consulta_subsiguientes);
$consulta_subsiguientes1 = $result->fetch_assoc();

$consulta_subsiguientes_devuelto = "";		  

if($result->num_rows>0){
	$consulta_subsiguientes_devuelto = $consulta_subsiguientes1['total_subsiguientes'];
}

if ($consulta_agenda_id != ""){
   $consulta_subsiguientes_devuelto = $consulta_subsiguientes_devuelto + 1;
}	
		
 /*********************************************************************************/
//CONSULTA AÑO, MES y DIA DEL PACIENTE
$nacimiento = "SELECT fecha_nacimiento AS fecha 
		FROM pacientes 
		WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($nacimiento);
$nacimiento2 = $result->fetch_assoc();

$fecha_de_nacimiento = "";

if($result->num_rows>0){
	$fecha_de_nacimiento = $nacimiento2['fecha'];
}
     
//OBTENER LA EDAD DEL USUARIO 
/*********************************************************************************/
//CONSULTAR FECHA DE NACIMIENTO
$consulta_nacimiento = "SELECT fecha_nacimiento 
   FROM pacientes 
   WHERE expediente = '$expediente'";
$result = $mysqli->query($consulta_nacimiento);
$consulta_nacimiento2 = $result->fetch_assoc();

$fecha_nacimiento = "";

if($result->num_rows>0){
	$fecha_nacimiento = $consulta_nacimiento2['fecha_nacimiento'];
}

$valores_array = getEdad($fecha_de_nacimiento);
$anos = $valores_array['anos'];
$meses = $valores_array['meses'];	  
$dias = $valores_array['dias'];	
/*********************************************************************************/  
	  
//INICIO EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
$valores_array = getAgendatime($consultarJornadaJornada_id, $servicio_usuario, $puesto_id, $consulta_agenda_id, $hora_h, $consulta_nuevos_devuelto, $consultarJornadaNuevos, $consultaJornadaTotal, $consulta_subsiguientes_devuelto);
$hora = $valores_array['hora'];
$colores = $valores_array['colores'];
//FIN EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
	
//CONSULTAR PRECLINICA DEL USUARIO
$consulta_preclinica = "SELECT preclinica_id
     FROM preclinica
	 WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio' AND fecha = '$fecha_cita_anterior'";	
$result = $mysqli->query($consulta_preclinica);	 
$consulta_preclinica2 = $result->fetch_assoc();

$preclinica_usuario = "";

if($result->num_rows>0){
	$preclinica_usuario = $consulta_preclinica2['preclinica_id'];
}	 

if($hora=="Vacio" || $hora=="NuevosExcede" || $hora=="NulaSError" || $hora=="SubsiguienteExcede"){
	echo $hora;
}else{
  if($preclinica_usuario == ""){
   if($result->num_rows > 0 && $consultar_puesto1['puesto_id'] == 1){//Verifica la hora del psicologo y su disponibilidad
	  echo 1;
   }else if($result->num_rows > 0 && $consultar_puesto1['puesto_id'] == 2){//Verifica la hora del psiquiatra y su disponibilidad
	  echo 1;
   }else{
      //VERIFICAMOS LA EXISTENCIA DE LA CITA PARA ESE DIA
      $conulta_cita_ususario = "SELECT agenda_id 
	      FROM agenda 
		  WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio' AND status = 0 AND fecha_cita = '$nueva_fecha'";
	  $result = $mysqli->query($conulta_cita_ususario);
		  
  	  $conulta_cita_ususario1 = $result->fetch_assoc();
	  
	  $cita_de_usuario = "";	
	  
	  if($result->num_rows>0){
		  $cita_de_usuario = $conulta_cita_ususario1['agenda_id'];
	  }
	  
	  if($cita_de_usuario == ""){//SI NO HAY NINGUNA CITA, SE PROCEDE A ALMACENAR UNA
      //VERIFICAMOS EL PROCESO
	  //CONSULTAR PRECLINICA DEL USUARIO		 
	  $consulta_preclinica = "SELECT preclinica_id 
	       FROM preclinica 
		   WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha_anterior_2' AND servicio_id = '$servicio'";
	  $result = $mysqli->query($consulta_preclinica);
	  $consulta_preclinica1 = $result->fetch_assoc();
	  
	  $preclinica_consulta = "";

      if($result->num_rows>0){
		  $preclinica_consulta = $consulta_preclinica1['preclinica_id'];
	  }
	    
	  if($preclinica_consulta == ""){
		  //CONSULTAMOS SI EL USUARIO ES NUEVO O SUBSIGUIENTE		 
	      $consultar_expediente = "SELECT a.agenda_id 
             FROM agenda AS a 
             INNER JOIN colaboradores AS c
             ON a.colaborador_id = c.colaborador_id
             WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_id' AND a.servicio_id = '$servicio' AND a.status = 1";
			 			 		 
		  $result = $mysqli->query($consultar_expediente);
          $consultar_expediente1 = $result->fetch_assoc(); 
		  
		  $expediente_consulta_ = "";
		  
		  if($result->num_rows>0){
			  $expediente_consulta_ = $consultar_expediente1['agenda_id'];
		  }
		 
		  if ($expediente_consulta_== ""){
		     $paciente = 'N';
		  }else{
		     $paciente = 'S';
   		  }	
		  
         //OBTENER CORRELATIVO ENTIDAD AGENDA
         $correlativo_agenda= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
		      FROM agenda";
		 $result = $mysqli->query($correlativo_agenda);
         $correlativo_agenda2 = $result->fetch_assoc();
 
         $numero_agenda = 0;
         $cantidad_agenda = 0;
		 
		 if($result->num_rows>0){
			 $numero_agenda = $correlativo_agenda2['max'];
             $cantidad_agenda = $correlativo_agenda2['count'];
		 }

         if ( $cantidad_agenda == 0 )
	        $numero_agenda = 1;
         else
            $numero_agenda = $numero_agenda + 1;
		    
		 if($status_anterior == 0){//SI EL REGISTRO DE USUARIO ES PENDIENTE, SE ELIMINA LA CITA Y SE CREA UNA NUEVA CITA COMO REPROGRAMACIÓN	   
		   //OBTENER CORRELATIVO ENTIDAD AGENDA CAMBIO
           $correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count    
		       FROM agenda_cambio";
		   $result = $mysqli->query($correlativo);
           $correlativo2 = $result->fetch_assoc();

           $numero = 0;
           $cantidad = 0;
		   
		   if($result->num_rows>0){
			  $numero = $correlativo2['max'];
              $cantidad = $correlativo2['count']; 
		   }

           if ( $cantidad == 0 )
	         $numero = 1;
           else
             $numero = $numero + 1;
			   
		   if($observacion != ""){
			 $observacion1 = $observacion." (Se elimino por que se reprogramo la cita)"; 
		   }else{
		     $observacion1 = "Se elimino por que se reprogramo la cita";
		   }
			   		
           $status_agenda_cambio = "Eliminado";					
		   $insert = "INSERT INTO agenda_cambio VALUES('$numero', '$colaborador_id', '$pacientes_id', '$expediente', '$fecha_anterior', '$nueva_fecha', '$fecha_cambio', '$id_usuario','$usuario_cambio','$observacion1','$status_agenda_cambio','$fecha_registro')";
		   
		   $query = $mysqli->query($insert);
			   									
		   if($query){
		      //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		      $historial_numero = historial();
		      $estado = "Eliminar";
		      $observacion_historial = "Se ha eliminado la fecha de cita porque se realizo una reprogramación para este registro";
		      $modulo = "Agenda";
		      $insert = "INSERT INTO historial 
			       VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio','$fecha_anterior_2','$estado','$observacion_historial','$usuario_cambio','$fecha_registro')";
		      $mysqli->query($insert);
		      /*****************************************************/	
		  
			   $delete = "DELETE FROM agenda WHERE agenda_id = '$agenda_id'";
			   $mysqli->query($delete);
		   }
		}
		   
        //OBTENER CORRELATIVO ENTIDAD AGENDA CAMBIO
        $correlativo= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
		   FROM agenda_cambio";
		$result = $mysqli->query($correlativo);
        $correlativo2 = $result->fetch_assoc();

        $numero = 0;
        $cantidad = 0;
        
		if($result->num_rows>0){
			$numero = $correlativo2['max'];
            $cantidad = $correlativo2['count'];
		}
		
        if ( $cantidad == 0 )
	       $numero = 1;
        else
           $numero = $numero + 1;		   
		   
		$status_agenda_cambio = "Editado";	
		   
		$insert = "INSERT INTO agenda VALUES('$numero_agenda', '$pacientes_id', '$expediente', '$colaborador_id', '$hora' ,'$nueva_fecha', '$fecha_cita_end', '$fecha_cambio', '0', '$color_repro', '$observacion' , '$usuario_cambio', '$servicio', '','0','0','1','$paciente','$status_repro')";    
        $mysqli->query($insert);		
		
		if($status_anterior != 0){
		     $insert = "INSERT INTO agenda_cambio VALUES('$numero', '$colaborador_id', '$pacientes_id ', '$expediente', '$fecha_anterior', '$nueva_fecha', '$fecha_cambio', '$id_usuario','$usuario_cambio','$observacion','$status_agenda_cambio','$fecha_registro')";
			 $query = $mysqli->query($insert);
		}
				 
	    if($query){
		   /*LISTA DE PROGRAMACION DE CITAS*/
	       $correlativo_listaespera= "SELECT MAX(id) AS max, COUNT(id) AS count 
		      FROM  lista_espera";
		   $result = $mysqli->query($correlativo_listaespera);
           $correlativo_listaespera2 = $result->fetch_assoc();

           $numero_listaespera = 0;
           $cantidad_listaespera = 0;
		   
		   if($result->num_rows>0){
			   $numero_listaespera = $correlativo_listaespera2['max'];
               $cantidad_listaespera = $correlativo_listaespera2['count'];  
		   }

           if ( $cantidad_listaespera == 0 )
	          $numero_listaespera = 1;
           else
              $numero_listaespera = $numero_listaespera + 1;	

           if(dias_transcurridos($fecha_registro,$nueva_fecha1)<=15 ){
		      $prioridad = 'P';
  		   }else{
		      $prioridad = 'N';
		   }			  	 		
							
           $insert = "INSERT INTO lista_espera (id,fecha_solicitud,fecha_inclusion,pacientes_id,edad,colaborador_id,prioridad,fecha_cita,tipo_cita,reprogramo,usuario,servicio) 
	        VALUES('$numero_listaespera','$fecha_registro','$fecha_registro','$pacientes_id','$anos','$colaborador_id','$prioridad','$nueva_fecha1','$paciente','X','$id_usuario ','$servicio')";	
            $mysqli->query($insert);			
 		   /**********************************************************/	
		   
		   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		   $historial_numero = historial();
		   $estado = "Actualizar";
		   $observacion_historial = "Se ha editado la fecha de cita para este registro";
		   $modulo = "Agenda";
		   $insert = "INSERT INTO historial 
			     VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$colaborador_id','$servicio','$nueva_fecha1','$estado','$observacion_historial','$usuario_cambio','$fecha_registro')";
		   $mysqli->query($insert);
		  /*****************************************************/	
			
			$datos = array(
				0 => "Modificado", 
				1 => "Registro Modificado Correctamente", 
				2 => "success",
				3 => "btn-primary",
				4 => "formulario",//Nombre del Formulario que se esta trabajando
				5 => "Editar",//Accion a realizar Editar, Guardar o Eliminar
				6 => "formCita",//Nombre de Accion segun el formulario
				7 => "registrar",//Nombre de la Ventana Modal
				8 => $numero_agenda,
			);
						
	   }else{
			$datos = array(
				0 => "Error", 
				1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
	   }		
	}else{
			$datos = array(
				0 => "Error", 
				1 => "No se puedo reprogramar estre registro ya se ha hecho la preclínica", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
	}
  }else{
	$datos = array(
		0 => "Error", 
		1 => "No se puedo almacenar este registro, el paciente ya tiene cita ese día por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);
  }
 } 
 }else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este paciente ya ha realizado su preclínica no se puede reprogramar por favor validar antes de continuar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);
 } 
}

echo json_encode($datos);
	
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>