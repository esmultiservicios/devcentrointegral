<?php
session_start();
include '../funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

header('Content-Type: application/json');
$usuario = $_SESSION['colaborador_id'] ?? '';

if (empty($usuario)) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$correlativo = 'SELECT MAX(agenda_id) AS max, COUNT(agenda_id) AS count FROM agenda';
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'] ?? 0;
$cantidad = $correlativo2['count'] ?? 0;

$numero = ($cantidad == 0) ? 1 : $numero + 1;

$pacientes_id = $_POST['paciente_id'] ?? '';
$color = $_POST['color'] ?? '';
$fecha_cita = $_POST['fecha_cita'] ?? '';
$fecha_start = date('Y-m-d', strtotime($fecha_cita));
$fecha_cita_end = $_POST['fecha_cita_end'] ?? '';
$hora = $_POST['hora'] ?? '';
$medico = $_POST['medico'] ?? '';
$unidad = $_POST['unidad'] ?? '';
$observacion = ucwords(strtolower($_POST['obs'] ?? ''), ' ');
$fecha_registro = date('Y-m-d H:i:s');
$colaborador_id = $_POST['medico'] ?? '';
$fecha_consulta = date('Y-m-d');
$servicio = $_POST['serv'] ?? '';
$preclinica = 1;

// Consultar expediente
$consultar_expediente = "SELECT expediente, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consultar_expediente);

if ($result) {
    $consultar_expediente1 = $result->fetch_assoc();
    $expediente = $consultar_expediente1['expediente'] ?? '';
    $nombre = $consultar_expediente1['nombre'] ?? '';
} else {
    echo json_encode(['error' => 'Error en la consulta de expediente']);
    exit;
}

// Consultar usuario
$consultar_usuario = "SELECT a.agenda_id FROM agenda AS a INNER JOIN colaboradores AS c ON a.colaborador_id = c.colaborador_id WHERE a.pacientes_id = '$pacientes_id' AND cast(a.fecha_cita as DATE) = '$fecha_start' AND c.puesto_id = '$unidad'";
$result = $mysqli->query($consultar_usuario);

if ($result) {
    $consultar_usuario1 = $result->fetch_assoc();
} else {
    echo json_encode(['error' => 'Error en la consulta de usuario']);
    exit;
}

// Consultar última atención
$consulta_ultima_atencion = "SELECT atencion_id, pacientes_id, fecha FROM atenciones_medicas WHERE pacientes_id = '$pacientes_id' ORDER BY fecha DESC LIMIT 1";
$result = $mysqli->query($consulta_ultima_atencion);

if ($result) {
    $consulta_ultima_atencion2 = $result->fetch_assoc();
    $consulta_ultima_atencion_fecha = $consulta_ultima_atencion2['fecha'] ?? '';
} else {
    echo json_encode(['error' => 'Error en la consulta de última atención']);
    exit;
}

// Consultar médico
$consultar_medico = "SELECT agenda_id FROM agenda WHERE colaborador_id = '$medico' AND fecha_cita = '$fecha_cita' AND fecha_cita_end = '$fecha_cita_end' AND status = 0";
$result = $mysqli->query($consultar_medico);

if ($result) {
    $consultar_medico1 = $result->fetch_assoc();
} else {
    echo json_encode(['error' => 'Error en la consulta de médico']);
    exit;
}

// Consultar nombre del profesional
$consulta_nombre_profesional = "SELECT CONCAT(nombre,' ',apellido) AS nombre FROM colaboradores WHERE colaborador_id = '$medico'";
$result = $mysqli->query($consulta_nombre_profesional);

if ($result) {
    $consulta_nombre_profesional2 = $result->fetch_assoc();
    $nombre_colaborador = $consulta_nombre_profesional2['nombre'] ?? '';
} else {
    echo json_encode(['error' => 'Error en la consulta de nombre del profesional']);
    exit;
}

// Consultar nombre del servicio
$consulta_nombre_servicio = "SELECT nombre FROM servicios WHERE servicio_id = '$servicio'";
$result = $mysqli->query($consulta_nombre_servicio);

if ($result) {
    $consulta_nombre_servicio2 = $result->fetch_assoc();
    $nombre_servicio = $consulta_nombre_servicio2['nombre'] ?? '';
} else {
    echo json_encode(['error' => 'Error en la consulta de nombre del servicio']);
    exit;
}

if ($pacientes_id != 0 || $usuario != 0) {
    if (empty($consultar_medico1['agenda_id'])) {
        if (empty($consultar_usuario1['agenda_id'])) {
            // Consultar puesto colaborador
            $consulta_puesto = "SELECT puesto_id FROM colaboradores WHERE colaborador_id = '$colaborador_id'";
            $result = $mysqli->query($consulta_puesto);

            if ($result) {
                $consulta_puesto1 = $result->fetch_assoc();
                $puesto_colaborador = $consulta_puesto1['puesto_id'] ?? '';
            } else {
                echo json_encode(['error' => 'Error en la consulta de puesto']);
                exit;
            }

            $consultar_expediente = "SELECT a.agenda_id FROM agenda AS a INNER JOIN colaboradores AS c ON a.colaborador_id = c.colaborador_id WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_colaborador' AND a.servicio_id = '$servicio' AND a.status = 1";
            $result = $mysqli->query($consultar_expediente);

            if ($result) {
                $consultar_expediente1 = $result->fetch_assoc();
                $paciente = empty($consultar_expediente1['agenda_id']) ? 'N' : 'S';
            } else {
                echo json_encode(['error' => 'Error en la consulta de expediente']);
                exit;
            }

            if ($pacientes_id != 0) {
                $insert = "INSERT INTO agenda VALUES('$numero', '$pacientes_id', '$expediente', '$colaborador_id', '$hora', '$fecha_cita', '$fecha_cita_end', '$fecha_registro', '0', '$color', '$observacion','$usuario','$servicio','','0','0','2','$paciente','0')";
                $query = $mysqli->query($insert);

                // Ingresar registros en la entidad historial
                $historial_numero = historial();
                $estado = 'Agregar';
                $observacion = 'Se agendó una cita para este registro';
                $modulo = 'Citas';
                $insert = "INSERT INTO historial VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$colaborador_id','$servicio','$fecha_start','$estado','$observacion','$usuario','$fecha_registro')";
                $mysqli->query($insert);
            }

            // Consultar año, mes y día del paciente
            $nacimiento = "SELECT fecha_nacimiento AS fecha FROM pacientes WHERE pacientes_id = '$pacientes_id'";
            $result = $mysqli->query($nacimiento);

            if ($result) {
                $nacimiento2 = $result->fetch_assoc();
                $fecha_nacimiento = $nacimiento2['fecha'] ?? '';

                if ($fecha_nacimiento) {
                    $valores_array = getEdad($fecha_nacimiento);
                    $anos = $valores_array['anos'] ?? 0;
                    $meses = $valores_array['meses'] ?? 0;
                    $dias = $valores_array['dias'] ?? 0;
                } else {
                    $anos = $meses = $dias = 0;
                }
            } else {
                $anos = $meses = $dias = 0;
            }

            if ($query) {
                if ($expediente == 0) {
                    $exp = 'TEMP';
                } else {
                    $exp = $expediente;
                }

                echo json_encode([
                    'success' => 'Cita agendada correctamente',
                    'id' => $numero,
                    'title' => $exp . '-' . $nombre,
                    'start' => $fecha_cita,
                    'end' => $fecha_cita_end,
                    'color' => $color
                ]);
            } else {
                echo json_encode(['error' => 'No se pudo agendar la cita']);
            }
        } else {
            echo json_encode(['error' => 'El paciente ya tiene una cita agendada en la fecha indicada']);
        }
    } else {
        echo json_encode(['error' => 'El médico ya tiene una cita agendada en la fecha indicada']);
    }
} else {
    echo json_encode(['error' => 'Error en los datos proporcionados']);
}
