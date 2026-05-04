<?php
//paginar.php
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

    if ($numero <= 0) {
        return '<span class="text-muted font-weight-bold">Aún no se ha generado</span>';
    }

    if ($relleno <= 0) {
        $relleno = 8;
    }

    return '<span class="font-weight-bold text-dark">' . limpiar_texto($prefijo . str_pad($numero, $relleno, "0", STR_PAD_LEFT)) . '</span>';
}

function texto_estado($estado) {
    $estado = (int)$estado;

    $base = 'badge d-inline-flex align-items-center justify-content-center px-3 py-2';
    $style = 'font-size: 13px; border-radius: 20px; min-width: 105px; font-weight: 700; letter-spacing: .2px;';

    if ($estado === 1) {
        return '<span class="'.$base.' badge-secondary" style="'.$style.'">
                    <i class="fas fa-edit mr-1"></i> Borrador
                </span>';
    }

    if ($estado === 2) {
        return '<span class="'.$base.' badge-success" style="'.$style.'">
                    <i class="fas fa-check-circle mr-1"></i> Procesada
                </span>';
    }

    if ($estado === 3) {
        return '<span class="'.$base.' badge-danger" style="'.$style.'">
                    <i class="fas fa-ban mr-1"></i> Cancelada
                </span>';
    }

    if ($estado === 4) {
        return '<span class="'.$base.' badge-warning text-dark" style="'.$style.'">
                    <i class="fas fa-clock mr-1"></i> Crédito
                </span>';
    }

    if ($estado === 5) {
        return '<span class="'.$base.' badge-info" style="'.$style.'">
                    <i class="fas fa-file-alt mr-1"></i> Proforma
                </span>';
    }

    return '<span class="'.$base.' badge-light" style="'.$style.'">
                <i class="fas fa-info-circle mr-1"></i> Sin estado
            </span>';
}

function texto_tipo_documento($tipo_factura, $numero) {
    $tipo_factura = (int)$tipo_factura;
    $numero = (int)$numero;

    if ($tipo_factura === 3 && $numero <= 0) {
        return '
            <div class="d-inline-flex flex-column align-items-center px-3 py-2"
                 style="background:#e8f7fb; color:#05748a; border:1px solid #20a8c0; border-left:5px solid #17a2b8; border-radius:10px; min-width:155px; font-weight:800;">
                <span style="font-size:13px;">
                    <i class="fas fa-file-alt mr-1"></i> PREFACTURA
                </span>
                <small style="font-size:10px; font-weight:700;">Proforma sin generar</small>
            </div>';
    }

    if ($tipo_factura === 3 && $numero > 0) {
        return '
            <div class="d-inline-flex flex-column align-items-center px-3 py-2"
                 style="background:#e8f7fb; color:#05748a; border:1px solid #20a8c0; border-left:5px solid #17a2b8; border-radius:10px; min-width:155px; font-weight:800;">
                <span style="font-size:13px;">
                    <i class="fas fa-file-alt mr-1"></i> PROFORMA
                </span>
                <small style="font-size:10px; font-weight:700;">Generada</small>
            </div>';
    }

    if ($tipo_factura === 1) {
        return '
            <div class="d-inline-flex flex-column align-items-center px-3 py-2"
                 style="background:#eaf2ff; color:#0056b3; border:1px solid #007bff; border-left:5px solid #007bff; border-radius:10px; min-width:155px; font-weight:800;">
                <span style="font-size:13px;">
                    <i class="fas fa-money-bill-wave mr-1"></i> FACTURA
                </span>
                <small style="font-size:10px; font-weight:700;">Contado</small>
            </div>';
    }

    if ($tipo_factura === 2) {
        return '
            <div class="d-inline-flex flex-column align-items-center px-3 py-2"
                 style="background:#fff7e0; color:#856404; border:1px solid #ffc107; border-left:5px solid #ffc107; border-radius:10px; min-width:155px; font-weight:800;">
                <span style="font-size:13px;">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> FACTURA
                </span>
                <small style="font-size:10px; font-weight:700;">Crédito</small>
            </div>';
    }

    return '
        <span class="badge badge-secondary px-3 py-2" style="font-size: 13px; border-radius: 20px;">
            Documento
        </span>';
}

function clase_fila_documento($tipo_factura, $numero) {
    $tipo_factura = (int)$tipo_factura;
    $numero = (int)$numero;

    if ($tipo_factura === 3 && $numero <= 0) {
        return 'style="border-left:6px solid #17a2b8; background:#f3fbfd;"';
    }

    if ($tipo_factura === 1) {
        return 'style="border-left:6px solid #007bff;"';
    }

    if ($tipo_factura === 2) {
        return 'style="border-left:6px solid #ffc107;"';
    }

    return '';
}

function acciones_factura($facturas_id, $estado, $tipo_factura, $numero) {
    $facturas_id = (int)$facturas_id;
    $estado = (int)$estado;
    $tipo_factura = (int)$tipo_factura;
    $numero = (int)$numero;

    $items = "";

    if ($tipo_factura === 3 && $estado === 1 && $numero <= 0) {
        $items .= '
            <a class="dropdown-item" href="javascript:pay('.$facturas_id.');void(0);">
                <i class="fas fa-file-signature text-info mr-2"></i> Generar proforma
            </a>
            <a class="dropdown-item text-danger" href="javascript:deleteBill('.$facturas_id.');void(0);">
                <i class="fas fa-trash mr-2"></i> Eliminar prefactura
            </a>';
    } else {

        if ($estado === 1) {
            $items .= '
                <a class="dropdown-item" href="javascript:pay('.$facturas_id.');void(0);">
                    <i class="fas fa-file-invoice text-primary mr-2"></i> Generar factura
                </a>
                <a class="dropdown-item text-danger" href="javascript:deleteBill('.$facturas_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar factura
                </a>';
        }

        if ($estado === 2) {
            $items .= '
                <a class="dropdown-item" href="javascript:printBill('.$facturas_id.');void(0);">
                    <i class="fas fa-print text-primary mr-2"></i> Imprimir factura
                </a>
                <a class="dropdown-item" href="javascript:mailBill('.$facturas_id.');void(0);">
                    <i class="far fa-paper-plane text-info mr-2"></i> Enviar factura
                </a>';
        }

        if ($estado === 3) {
            $items .= '
                <a class="dropdown-item" href="javascript:printBill('.$facturas_id.');void(0);">
                    <i class="fas fa-print text-primary mr-2"></i> Imprimir factura
                </a>';
        }

        if ($estado === 4) {
            $items .= '
                <a class="dropdown-item" href="javascript:printBill('.$facturas_id.');void(0);">
                    <i class="fas fa-print text-primary mr-2"></i> Imprimir factura
                </a>
                <a class="dropdown-item" href="javascript:pago('.$facturas_id.');void(0);">
                    <i class="fab fa-amazon-pay text-success mr-2"></i> Registrar pago
                </a>';
        }

        if ($estado === 5) {
            $items .= '
                <a class="dropdown-item" href="javascript:printBill('.$facturas_id.');void(0);">
                    <i class="fas fa-print text-info mr-2"></i> Imprimir proforma
                </a>';
        }
    }

    if ($items === "") {
        $items = '
            <span class="dropdown-item text-muted">
                <i class="fas fa-info-circle mr-2"></i> Sin acciones
            </span>';
    }

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownFactura'.$facturas_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownFactura'.$facturas_id.'">
                '.$items.'
            </div>
        </div>';
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

$colaborador_id = (int)$_SESSION['colaborador_id'];
$type = (int)$_SESSION['type'];

$paginaActual = post_int("partida", 1);
$fechai = post_string("fechai", date("Y-m-d"));
$fechaf = post_string("fechaf", date("Y-m-d"));
$dato = post_string("dato");
$clientes = post_int("clientes");
$profesional = post_int("profesional");
$estado = post_int("estado", 1);

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

if (!in_array($estado, array(1, 2, 3, 4, 5))) {
    $estado = 1;
}

/* ============================================================
   ARMAR WHERE
============================================================ */

$where = array();
$params = array();
$types = "";

/*
   En esta pantalla solo se muestran:
   - Facturas normales en borrador.
   - Prefacturas antes de generar proforma.
   Cuando se genera factura/proforma, dejan de estar en estado 1.
*/
if ($estado === 1) {
    $where[] = "(
        f.tipo_factura IN (1, 2)
        OR (
            f.tipo_factura = 3
            AND f.number = 0
            AND f.estado = 1
        )
    )";
} else {
    $where[] = "f.tipo_factura IN (1, 2)";
}

$where[] = "f.estado = ?";
$params[] = $estado;
$types .= "i";

if ($fechai !== "" && $fechaf !== "") {
    $where[] = "f.fecha BETWEEN ? AND ?";
    $params[] = $fechai;
    $params[] = $fechaf;
    $types .= "ss";
}

if ($profesional > 0) {
    $where[] = "f.colaborador_id = ?";
    $params[] = $profesional;
    $types .= "i";
}

if ($clientes > 0) {
    $where[] = "f.pacientes_id = ?";
    $params[] = $clientes;
    $types .= "i";
}

if ($estado === 2 || $estado === 4) {
    $where[] = "f.usuario = ?";
    $params[] = $colaborador_id;
    $types .= "i";
}

if ($dato !== "") {
    $where[] = "(
        CONCAT(p.nombre, ' ', p.apellido) LIKE ?
        OR p.apellido LIKE ?
        OR p.identidad LIKE ?
        OR CAST(f.number AS CHAR) LIKE ?
        OR CONCAT(IFNULL(sc.prefijo, ''), LPAD(f.number, IFNULL(sc.relleno, 8), '0')) LIKE ?
    )";

    $buscarCompleto = "%" . $dato . "%";
    $buscarInicio = $dato . "%";

    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;

    $types .= "sssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$from_sql = "
    FROM facturas AS f
    INNER JOIN pacientes AS p
        ON f.pacientes_id = p.pacientes_id
    LEFT JOIN secuencia_facturacion AS sc
        ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
    INNER JOIN servicios AS s
        ON f.servicio_id = s.servicio_id
    INNER JOIN colaboradores AS c
        ON f.colaborador_id = c.colaborador_id
    LEFT JOIN (
        SELECT 
            facturas_id,
            SUM(precio * cantidad) AS importe,
            SUM(isv_valor) AS isv,
            SUM(descuento) AS descuento,
            SUM((precio * cantidad) + isv_valor - descuento) AS neto
        FROM facturas_detalle
        GROUP BY facturas_id
    ) AS fd
        ON f.facturas_id = fd.facturas_id
";

/* ============================================================
   CONTAR REGISTROS
============================================================ */

try {

    $sql_count = "
        SELECT COUNT(*) AS total
        $from_sql
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
            f.facturas_id,
            DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente,
            p.identidad,
            CONCAT(c.nombre, ' ', c.apellido) AS profesional,
            f.estado,
            f.tipo_factura,
            s.nombre AS consultorio,
            IFNULL(sc.prefijo, '') AS prefijo,
            f.number AS numero,
            IFNULL(sc.relleno, 8) AS relleno,
            IFNULL(fd.importe, 0) AS importe,
            IFNULL(fd.isv, 0) AS isv,
            IFNULL(fd.descuento, 0) AS descuento,
            IFNULL(fd.neto, 0) AS neto
        $from_sql
        $where_sql
        ORDER BY f.facturas_id DESC
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
                        <th width="7%">Fecha</th>
                        <th width="13%">Documento</th>
                        <th width="12%">Número</th>
                        <th width="16%">Cliente</th>
                        <th width="8%">Identidad</th>
                        <th width="13%">Profesional</th>
                        <th width="10%">Consultorio</th>
                        <th width="7%">Importe</th>
                        <th width="7%">ISV</th>
                        <th width="7%">Descuento</th>
                        <th width="7%">Neto</th>
                        <th width="9%">Estado</th>
                        <th width="8%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    $i = $limit + 1;

    while ($registro2 = $result->fetch_assoc()) {

        $facturas_id = (int)$registro2['facturas_id'];
        $estadoActual = (int)$registro2['estado'];
        $tipoFactura = (int)$registro2['tipo_factura'];
        $numeroEntero = (int)$registro2['numero'];

        $numero = formato_numero_documento(
            $registro2['prefijo'],
            $registro2['numero'],
            $registro2['relleno']
        );

        $claseFila = clase_fila_documento($tipoFactura, $numeroEntero);

        $tabla .= '
            <tr '.$claseFila.'>
                <td>'.limpiar_texto($i).'</td>
                <td>'.limpiar_texto($registro2['fecha']).'</td>
                <td>'.texto_tipo_documento($tipoFactura, $numeroEntero).'</td>
                <td>'.$numero.'</td>
                <td>'.limpiar_texto($registro2['paciente']).'</td>
                <td>'.limpiar_texto($registro2['identidad']).'</td>
                <td>'.limpiar_texto($registro2['profesional']).'</td>
                <td>'.limpiar_texto($registro2['consultorio']).'</td>
                <td>'.number_format((float)$registro2['importe'], 2).'</td>
                <td>'.number_format((float)$registro2['isv'], 2).'</td>
                <td>'.number_format((float)$registro2['descuento'], 2).'</td>
                <td>'.number_format((float)$registro2['neto'], 2).'</td>
                <td>'.texto_estado($estadoActual).'</td>
                <td>'.acciones_factura($facturas_id, $estadoActual, $tipoFactura, $numeroEntero).'</td>
            </tr>
        ';

        $i++;
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="14" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="14">
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