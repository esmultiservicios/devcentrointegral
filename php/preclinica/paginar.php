<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include "../funtions.php";

/* ============================================================
   CONEXIÓN A DB
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   FUNCIONES AUXILIARES
============================================================ */

function responder_json($tabla, $paginacion) {
    echo json_encode(array(
        0 => $tabla,
        1 => $paginacion
    ));
    exit;
}

function post_int($key, $default = 0) {
    if (!isset($_POST[$key]) || $_POST[$key] === "") {
        return $default;
    }

    return (int)$_POST[$key];
}

function post_string($key, $default = "") {
    if (!isset($_POST[$key])) {
        return $default;
    }

    return trim($_POST[$key]);
}

function limpiar_texto($texto) {
    return htmlspecialchars($texto ?? "", ENT_QUOTES, "UTF-8");
}

function bind_params_ref($stmt, $types, $params) {
    if ($types === "" || count($params) === 0) {
        return;
    }

    $refs = array();
    $refs[] = $types;

    foreach ($params as $key => $value) {
        $refs[] = &$params[$key];
    }

    call_user_func_array(array($stmt, "bind_param"), $refs);
}

function ejecutar_consulta($mysqli, $sql, $types = "", $params = array()) {
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $mysqli->error);
    }

    if ($types !== "" && count($params) > 0) {
        bind_params_ref($stmt, $types, $params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function acciones_agenda($agenda_id, $pacientes_id, $expediente) {
    $agenda_id = (int)$agenda_id;
    $pacientes_id = (int)$pacientes_id;
    $expediente = (int)$expediente;

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownAgenda'.$agenda_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownAgenda'.$agenda_id.'" style="min-width: 220px;">

                <a class="dropdown-item" href="javascript:editarRegistro('.$agenda_id.', '.$expediente.');void(0);">
                    <i class="fas fa-notes-medical text-primary mr-2"></i> Agregar preclínica
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:nosePresentoRegistro('.$agenda_id.', '.$pacientes_id.');void(0);">
                    <i class="fas fa-times-circle mr-2"></i> No se presentó
                </a>

            </div>
        </div>
    ';
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id'])) {
    responder_json(
        '<div class="alert alert-danger">La sesión ha expirado. Por favor vuelva a iniciar sesión.</div>',
        ''
    );
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$paginaActual = post_int("partida", 1);
$fechai = post_string("fechai", date("Y-m-d"));
$fechaf = post_string("fechaf", date("Y-m-d"));
$dato = post_string("dato");
$unidad = post_int("unidad");
$colaborador = post_int("colaborador");

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

/* ============================================================
   ARMAR WHERE SEGURO
============================================================ */

$where = array();
$params = array();
$types = "";

$where[] = "ag.preclinica = 0";

if ($fechai !== "" && $fechaf !== "") {
    $where[] = "CAST(ag.fecha_cita AS DATE) BETWEEN ? AND ?";
    $params[] = $fechai;
    $params[] = $fechaf;
    $types .= "ss";
}

if ($colaborador > 0) {
    $where[] = "ag.colaborador_id = ?";
    $params[] = $colaborador;
    $types .= "i";
}

if ($unidad > 0) {
    $where[] = "ag.servicio_id = ?";
    $params[] = $unidad;
    $types .= "i";
}

if ($dato !== "") {
    $where[] = "(
        p.expediente LIKE ?
        OR CONCAT(p.nombre, ' ', p.apellido) LIKE ?
        OR CONCAT(p.apellido, ' ', p.nombre) LIKE ?
        OR p.nombre LIKE ?
        OR p.apellido LIKE ?
        OR p.identidad LIKE ?
        OR ag.hora LIKE ?
        OR s.nombre LIKE ?
        OR CONCAT(c.nombre, ' ', c.apellido) LIKE ?
    )";

    $buscarInicio = $dato . "%";
    $buscarCompleto = "%" . $dato . "%";

    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;

    $types .= "sssssssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$from_sql = "
    FROM agenda AS ag
    INNER JOIN pacientes AS p
        ON ag.pacientes_id = p.pacientes_id
    INNER JOIN colaboradores AS c
        ON ag.colaborador_id = c.colaborador_id
    INNER JOIN servicios AS s
        ON ag.servicio_id = s.servicio_id
    INNER JOIN puesto_colaboradores AS pc
        ON c.puesto_id = pc.puesto_id
";

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

try {

    /* ============================================================
       CONTAR REGISTROS
    ============================================================ */

    $sql_count = "
        SELECT COUNT(*) AS total
        $from_sql
        $where_sql
    ";

    $result_count = ejecutar_consulta($mysqli, $sql_count, $types, $params);
    $row_count = $result_count->fetch_assoc();

    $nroProductos = (int)$row_count['total'];

    $nroLotes = 25;
    $nroPaginas = ($nroProductos > 0) ? ceil($nroProductos / $nroLotes) : 1;

    if ($paginaActual > $nroPaginas) {
        $paginaActual = $nroPaginas;
    }

    $limit = ($paginaActual <= 1) ? 0 : $nroLotes * ($paginaActual - 1);

    /* ============================================================
       PAGINACIÓN
    ============================================================ */

    $lista = '';

    if ($paginaActual > 1) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(1);void(0);">Inicio</a></li>';
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual - 1).');void(0);">Anterior '.($paginaActual - 1).'</a></li>';
    }

    if ($paginaActual < $nroPaginas) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual + 1).');void(0);">Siguiente '.($paginaActual + 1).' de '.$nroPaginas.'</a></li>';
    }

    if ($paginaActual > 1) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.$nroPaginas.');void(0);">Última</a></li>';
    }

    /* ============================================================
       CONSULTAR REGISTROS
    ============================================================ */

    $sql_registro = "
        SELECT 
            ag.pacientes_id,
            ag.agenda_id,
            p.expediente,
            p.identidad,
            CONCAT(p.apellido, ' ', p.nombre) AS paciente,
            DATE_FORMAT(CAST(ag.fecha_cita AS DATE), '%d/%m/%Y') AS fecha_cita,
            ag.hora,
            CONCAT(c.nombre, ' ', c.apellido) AS colaborador,
            s.nombre AS servicio,
            ag.observacion,
            ag.comentario,
            CAST(ag.fecha_cita AS DATE) AS fecha,
            pc.puesto_id,
            ag.servicio_id,
            CAST(ag.fecha_cita AS DATE) AS fecha_cita1
        $from_sql
        $where_sql
        ORDER BY fecha_cita1 ASC, ag.hora ASC
        LIMIT ?, ?
    ";

    $params_registro = $params;
    $types_registro = $types . "ii";
    $params_registro[] = $limit;
    $params_registro[] = $nroLotes;

    $result = ejecutar_consulta($mysqli, $sql_registro, $types_registro, $params_registro);

    /* ============================================================
       TABLA
    ============================================================ */

    $tabla = '
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr class="bg-info text-white">
                        <th width="3%">No.</th>
                        <th width="8%">Expediente</th>
                        <th width="9%">Identidad</th>
                        <th width="15%">Nombre</th>
                        <th width="7%">Fecha Cita</th>
                        <th width="6%">Hora</th>
                        <th width="13%">Profesional</th>
                        <th width="11%">Servicio</th>
                        <th width="11%">Observación</th>
                        <th width="11%">Comentario</th>
                        <th width="8%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    $i = $limit + 1;

    while ($registro2 = $result->fetch_assoc()) {

        $agenda_id = (int)$registro2['agenda_id'];
        $pacientes_id = (int)$registro2['pacientes_id'];
        $expediente_original = (int)$registro2['expediente'];

        if ((int)$registro2['expediente'] === 0) {
            $expediente = "TEMP";
        } else {
            $expediente = $registro2['expediente'];
        }

        $identidad = limpiar_texto($registro2['identidad']);
        $paciente = limpiar_texto($registro2['paciente']);
        $fecha_cita = limpiar_texto($registro2['fecha_cita']);
        $hora = limpiar_texto($registro2['hora']);
        $colaborador = limpiar_texto($registro2['colaborador']);
        $servicio = limpiar_texto($registro2['servicio']);
        $observacion = limpiar_texto($registro2['observacion']);
        $comentario = limpiar_texto($registro2['comentario']);

        if ($observacion === "") {
            $observacion = '<span class="text-muted">Sin observación</span>';
        }

        if ($comentario === "") {
            $comentario = '<span class="text-muted">Sin comentario</span>';
        }

        $tabla .= '
            <tr>
                <td>'.limpiar_texto($i).'</td>

                <td>
                    <span class="font-weight-bold text-primary">
                        '.limpiar_texto($expediente).'
                    </span>
                </td>

                <td>'.$identidad.'</td>
                <td>'.$paciente.'</td>
                <td>'.$fecha_cita.'</td>
                <td>'.$hora.'</td>
                <td>'.$colaborador.'</td>
                <td>'.$servicio.'</td>
                <td>'.$observacion.'</td>
                <td>'.$comentario.'</td>
                <td>'.acciones_agenda($agenda_id, $pacientes_id, $expediente_original).'</td>
            </tr>
        ';

        $i++;
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="11" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="11">
                    <b><p align="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
                </td>
            </tr>
        ';
    }

    $tabla .= '
                </tbody>
            </table>
        </div>
    ';

    responder_json($tabla, $lista);

} catch (Exception $e) {

    responder_json(
        '<div class="alert alert-danger">Error: '.limpiar_texto($e->getMessage()).'</div>',
        ''
    );
}

$mysqli->close();