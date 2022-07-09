<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$usuario = $_SESSION['colaborador_id'];     
$resp = 0;

$id = $_POST['Event'][0];
$start = $_POST['Event'][1];
$end = $_POST['Event'][2];	

//CONSULTAR EXPEDIENTE Y DATOS DEL USAUARIO
$consulta = "SELECT colaborador_id, expediente, pacientes_id, CAST(fecha_cita AS DATE) AS 'fecha_cita', usuario, observacion, servicio_id, status 
   FROM agenda 
   WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta);
$consulta1 =  $result->fetch_assoc();

$agenda_id = $id;
$colaborador_id = $consulta1['colaborador_id'];
$expediente = $consulta1['expediente'];
$fecha_cita = $consulta1['fecha_cita']; //REPRESENTA LA CITA ANTERIOR DEL USUARIO
$fecha_cita_edit = date("Y-m-d", strtotime($consulta1['fecha_cita']));
$fecha_cita_nueva = date("Y-m-d", strtotime($start));
$pacientes_id = $consulta1['pacientes_id'];
$usuario_anterior = $consulta1['usuario'];
$status_anterior = $consulta1['status'];
$servicio_id = $consulta1['servicio_id'];
$observacion = $consulta1['observacion'];	
$hora_sistema = gmdate('H:i:s', time());
$fecha_sistema = date("Y-m-d");
$fecha_consulta = date('Y-m-d');
$hora_h = date('H:i',strtotime($start));
$hora_ = date('H:i',strtotime($start)); 
$color_repro = "#FF5733";	
$fecha_registro = date("Y-m-d H:i:s");

//CONSULTAR PUESTO COLABORADOR
$consulta_puesto = "SELECT puesto_id 
	 FROM colaboradores  
	 WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_puesto);
$consulta_puesto1 = $result->fetch_assoc(); 
$puesto_colaborador = $consulta_puesto1['puesto_id']; 
$consultar_colaborador_puesto_id = $consulta_puesto1['puesto_id'];
	
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
	
if($result->num_rows>0){	
    //CONSULTAR PUESTO DE COLABORADOR	
	$consultar_puesto = "SELECT puesto_id 
	    FROM colaboradores 
		WHERE colaborador_id = '$colaborador_id'";
	$result = $mysqli->query($consultar_puesto);
    $consultar_puesto1 = $result->fetch_assoc();
	$consultar_colaborador = $consultar_puesto1['puesto_id'];
	
	//CONSULTAR DISPONIBILIDAD PARA SABER SI EL USUARIO ES NUEVO O SUBSIGUIENTE
	$consultar_expediente = "SELECT a.agenda_id AS 'agenda_id'
                 FROM agenda AS a
                 INNER JOIN colaboradores AS c
	             ON a.colaborador_id = c.colaborador_id
                 WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio_id' AND c.puesto_id = '$consultar_colaborador' AND a.status = 1";
	$result = $mysqli->query($consultar_expediente);
    $consultar_expediente1 = $result->fetch_assoc(); 
	$consulta_agenda_id = $consultar_expediente1['agenda_id'];
  
	//CONSULTAMOS LA CANTIDAD DE USUARIOS NUEVOS AGENDADOS
	$consulta_nuevos = "SELECT COUNT(agenda_id) AS 'total_nuevos' 
	    FROM agenda 
		WHERE CAST(fecha_cita AS DATE) = '$fecha_cita_nueva' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND paciente = 'N' AND status != 2";
	$result = $mysqli->query($consulta_nuevos);
	
	$consulta_nuevos1 = $result->fetch_assoc();
	$consulta_nuevos_devuelto = $consulta_nuevos1['total_nuevos'];
				  
	if ($consulta_agenda_id == ""){
	   $consulta_nuevos_devuelto = $consulta_nuevos_devuelto + 1;
	}
		  
	//CONSULTAMOS LA CANTIDAD DE USUARIOS SUBSIGUIENTES AGENDADOS
	$consulta_subsiguientes = "SELECT COUNT(agenda_id) AS 'total_subsiguientes' 
	    FROM agenda 
		WHERE CAST(fecha_cita AS DATE) = '$fecha_cita_nueva' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND paciente = 'S'  AND status != 2";
	$result = $mysqli->query($consulta_subsiguientes);
	
	$consulta_subsiguientes1 = $result->fetch_assoc();
	$consulta_subsiguientes_devuelto = $consulta_subsiguientes1['total_subsiguientes'];		  
	  
	if ($consulta_agenda_id != ""){
	    $consulta_subsiguientes_devuelto = $consulta_subsiguientes_devuelto + 1;
	}

    //INICIO EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
	$valores_array = getAgendatime($consultarJornadaJornada_id, $servicio_id, $consultar_colaborador_puesto_id, $consulta_agenda_id, $hora_h, $consulta_nuevos_devuelto, $consultarJornadaNuevos, $consultaJornadaTotal, $consulta_subsiguientes_devuelto);	
	$hora = $valores_array['hora'];
	$colores = $valores_array['colores'];
	//FIN EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL
}

//CONSULTAR PACIENTE_ID
$consulta_paciente = "SELECT pacientes_id, servicio_id 
   FROM agenda 
   WHERE agenda_id = '$id'";
$result = $mysqli->query($consulta_paciente);
$consulta_paciente1 = $result->fetch_assoc();
$pacientes_id = $consulta_paciente1['pacientes_id'];
$servicio = $consulta_paciente1['servicio_id'];

$consultar_usuario = "SELECT agenda_id, servicio_id 
    FROM agenda 
	WHERE pacientes_id = '$pacientes_id' AND fecha_cita = '$start' AND fecha_cita_end = '$end' AND status = 0 AND colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_usuario);
$consultar_usuario1 = $result->fetch_assoc();	

$consultar_medico = "SELECT agenda_id 
    FROM agenda 
	WHERE colaborador_id = '$colaborador_id' AND fecha_cita = '$start' AND fecha_cita_end = '$end ' AND status = 0";
$result = $mysqli->query($consultar_medico);
$consultar_medico1 = $result->fetch_assoc();
	
if($hora=="Vacio" || $hora=="NuevosExcede" || $hora=="NulaSError" || $hora=="SubsiguienteExcede"){
	echo 5;
}else{
	//CONSULTAR PRECLINICA DEL USUARIO
	$consulta_preclinica = "SELECT preclinica_id 
	       FROM preclinica 
		   WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha_cita_edit' AND servicio_id = '$servicio_id'";
	$result = $mysqli->query($consulta_preclinica);
	$consulta_preclinica1 = $result->fetch_assoc();
	$preclinica_consulta = $consulta_preclinica1['preclinica_id'];
	
   if($preclinica_consulta  == ""){	
      if ( $consultar_medico1['agenda_id'] == ""){
	     if ( $consultar_usuario1['agenda_id'] == ""){
            //SE ACTUALIZA LA INFORMACION DEL CAMBIO EN LA AGENDA
		  
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
               WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_colaborador' AND a.servicio_id = '$servicio_id' AND a.status = 1";
			$result = $mysqli->query($consultar_expediente);   
            $consultar_expediente1 = $result->fetch_assoc(); 
			  
		    if ($consultar_expediente1['agenda_id']== ""){
		       $paciente = 'N';
		    }else{
		       $paciente = 'S';
		    }			  				
              
		    if($status_anterior == 0){			
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
			   
			   if($observacion != ""){
			     $observacion1 = $observacion." (Se elimino por que se reprogramo la cita)"; 
			   }else{
			     $observacion1 = "Se elimino por que se reprogramo la cita";
			   }
			   
			   $status_agenda_cambio = "Eliminado";
		       $insert = "INSERT INTO agenda_cambio 
			       VALUES('$numero','$colaborador_id', '$pacientes_id', '$expediente','$fecha_cita','$start','$fecha_registro','$usuario_anterior','$usuario','Se le cambio la cita al usuario. Usuario que cambio la cita: $usuario','$status_agenda_cambio','$fecha_registro')";
			   $mysqli->query($insert);
			   
               //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			   $historial_numero = historial();
			   $estado = "Agregar";
			   $observacion = "Se agrego informacion de este registro en la entidad en el historial de cambio de la agenda";
			   $modulo = "Citas";
			   $insert = "INSERT INTO historial 
			        VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$start','$estado','$observacion','$usuario','$fecha_registro')";
			    $mysqli->query($insert);
			   /*****************************************************/			   
			
			   $delete = "DELETE FROM agenda WHERE agenda_id = '$agenda_id'";
			   $mysqli->query($delete);
			   
               //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			   $historial_numero = historial();
			   $estado = "Eliminar";
			   $observacion_ = "Se elimina la cita para este registro";
			   $modulo = "Citas";
			   $insert = "INSERT INTO historial 
			          VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$start','$estado','$observacion_','$usuario','$fecha_registro')";
			   $mysqli->query($insert);
			   /*****************************************************/				   
		    }
			 
		    //OBTENER CORRELATIVO ENTIDAD AGENDA
            $correlativo_agenda= "SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count 
			    FROM agenda";
			$result = $mysqli->query($correlativo_agenda);
            $correlativo_agenda2 = $result->fetch_assoc();
 
            $numero_agenda = $correlativo_agenda2['max'];
            $cantidad_agenda = $correlativo_agenda2['count'];

            if ( $cantidad_agenda == 0 )
	           $numero_agenda = 1;
            else
               $numero_agenda = $numero_agenda + 1;
		   
		    $insert = "INSERT INTO agenda 
			    VALUES('$numero_agenda', '$pacientes_id', '$expediente', '$colaborador_id', '$hora' ,'$start', '$end', '$fecha_registro', '0', '$color_repro', '$observacion' , '$usuario', '$servicio_id', '','0','0','1','$paciente','0')";
            $query = $mysqli->query($insert);

            //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado = "Agregar";
			$observacion = "Se agendo una cita para este registro";
			$modulo = "Citas";
			$insert = "INSERT INTO historial 
			    VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$start','$estado','$observacion','$usuario','$fecha_registro')";
			$mysqli->query($insert);
			/*****************************************************/				
		  
            /*********************************************************************************/
            //CONSULTA AÑO, MES y DIA DEL PACIENTE
            $nacimiento = "SELECT fecha_nacimiento AS fecha 
			    FROM pacientes 
				WHERE pacientes_id = '$pacientes_id'";
			$result = $mysqli->query($nacimiento);

            $nacimiento2 = $result->fetch_assoc();
            $fecha_de_nacimiento = $nacimiento2['fecha'];
     
            //OBTENER LA EDAD DEL USUARIO 
            /*********************************************************************************/
            $valores_array = getEdad($fecha_de_nacimiento);
            $anos = $valores_array['anos'];
            $meses = $valores_array['meses'];	  
            $dias = $valores_array['dias'];	
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
			  
              $insert = "INSERT INTO lista_espera (id,fecha_solicitud,fecha_inclusion,pacientes_id,edad,colaborador_id,prioridad,fecha_cita,tipo_cita,reprogramo,usuario,servicio) 
		          VALUES('$numero_listaespera','$fecha_registro','$fecha_registro','$pacientes_id','$anos','$colaborador_id','$prioridad','$fecha_cita','$paciente','X','$usuario','$servicio_id')";	
              $mysqli->query($insert);

            //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado = "Agregar";
			$observacion = "Se agrego registro de este usuario en la lista de espera";
			$modulo = "Citas";
			$insert = "INSERT INTO historial 
			    VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$start','$estado','$observacion','$usuario','$fecha_registro')";
			$mysqli->query($insert);
			/*****************************************************/				  
						
             /*********************************************************+*/						
		    $resp = 1;//REGISTRO EDITADO Y/O ALMACENADO CORRECTAMENTE
		       echo $resp;
	        }else{
			   $resp = 2;//ERROR AL PROCESAR ESTA SOLICITUD
		       echo $resp;
	        }
        }else{
		  $resp = 3;
		  echo $resp; //USUARIO YA TIENE CITA AGENDAD ESE DIA
	    }			
	}else{
	   $resp = 4;
	   echo $resp; //EL MEDICO YA TIENE LA HORA OCUPADA
   }
 }else{
	 echo 6;////YA SE HA PRECLINEADO ESTE USUARIO
 }	
}	

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>
