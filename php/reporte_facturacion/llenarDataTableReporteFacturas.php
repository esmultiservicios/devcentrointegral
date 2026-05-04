<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include "../funtions.php";

/* ============================================================
   CONEXIÓN
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   FUNCIONES
============================================================ */

function responder_data($data) {
    echo json_encode(array("data" => $data));
    exit;
}

function responder_error($mensaje) {
    echo json_encode(array(
        "error" => true,
        "mensaje" => $mensaje,
        "data" => array()
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
        return "Aún no se ha generado";
    }

    if ($relleno <= 0) {
        $relleno = 8;
    }

    return $prefijo . str_pad($numero, $relleno, "0", STR_PAD_LEFT);
}

function tipo_documento_texto($tipo_factura, $documento_id, $nombre_documento, $estado, $numero) {
    $tipo_factura = (int)$tipo_factura;
    $documento_id = (int)$documento_id;
    $estado = (int)$estado;
    $numero = (int)$numero;

    /*
       Proforma confirmada:
       documento_id = 4
       tipo_factura = 3
       estado = 5
       number > 0
    */
    if ($documento_id === 4 && $tipo_factura === 3 && $estado === 5 && $numero > 0) {
        return "Proforma";
    }

    /*
       Prefactura temporal:
       documento_id = 4
       tipo_factura = 3
       estado = 1
       number = 0
    */
    if ($documento_id === 4 && $tipo_factura === 3 && $estado === 1 && $numero <= 0) {
        return "Prefactura";
    }

    if ($tipo_factura === 1) {
        return "Contado";
    }

    if ($tipo_factura === 2) {
        return "Crédito";
    }

    if ($tipo_factura === 3) {
        return "Proforma";
    }

    if ($nombre_documento !== "") {
        return $nombre_documento;
    }

    return "Documento";
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id']) || !isset($_SESSION['type'])) {
    responder_data(array());
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$colaborador_id = (int)$_SESSION['colaborador_id'];
$type = (int)$_SESSION['type'];

$fechai = post_string("fechai", date("Y-m-01"));
$fechaf = post_string("fechaf", date("Y-m-d"));
$clientes = post_int("clientes");
$profesional = post_int("profesional");
$estado = post_int("estado", 1);
$documento_id = post_int("documento_id", 1);

if (!in_array($documento_id, array(1, 2, 3, 4))) {
    $documento_id = 1;
}

/*
   Estados reales que usa tu tabla:
   1 = Borrador / Prefactura temporal
   2 = Pagado / Factura contado generada
   3 = Cancelado
   4 = Crédito
   5 = Proforma confirmada
*/
if (!in_array($estado, array(1, 2, 3, 4, 5))) {
    $estado = 1;
}

/* ============================================================
   ARMAR WHERE
============================================================ */

$where = array();
$params = array();
$types = "";

$where[] = "f.fecha BETWEEN ? AND ?";
$params[] = $fechai;
$params[] = $fechaf;
$types .= "ss";

/*
   Relación correcta:
   f.secuencia_facturacion_id -> sc.secuencia_facturacion_id -> sc.documento_id
*/
$where[] = "sc.documento_id = ?";
$params[] = $documento_id;
$types .= "i";

/*
   Documento 1 = Facturas normales.
   Documento 4 = Proformas.
*/
if ($documento_id === 1) {

    /*
       Facturas normales:
       tipo_factura 1 = Contado
       tipo_factura 2 = Crédito
    */
    $where[] = "f.tipo_factura IN (1, 2)";

    /*
       Conserva tu lógica anterior:
       estado 1 del filtro = ver facturas completadas/pagadas y crédito.
    */
    if ($estado === 1) {

        $where[] = "f.estado IN (2, 4)";

    } elseif ($estado === 2) {

        $where[] = "f.tipo_factura = 1";
        $where[] = "f.estado = 2";

    } elseif ($estado === 3) {

        $where[] = "f.estado = 3";

    } elseif ($estado === 4) {

        $where[] = "f.tipo_factura = 2";
        $where[] = "f.estado = 4";

    } else {

        $where[] = "f.estado IN (2, 4)";
    }

} elseif ($documento_id === 4) {

    /*
       Proformas:
       tipo_factura = 3
    */
    $where[] = "f.tipo_factura = 3";

    /*
       IMPORTANTE:
       En tu flujo, una proforma confirmada queda:
       estado = 5
       number > 0

       Si el filtro manda estado = 1, NO debemos traer prefacturas temporales.
       Entonces para documento_id = 4, estado 1 se interpreta como proformas confirmadas.
    */
    if ($estado === 1) {

        $where[] = "f.estado = 5";
        $where[] = "f.number > 0";

    } elseif ($estado === 5) {

        $where[] = "f.estado = 5";
        $where[] = "f.number > 0";

    } elseif ($estado === 3) {

        $where[] = "f.estado = 3";

    } else {

        /*
           Si mandan estado 2 o 4 por error en proformas,
           igual mostramos proformas confirmadas.
        */
        $where[] = "f.estado = 5";
        $where[] = "f.number > 0";
    }
}

if ($clientes > 0) {
    $where[] = "f.pacientes_id = ?";
    $params[] = $clientes;
    $types .= "i";
}

if ($profesional > 0) {
    $where[] = "f.colaborador_id = ?";
    $params[] = $profesional;
    $types .= "i";
}

/*
   Si no es tipo administrador/supervisor, filtra por usuario.
*/
if (!in_array($type, array(1, 2, 4))) {
    $where[] = "f.usuario = ?";
    $params[] = $colaborador_id;
    $types .= "i";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA
============================================================ */

$sql = "
    SELECT 
        f.facturas_id,
        f.pacientes_id,
        f.fecha,
        DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha_formato,
        p.identidad,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente,
        sc.prefijo,
        sc.relleno,
        sc.documento_id,
        d.nombre AS nombre_documento,
        f.number AS numero,
        f.estado,
        f.cierre,
        f.tipo_factura,
        s.nombre AS servicio,
        CONCAT(c.nombre, ' ', c.apellido) AS profesional,
        IFNULL(fd.importe, 0) AS importe,
        IFNULL(fd.isv_neto, 0) AS isv_neto,
        IFNULL(fd.descuento, 0) AS descuento,
        IFNULL(fd.total, 0) AS total
    FROM facturas AS f
    INNER JOIN secuencia_facturacion AS sc
        ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
    INNER JOIN documento AS d
        ON sc.documento_id = d.documento_id
    INNER JOIN pacientes AS p
        ON f.pacientes_id = p.pacientes_id
    INNER JOIN servicios AS s
        ON f.servicio_id = s.servicio_id
    INNER JOIN colaboradores AS c
        ON f.colaborador_id = c.colaborador_id
    LEFT JOIN (
        SELECT 
            facturas_id,
            SUM(precio * cantidad) AS importe,
            SUM(isv_valor) AS isv_neto,
            SUM(descuento) AS descuento,
            SUM((precio * cantidad) + isv_valor - descuento) AS total
        FROM facturas_detalle
        GROUP BY facturas_id
    ) AS fd
        ON f.facturas_id = fd.facturas_id
    $where_sql
    ORDER BY f.fecha DESC, f.number DESC, f.facturas_id DESC
";

/* ============================================================
   EJECUTAR Y RESPONDER
============================================================ */

try {

    $result = ejecutar_consulta($mysqli, $sql, $types, $params);

    $data = array();

    while ($row = $result->fetch_assoc()) {

        $factura = formato_numero_documento(
            $row['prefijo'],
            $row['numero'],
            $row['relleno']
        );

        $tipo_documento = tipo_documento_texto(
            $row['tipo_factura'],
            $row['documento_id'],
            $row['nombre_documento'],
            $row['estado'],
            $row['numero']
        );

        $data[] = array(
            "facturas_id" => (int)$row['facturas_id'],
            "pacientes_id" => (int)$row['pacientes_id'],
            "fecha" => $row['fecha_formato'],
            "identidad" => $row['identidad'],
            "paciente" => $row['paciente'],
            "factura" => $factura,
            "precio" => number_format((float)$row['importe'], 2, ".", ""),
            "isv_neto" => number_format((float)$row['isv_neto'], 2, ".", ""),
            "descuento" => number_format((float)$row['descuento'], 2, ".", ""),
            "total" => number_format((float)$row['total'], 2, ".", ""),
            "servicio" => $row['servicio'],
            "profesional" => $row['profesional'],
            "tipo_documento" => $tipo_documento,
            "tipo_factura" => (int)$row['tipo_factura'],
            "documento_id" => (int)$row['documento_id'],
            "estado" => (int)$row['estado'],
            "cierre" => (int)$row['cierre']
        );
    }

    responder_data($data);

} catch (Exception $e) {
    responder_error($e->getMessage());
}

$mysqli->close();