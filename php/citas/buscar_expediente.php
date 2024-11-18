<?php
session_start();
include '../funtions.php';

// CONEXIÓN A DB
$mysqli = connect_mysqli();

$expediente = $_POST['expediente'];  // Recibe la identidad del usuario o el número de expediente del mismo
$servicio = $_POST['servicio_id'];
$colaborador_id = $_POST['colaborador_id'];
$start = $_POST['start'];
$end = $_POST['end'];
$fecha = date('Y-m-d');
$año = date('Y', strtotime($fecha));
$fecha_cita = date('Y-m-d', strtotime($start));
$fecha_inical = $año . '-01-01';
$fecha_final = $año . '-12-31';
$hora_ = date('H:i', strtotime($start));
$hora_h = date('H:i', strtotime($start));

// CONSULTAR PUESTO COLABORADOR
$consultar_puesto = "SELECT puesto_id FROM colaboradores WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto);
$consultar_colaborador_puesto_id = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['puesto_id'] : null;

// OBTENER ESTATUS PARA ACTUALIZAR DATOS DEL PACIENTE
$consultar_paciente = "SELECT pacientes_id, expediente, CONCAT(nombre, ' ', apellido) AS nombre FROM pacientes WHERE expediente = '$expediente' OR identidad = '$expediente'";
$result = $mysqli->query($consultar_paciente);
$consultar_paciente2 = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
$pacientes_id = $consultar_paciente2['pacientes_id'] ?? null;
$expediente_consulta = $consultar_paciente2['expediente'] ?? null;
$paciente_nombre = $consultar_paciente2['nombre'] ?? '';

// CONSULTAR DATOS DE LA JORNADA Y CANTIDAD DE NUEVOS Y SUBSIGUIENTES EN jornada_colaboradores
$consultarJornada = "SELECT j_colaborador_id, nuevos, subsiguientes FROM jornada_colaboradores WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultarJornada);
$consultarJornada2 = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
$consultarJornadaJornada_id = $consultarJornada2['j_colaborador_id'] ?? null;
$consultarJornadaNuevos = $consultarJornada2['nuevos'] ?? 0;
$consultarJornadaSubsiguientes = $consultarJornada2['subsiguientes'] ?? 0;
$consultaJornadaTotal = $consultarJornadaNuevos + $consultarJornadaSubsiguientes;

// CONSULTAR SI EL PROFESIONAL TIENE CITA EN ESE HORARIO
$query_profesional = "SELECT agenda_id FROM agenda WHERE colaborador_id = '$colaborador_id' AND fecha_cita = '$start' AND status IN (1, 0)";
$result_profesional = $mysqli->query($query_profesional);

// CONSULTAR SI EL PACIENTE TIENE ESA HORA OCUPADA
$query_paciente = "SELECT agenda_id FROM agenda WHERE pacientes_id = '$pacientes_id' AND fecha_cita = '$start'";
$result_pacientes = $mysqli->query($query_paciente);

if ($result_profesional->num_rows > 0) {
	echo 1;  // El profesional ya tiene esa hora ocupada
} else {
	if ($result_pacientes->num_rows > 0) {
		echo 2;  // El paciente ya tiene esta hora ocupada
	} else {
		// CONSULTAR SI EL USUARIO ES SUBSIGUIENTE
		$consultar_agenda_pacientes = "SELECT a.agenda_id AS 'agenda_id' FROM agenda AS a INNER JOIN colaboradores AS c ON a.colaborador_id = c.colaborador_id WHERE pacientes_id = '$pacientes_id' AND c.puesto_id = '$consultar_colaborador_puesto_id' AND a.status = 1";
		$result_agenda_pacientes = $mysqli->query($consultar_agenda_pacientes);
		$consultar_expediente1 = ($result_agenda_pacientes && $result_agenda_pacientes->num_rows > 0) ? $result_agenda_pacientes->fetch_assoc() : null;
		$consulta_agenda_id = $consultar_expediente1['agenda_id'] ?? null;

		// CONSULTAR CANTIDAD DE USUARIOS NUEVOS AGENDADOS
		$consulta_nuevos = "SELECT COUNT(agenda_id) AS 'total_nuevos' FROM agenda WHERE CAST(fecha_cita AS DATE) = '$fecha_cita' AND colaborador_id = '$colaborador_id' AND paciente = 'N' AND status = 0";
		$result = $mysqli->query($consulta_nuevos);
		$consulta_nuevos_devuelto = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total_nuevos'] : 0;

		if ($result_agenda_pacientes->num_rows == 0) {
			$consulta_nuevos_devuelto++;
		}

		// CONSULTAR CANTIDAD DE USUARIOS SUBSIGUIENTES AGENDADOS
		$consulta_subsiguientes = "SELECT COUNT(agenda_id) AS 'total_subsiguientes' FROM agenda WHERE CAST(fecha_cita AS DATE) = '$fecha_cita' AND colaborador_id = '$colaborador_id' AND paciente = 'S' AND status = 1";
		$result = $mysqli->query($consulta_subsiguientes);
		$consulta_subsiguientes_devuelto = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total_subsiguientes'] : 0;

		if (!empty($consultar_expediente1['agenda_id'])) {
			$consulta_subsiguientes_devuelto++;
		}

		// EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGÚN PROFESIONAL
		$valores_array = getAgendatime($consultarJornadaJornada_id, $servicio, $consultar_colaborador_puesto_id, $consulta_agenda_id, $hora_h, $consulta_nuevos_devuelto, $consultarJornadaNuevos, $consultaJornadaTotal, $consulta_subsiguientes_devuelto);
		$hora = $valores_array['hora'] ?? '';
		$colores = $valores_array['colores'] ?? '';

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

$result->free();
$result_profesional->free();
$result_pacientes->free();
$mysqli->close();
