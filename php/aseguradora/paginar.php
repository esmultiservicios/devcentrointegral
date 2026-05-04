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

function acciones_aseguradora($aseguradora_id) {
    $aseguradora_id = (int)$aseguradora_id;

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownAseguradora'.$aseguradora_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownAseguradora'.$aseguradora_id.'" style="min-width:220px;">

                <a class="dropdown-item" href="javascript:editarRegistro('.$aseguradora_id.');void(0);">
                    <i class="fas fa-edit text-primary mr-2"></i> Editar aseguradora
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$aseguradora_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar aseguradora
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

$colaborador_id = (int)$_SESSION['colaborador_id'];
$paginaActual = post_int("partida", 1);
$dato = post_string("dato");

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

/* ============================================================
   ARMAR WHERE SEGURO
============================================================ */

$where = array();
$params = array();
$types = "";

if ($dato !== "") {
    $where[] = "(
        nombre LIKE ?
        OR rtn LIKE ?
        OR CAST(aseguradora_id AS CHAR) LIKE ?
    )";

    $buscarInicio = $dato . "%";
    $buscarCompleto = "%" . $dato . "%";

    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;

    $types .= "sss";
}

$where_sql = "";

if (count($where) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

try {

    /* ============================================================
       CONTAR REGISTROS
    ============================================================ */

    $sql_count = "
        SELECT COUNT(*) AS total
        FROM aseguradora
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
            aseguradora_id,
            nombre,
            rtn
        FROM aseguradora
        $where_sql
        ORDER BY aseguradora_id ASC
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
                        <th width="6%">No.</th>
                        <th width="45%">Aseguradora</th>
                        <th width="35%">RTN</th>
                        <th width="14%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    $i = $limit + 1;

    while ($registro2 = $result->fetch_assoc()) {

        $aseguradora_id = (int)$registro2['aseguradora_id'];
        $nombre = limpiar_texto($registro2['nombre']);
        $rtn = limpiar_texto($registro2['rtn']);

        if ($rtn === "") {
            $rtn = '<span class="text-muted">No registrado</span>';
        }

        $tabla .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$nombre.'</td>
                <td>'.$rtn.'</td>
                <td>'.acciones_aseguradora($aseguradora_id).'</td>
            </tr>
        ';

        $i++;
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="4" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="4">
                    <b><p align="center">Total de Registros Encontrados '.number_format($nroProductos).'</p></b>
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