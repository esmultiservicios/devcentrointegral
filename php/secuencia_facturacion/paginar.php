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

function formato_numero_documento($prefijo, $numero, $relleno) {
    $numero = (int)$numero;
    $relleno = (int)$relleno;

    if ($relleno <= 0) {
        $relleno = 8;
    }

    return $prefijo . str_pad($numero, $relleno, "0", STR_PAD_LEFT);
}

function badge_estado($activo) {
    $activo = (int)$activo;

    if ($activo === 1) {
        return '<span class="badge badge-success px-3 py-2" style="font-size: 13px; border-radius: 20px; min-width: 80px;">
                    <i class="fas fa-check-circle mr-1"></i> Activo
                </span>';
    }

    return '<span class="badge badge-danger px-3 py-2" style="font-size: 13px; border-radius: 20px; min-width: 80px;">
                <i class="fas fa-ban mr-1"></i> Inactivo
            </span>';
}

function badge_documento($documento_id, $documento) {
    $documento_id = (int)$documento_id;
    $documento = limpiar_texto($documento);

    if ($documento_id === 1) {
        return '<span class="badge badge-primary px-3 py-2" style="font-size: 13px; border-radius: 20px;">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> '.$documento.'
                </span>';
    }

    if ($documento_id === 4) {
        return '<span class="badge badge-info px-3 py-2" style="font-size: 13px; border-radius: 20px;">
                    <i class="fas fa-file-alt mr-1"></i> '.$documento.'
                </span>';
    }

    return '<span class="badge badge-secondary px-3 py-2" style="font-size: 13px; border-radius: 20px;">
                <i class="fas fa-file mr-1"></i> '.$documento.'
            </span>';
}

function acciones_secuencia($secuencia_facturacion_id) {
    $secuencia_facturacion_id = (int)$secuencia_facturacion_id;

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownSecuencia'.$secuencia_facturacion_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Acciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownSecuencia'.$secuencia_facturacion_id.'">
                <a class="dropdown-item" href="javascript:editarRegistro('.$secuencia_facturacion_id.');void(0);">
                    <i class="fas fa-edit text-warning mr-2"></i> Editar secuencia
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$secuencia_facturacion_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar secuencia
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
$dato = post_string("dato");
$empresa = post_int("empresa", 0);
$estado = post_int("estado", 1);

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

if (!in_array($estado, array(1, 2))) {
    $estado = 1;
}

/* ============================================================
   WHERE SEGURO
============================================================ */

$where = array();
$params = array();
$types = "";

$where[] = "sf.activo = ?";
$params[] = $estado;
$types .= "i";

if ($empresa > 0) {
    $where[] = "sf.empresa_id = ?";
    $params[] = $empresa;
    $types .= "i";
}

if ($dato !== "") {
    $where[] = "(
        e.nombre LIKE ?
        OR e.rtn LIKE ?
        OR d.nombre LIKE ?
        OR sf.cai LIKE ?
        OR sf.prefijo LIKE ?
        OR CAST(sf.siguiente AS CHAR) LIKE ?
        OR CONCAT(sf.prefijo, LPAD(sf.siguiente, sf.relleno, '0')) LIKE ?
    )";

    $buscar = "%" . $dato . "%";

    $params[] = $buscar;
    $params[] = $buscar;
    $params[] = $buscar;
    $params[] = $buscar;
    $params[] = $buscar;
    $params[] = $buscar;
    $params[] = $buscar;

    $types .= "sssssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$from_sql = "
    FROM secuencia_facturacion AS sf
    INNER JOIN empresa AS e
        ON sf.empresa_id = e.empresa_id
    LEFT JOIN documento AS d
        ON sf.documento_id = d.documento_id
";

/* ============================================================
   PROCESO
============================================================ */

try {

    $sql_count = "
        SELECT COUNT(*) AS total
        $from_sql
        $where_sql
    ";

    $result_count = ejecutar_consulta($mysqli, $sql_count, $types, $params);
    $row_count = $result_count->fetch_assoc();

    $nroLotes = 25;
    $nroProductos = (int)$row_count['total'];
    $nroPaginas = ($nroProductos > 0) ? ceil($nroProductos / $nroLotes) : 1;

    if ($paginaActual > $nroPaginas) {
        $paginaActual = $nroPaginas;
    }

    $limit = ($paginaActual <= 1) ? 0 : $nroLotes * ($paginaActual - 1);

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

    $sql_registro = "
        SELECT
            sf.secuencia_facturacion_id,
            sf.empresa_id,
            sf.documento_id,
            e.nombre AS empresa,
            e.rtn,
            IFNULL(d.nombre, 'Sin documento') AS documento,
            sf.cai,
            sf.prefijo,
            sf.siguiente,
            sf.rango_inicial,
            sf.rango_final,
            sf.fecha_activacion,
            sf.fecha_limite,
            sf.activo,
            sf.fecha_registro,
            sf.relleno
        $from_sql
        $where_sql
        ORDER BY sf.secuencia_facturacion_id ASC
        LIMIT ?, ?
    ";

    $params_registro = $params;
    $types_registro = $types . "ii";
    $params_registro[] = $limit;
    $params_registro[] = $nroLotes;

    $result = ejecutar_consulta($mysqli, $sql_registro, $types_registro, $params_registro);

    $tabla = '
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr class="bg-info text-white">
                        <th width="3%">No.</th>
                        <th width="18%">Empresa</th>
                        <th width="12%">Documento</th>
                        <th width="10%">RTN</th>
                        <th width="13%">Número Siguiente</th>
                        <th width="12%">Rango Inicial</th>
                        <th width="12%">Rango Final</th>
                        <th width="9%">Activación</th>
                        <th width="9%">Límite</th>
                        <th width="8%">Estado</th>
                        <th width="8%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    $i = $limit + 1;

    while ($row = $result->fetch_assoc()) {

        $numero_siguiente = formato_numero_documento(
            $row['prefijo'],
            $row['siguiente'],
            $row['relleno']
        );

        $rango_inicial = $row['prefijo'] . str_pad((int)$row['rango_inicial'], (int)$row['relleno'], "0", STR_PAD_LEFT);
        $rango_final = $row['prefijo'] . str_pad((int)$row['rango_final'], (int)$row['relleno'], "0", STR_PAD_LEFT);

        $tabla .= '
            <tr>
                <td>'.limpiar_texto($i).'</td>
                <td>
                    <strong>'.limpiar_texto($row['empresa']).'</strong>
                </td>
                <td>'.badge_documento($row['documento_id'], $row['documento']).'</td>
                <td>'.limpiar_texto($row['rtn']).'</td>
                <td>
                    <span class="font-weight-bold text-primary">
                        '.limpiar_texto($numero_siguiente).'
                    </span>
                </td>
                <td>'.limpiar_texto($rango_inicial).'</td>
                <td>'.limpiar_texto($rango_final).'</td>
                <td>'.limpiar_texto($row['fecha_activacion']).'</td>
                <td>'.limpiar_texto($row['fecha_limite']).'</td>
                <td>'.badge_estado($row['activo']).'</td>
                <td>'.acciones_secuencia($row['secuencia_facturacion_id']).'</td>
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