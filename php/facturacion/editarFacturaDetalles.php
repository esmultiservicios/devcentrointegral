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

function responder_json($ok, $mensaje = "", $data = array()) {
    echo json_encode(array(
        "ok" => $ok,
        "mensaje" => $mensaje,
        "data" => $data
    ));
    exit;
}

function post_int($key, $default = 0) {
    if (!isset($_POST[$key]) || $_POST[$key] === "") {
        return $default;
    }

    return (int)$_POST[$key];
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

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id'])) {
    responder_json(false, "La sesión ha expirado. Por favor vuelva a iniciar sesión.");
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$facturas_id = post_int("facturas_id");

if ($facturas_id <= 0) {
    responder_json(false, "ID de factura inválido.");
}

/* ============================================================
   CONSULTAR DETALLE
============================================================ */

try {

    $result = ejecutar_consulta(
        $mysqli,
        "SELECT 
            fd.facturas_detalle_id,
            fd.productos_id,
            p.nombre AS producto,
            fd.cantidad,
            fd.precio,
            fd.descuento,
            fd.isv_valor,
            p.isv AS producto_isv
        FROM facturas_detalle AS fd
        INNER JOIN facturas AS f
            ON fd.facturas_id = f.facturas_id
        INNER JOIN productos AS p
            ON fd.productos_id = p.productos_id
        WHERE fd.facturas_id = ?
        ORDER BY fd.facturas_detalle_id ASC",
        "i",
        array($facturas_id)
    );

    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            "facturas_detalle_id" => (int)$row['facturas_detalle_id'],
            "productos_id" => (int)$row['productos_id'],
            "producto" => $row['producto'],
            "cantidad" => (float)$row['cantidad'],
            "precio" => (float)$row['precio'],
            "descuento" => (float)$row['descuento'],
            "isv_valor" => (float)$row['isv_valor'],
            "producto_isv" => (int)$row['producto_isv']
        );
    }

    responder_json(true, "Detalle cargado correctamente.", $data);

} catch (Exception $e) {
    responder_json(false, "No se pudo cargar el detalle. " . $e->getMessage());
}

$mysqli->close();