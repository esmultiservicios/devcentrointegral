<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente']; //Recibe la identidad del usuario o el numero de expediente del mismo
$servicio = $_POST['servicio_id'];
$colaborador_id = $_POST['colaborador_id'];
$start = $_POST['start'];
$end = $_POST['end'];
$fecha = date('Y-m-d');
$año = date("Y", strtotime($fecha));
$fecha_cita = date("Y-m-d", strtotime($start));
$fecha_inical = $año."-01-01";
$fecha_final = $año."-12-31";
$hora_ = date('H:i',strtotime($start)); 
$hora_h = date('H:i',strtotime($start));

//CONSULTAR PUESTO COLABORADOR			  
$consultar_puesto = "SELECT puesto_id 
   FROM colaboradores 
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto);
$consultar_puesto1 = $result->fetch_assoc();
$consultar_colaborador_puesto_id = $consultar_puesto1['puesto_id'];

//OBTENEMOS EL ESTATUS PARA ACTUALIZARLE LOS DATOS AL PACIENTE			 
$consultar_paciente = "SELECT pacientes_id, expediente, CONCAT(nombre,' ',apellido) AS nombre
        FROM pacientes 
		WHERE expediente = '$expediente' OR identidad = '$expediente'";
$result = $mysqli->query($consultar_paciente);		
$consultar_paciente2 = $result->fetch_assoc();
$pacientes_id = $consultar_paciente2['pacientes_id'];
$expediente_consulta = $consultar_paciente2['expediente'];
$paciente_nombre = $consultar_paciente2['nombre'];

//CONSULTAR DATOS DE LA JORNADA Y LA CANTIDAD DE NUEVOS Y SUBSIGUIENTES EN jornada_colaboradores
$consultarJornada = "SELECT j_colaborador_id, nuevos, subsiguientes 
    FROM jornada_colaboradores 
    WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultarJornada);
$consultarJornada2 = $result->fetch_assoc();
$consultarJornadaJornada_id = $consultarJornada2['j_colaborador_id'];
$consultarJornadaNuevos = $consultarJornada2['nuevos'];
$consultarJornadaSubsiguientes = $consultarJornada2['subsiguientes'];
$consultaJornadaTotal = $consultarJornadaNuevos + $consultarJornadaSubsiguientes;

//CONSULTAMOS QUE EL PROFESIONAL NO TENGA CITA EN ESA HORARIOS
$query_profesional = "SELECT agenda_id
	FROM agenda
	WHERE colaborador_id = '$colaborador_id' AND fecha_cita = '$start' AND  status IN(1,0)";
$result_profesional = $mysqli->query($query_profesional);

//CONSULTAMOS QUE EL PACIENTE NO TENGA ESA HORA OCUPADA
$query_paciente = "SELECT agenda_id
	FROM agenda
	WHERE pacientes_id = '$pacientes_id' AND fecha_cita = '$start'";
$result_pacientes = $mysqli->query($query_paciente);
	
if($result_profesional->num_rows>0){
	echo 1; //EL PROFRESIONAL YA TIENE ESA HORA OCUPADA
}else{
	if($result_pacientes->num_rows>0){
		echo 2;//EL PACIENTE YA TIENE ESTA HORA OCUPADA
	}else{
		//INICIO, CONSULTA SI EL USUARIO ES SUBSIGUIENTE
		$consultar_agenda_pacientes = "SELECT a.agenda_id AS 'agenda_id'
			FROM agenda AS a
			INNER JOIN colaboradores AS c
			ON a.colaborador_id = c.colaborador_id
			WHERE pacientes_id = '$pacientes_id' AND c.puesto_id = '$consultar_colaborador_puesto_id' AND a.status = 1";
		$result_agenda_pacientes = $mysqli->query($consultar_agenda_pacientes);
	 
		$consultar_expediente1 = $result_agenda_pacientes->fetch_assoc();
		$consulta_agenda_id = $consultar_expediente1['agenda_id'];
		//FIN, CONSULTA SI EL USUARIO ES SUBSIGUIENTE		
			
		//CONSULTAMOS LA CANTIDAD DE USUARIOS NUEVOS AGENDADOS
		$consulta_nuevos = "SELECT COUNT(agenda_id) AS 'total_nuevos' 
			 FROM agenda 
			 WHERE CAST(fecha_cita AS DATE) = '$fecha_cita' AND colaborador_id = '$colaborador_id' AND paciente = 'N' AND status = 0";
		$result = $mysqli->query($consulta_nuevos);
		$consulta_nuevos1 = $result->fetch_assoc();
		$consulta_nuevos_devuelto = $consulta_nuevos1['total_nuevos'];

		if($result_agenda_pacientes->num_rows==0){
			$consulta_nuevos_devuelto = $consulta_nuevos_devuelto + 1;
		}
			
		//CONSULTAMOS LA CANTIDAD DE USUARIOS SUBSIGUIENTES AGENDADOS
		$consulta_subsiguientes = "SELECT COUNT(agenda_id) AS 'total_subsiguientes' 
			FROM agenda 
			WHERE CAST(fecha_cita AS DATE) = '$fecha_cita' AND colaborador_id = '$colaborador_id' AND paciente = 'S'  AND status = 1";
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

		$datos = array(
				0 => $pacientes_id, 
				1 => $paciente_nombre,
				2 => $colores,	
				3 => $hora,
				4 => $colaborador_id,				
		);
		echo json_encode($datos);			
	}
}

$result->free();//LIMPIAR RESULTADO
$result_profesional->free();//LIMPIAR RESULTADO
$result_pacientes->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN		
?>