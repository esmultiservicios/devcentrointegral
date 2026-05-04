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

function limpiar_js_string($texto) {
    return str_replace(
        array("\\", "'", "\"", "\r", "\n"),
        array("\\\\", "\\'", "&quot;", "", ""),
        $texto ?? ""
    );
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

function tabla_existe($mysqli, $tabla) {
    $sql = "
        SELECT COUNT(*) AS total
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = ?
    ";

    $result = ejecutar_consulta($mysqli, $sql, "s", array($tabla));
    $row = $result->fetch_assoc();

    return ((int)$row['total'] > 0);
}

function acciones_catalogo($id, $entidad) {
    $id = (int)$id;
    $entidad_js = limpiar_js_string($entidad);

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownCatalogo'.$id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownCatalogo'.$id.'" style="min-width:220px;">

                <a class="dropdown-item" href="javascript:editarRegistro('.$id.', \''.$entidad_js.'\');void(0);">
                    <i class="fas fa-edit text-primary mr-2"></i> Editar registro
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$id.', \''.$entidad_js.'\');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar registro
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
$entidad = post_string("entidad");
$dato = post_string("dato");

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

/* ============================================================
   VALIDAR ENTIDAD / TABLA

   Importante:
   No se puede usar bind_param para nombres de tablas.
   Por eso validamos que solo tenga letras, números y guion bajo.
============================================================ */

if ($entidad === "" || !preg_match('/^[a-zA-Z0-9_]+$/', $entidad)) {
    responder_json(
        '<div class="alert alert-danger">Entidad inválida.</div>',
        ''
    );
}

try {

    if (!tabla_existe($mysqli, $entidad)) {
        responder_json(
            '<div class="alert alert-danger">La tabla solicitada no existe: '.limpiar_texto($entidad).'</div>',
            ''
        );
    }

    /* ============================================================
       ARMAR WHERE SEGURO
    ============================================================ */

    $where_sql = "";
    $params = array();
    $types = "";

    if ($dato !== "") {
        $where_sql = "WHERE nombre LIKE ?";
        $params[] = $dato . "%";
        $types .= "s";
    }

    /* ============================================================
       CONTAR REGISTROS
    ============================================================ */

    $sql_count = "
        SELECT COUNT(*) AS total
        FROM `$entidad`
        $where_sql
    ";

    $result_count = ejecutar_consulta($mysqli, $sql_count, $types, $params);
    $row_count = $result_count->fetch_assoc();

    $nroProductos = (int)$row_count['total'];

    $nroLotes = 10;
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

       Este paginar mantiene tu lógica original:
       columna 0 = ID
       columna 1 = Descripción / nombre
    ============================================================ */

    $sql_registro = "
        SELECT *
        FROM `$entidad`
        $where_sql
        ORDER BY 1 ASC
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
                        <th width="8%">No.</th>
                        <th width="72%">Descripción</th>
                        <th width="20%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    while ($registro2 = $result->fetch_array()) {

        $id = (int)$registro2[0];
        $descripcion = limpiar_texto($registro2[1]);

        if ($descripcion === "") {
            $descripcion = '<span class="text-muted">Sin descripción</span>';
        }

        $tabla .= '
            <tr>
                <td>'.$id.'</td>
                <td>'.$descripcion.'</td>
                <td>'.acciones_catalogo($id, $entidad).'</td>
            </tr>
        ';
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="3" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="3">
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