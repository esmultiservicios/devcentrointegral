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

function texto_estatus($estatus) {
    $estatus = (int)$estatus;

    if ($estatus === 1) {
        return '
            <span class="badge badge-success px-3 py-2" style="font-size:12px; border-radius:20px; min-width:90px;">
                <i class="fas fa-check-circle mr-1"></i> Activo
            </span>';
    }

    return '
        <span class="badge badge-secondary px-3 py-2" style="font-size:12px; border-radius:20px; min-width:90px;">
            <i class="fas fa-ban mr-1"></i> Inactivo
        </span>';
}

function acciones_colaborador($colaborador_id) {
    $colaborador_id = (int)$colaborador_id;

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownColaborador'.$colaborador_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownColaborador'.$colaborador_id.'" style="min-width:220px;">

                <a class="dropdown-item" href="javascript:editarRegistro('.$colaborador_id.');void(0);">
                    <i class="fas fa-user-edit text-primary mr-2"></i> Editar colaborador
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$colaborador_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar colaborador
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

$dato = post_string("dato");
$estatus = post_int("estatus", 1);
$paginaActual = post_int("partida", 1);

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

if (!in_array($estatus, array(1, 2))) {
    $estatus = 1;
}

/* ============================================================
   ARMAR WHERE SEGURO
============================================================ */

$where = array();
$params = array();
$types = "";

$where[] = "c.estatus = ?";
$params[] = $estatus;
$types .= "i";

if ($dato !== "") {
    $where[] = "(
        CAST(c.colaborador_id AS CHAR) LIKE ?
        OR CONCAT(c.nombre, ' ', c.apellido) LIKE ?
        OR c.nombre LIKE ?
        OR c.apellido LIKE ?
        OR p.nombre LIKE ?
        OR c.identidad LIKE ?
        OR e.nombre LIKE ?
    )";

    $buscarInicio = $dato . "%";
    $buscarCompleto = "%" . $dato . "%";

    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;

    $types .= "sssssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$from_sql = "
    FROM colaboradores AS c
    INNER JOIN empresa AS e
        ON c.empresa_id = e.empresa_id
    INNER JOIN puesto_colaboradores AS p
        ON c.puesto_id = p.puesto_id
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

    $nroLotes = 20;
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
            c.colaborador_id AS codigo,
            CONCAT(c.nombre, ' ', c.apellido) AS nombre,
            p.nombre AS puesto,
            e.nombre AS empresa,
            c.identidad,
            c.estatus
        $from_sql
        $where_sql
        ORDER BY c.colaborador_id ASC
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
                        <th width="7%">Código</th>
                        <th width="22%">Nombre</th>
                        <th width="13%">Identidad</th>
                        <th width="18%">Puesto</th>
                        <th width="22%">Empresa</th>
                        <th width="9%">Estatus</th>
                        <th width="9%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    while ($registro2 = $result->fetch_assoc()) {

        $codigo = (int)$registro2['codigo'];
        $nombre = limpiar_texto($registro2['nombre']);
        $identidad = limpiar_texto($registro2['identidad']);
        $puesto = limpiar_texto($registro2['puesto']);
        $empresa = limpiar_texto($registro2['empresa']);
        $estatus_colaborador = (int)$registro2['estatus'];

        if ($identidad === "") {
            $identidad = '<span class="text-muted">No registrada</span>';
        }

        $tabla .= '
            <tr>
                <td>'.$codigo.'</td>
                <td>'.$nombre.'</td>
                <td>'.$identidad.'</td>
                <td>'.$puesto.'</td>
                <td>'.$empresa.'</td>
                <td>'.texto_estatus($estatus_colaborador).'</td>
                <td>'.acciones_colaborador($codigo).'</td>
            </tr>
        ';
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="7" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="7">
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