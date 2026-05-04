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

function acciones_usuario($usuario_id) {
    $usuario_id = (int)$usuario_id;

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownUsuario'.$usuario_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownUsuario'.$usuario_id.'" style="min-width:230px;">

                <a class="dropdown-item" href="javascript:editarRegistro('.$usuario_id.');void(0);">
                    <i class="fas fa-user-edit text-primary mr-2"></i> Editar usuario
                </a>

                <a class="dropdown-item" href="javascript:modificarContra('.$usuario_id.');void(0);">
                    <i class="fas fa-sync text-warning mr-2"></i> Cambiar contraseña
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$usuario_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar usuario
                </a>

            </div>
        </div>
    ';
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id']) || !isset($_SESSION['type'])) {
    responder_json(
        '<div class="alert alert-danger">La sesión ha expirado. Por favor vuelva a iniciar sesión.</div>',
        ''
    );
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$dato = post_string("dato");
$estatus = post_int("status_valor", 1);
$paginaActual = post_int("partida", 1);
$type = (int)$_SESSION['type'];

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

$where[] = "u.estatus = ?";
$params[] = $estatus;
$types .= "i";

if ($type !== 1) {
    $where[] = "u.type NOT IN (1)";
}

if ($dato !== "") {
    $where[] = "(
        CAST(u.id AS CHAR) LIKE ?
        OR CONCAT(c.nombre, ' ', c.apellido) LIKE ?
        OR c.nombre LIKE ?
        OR c.apellido LIKE ?
        OR u.username LIKE ?
        OR tipo.nombre LIKE ?
        OR u.email LIKE ?
        OR e.nombre LIKE ?
    )";

    $buscarInicio = $dato . "%";
    $buscarCompleto = "%" . $dato . "%";

    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;

    $types .= "ssssssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$from_sql = "
    FROM users AS u
    INNER JOIN colaboradores AS c
        ON u.colaborador_id = c.colaborador_id
    INNER JOIN empresa AS e
        ON c.empresa_id = e.empresa_id
    INNER JOIN tipo_user AS tipo
        ON u.type = tipo.tipo_user_id
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

    $nroLotes = 15;
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
            u.id,
            c.nombre,
            c.apellido,
            u.username,
            u.email,
            e.nombre AS empresa,
            u.type AS tipo,
            u.estatus,
            tipo.nombre AS tipo_usuario,
            c.colaborador_id
        $from_sql
        $where_sql
        ORDER BY u.id ASC
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
                        <th width="5%">Código</th>
                        <th width="12%">Nombre</th>
                        <th width="12%">Apellido</th>
                        <th width="12%">Username</th>
                        <th width="18%">Email</th>
                        <th width="16%">Empresa</th>
                        <th width="12%">Tipo</th>
                        <th width="8%">Estatus</th>
                        <th width="8%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    while ($registro2 = $result->fetch_assoc()) {

        $usuario_id = (int)$registro2['id'];
        $nombre = limpiar_texto($registro2['nombre']);
        $apellido = limpiar_texto($registro2['apellido']);
        $username = limpiar_texto($registro2['username']);
        $email = limpiar_texto($registro2['email']);
        $empresa = limpiar_texto($registro2['empresa']);
        $tipo_usuario = limpiar_texto($registro2['tipo_usuario']);
        $estatus_usuario = (int)$registro2['estatus'];

        if ($email === "") {
            $email = '<span class="text-muted">No registrado</span>';
        }

        $tabla .= '
            <tr>
                <td>'.$usuario_id.'</td>
                <td>'.$nombre.'</td>
                <td>'.$apellido.'</td>
                <td>'.$username.'</td>
                <td>'.$email.'</td>
                <td>'.$empresa.'</td>
                <td>'.$tipo_usuario.'</td>
                <td>'.texto_estatus($estatus_usuario).'</td>
                <td>'.acciones_usuario($usuario_id).'</td>
            </tr>
        ';
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="9" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="9">
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