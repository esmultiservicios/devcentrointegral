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

function consultar_uno($mysqli, $sql, $types = "", $params = array()) {
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
    $row = null;

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }

    $stmt->close();

    return $row;
}

function tipo_factura_texto($tipo_factura) {
    $tipo_factura = (int)$tipo_factura;

    if ($tipo_factura === 1) {
        return "Contado";
    }

    if ($tipo_factura === 2) {
        return "Crédito";
    }

    if ($tipo_factura === 3) {
        return "Proforma";
    }

    return "Documento";
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
   CONSULTAR FACTURA
============================================================ */

try {

    $row = consultar_uno(
        $mysqli,
        "SELECT 
            f.facturas_id,
            f.fecha,
            DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha_formato,
            f.pacientes_id,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente,
            p.identidad,
            f.colaborador_id,
            CONCAT(c.nombre, ' ', c.apellido) AS profesional,
            f.servicio_id,
            s.nombre AS consultorio,
            f.estado,
            f.tipo_factura,
            f.number,
            f.notas,
            f.fact_empresas_id,
            f.secuencia_facturacion_id,
            IFNULL(sc.prefijo, '') AS prefijo,
            IFNULL(sc.relleno, 8) AS relleno,
            IFNULL(sc.documento_id, 0) AS documento_id,
            IFNULL(d.nombre, '') AS documento_nombre,
            IFNULL(fact.nombre, '') AS empresa_nombre,
            IFNULL(fact.rtn, '') AS empresa_rtn
        FROM facturas AS f
        INNER JOIN pacientes AS p
            ON f.pacientes_id = p.pacientes_id
        INNER JOIN servicios AS s
            ON f.servicio_id = s.servicio_id
        INNER JOIN colaboradores AS c
            ON f.colaborador_id = c.colaborador_id
        LEFT JOIN secuencia_facturacion AS sc
            ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
        LEFT JOIN documento AS d
            ON sc.documento_id = d.documento_id
        LEFT JOIN fact_empresas AS fact
            ON f.fact_empresas_id = fact.fact_empresas_id
        WHERE f.facturas_id = ?
        LIMIT 1",
        "i",
        array($facturas_id)
    );

    if (!$row) {
        responder_json(false, "No se encontró la factura indicada.");
    }

    $numero_documento = formato_numero_documento(
        $row['prefijo'],
        $row['number'],
        $row['relleno']
    );

    $data = array(
        "facturas_id" => (int)$row['facturas_id'],
        "fecha" => $row['fecha'],
        "fecha_formato" => $row['fecha_formato'],
        "pacientes_id" => (int)$row['pacientes_id'],
        "paciente" => $row['paciente'],
        "identidad" => $row['identidad'],
        "colaborador_id" => (int)$row['colaborador_id'],
        "profesional" => $row['profesional'],
        "servicio_id" => (int)$row['servicio_id'],
        "consultorio" => $row['consultorio'],
        "estado" => (int)$row['estado'],
        "tipo_factura" => (int)$row['tipo_factura'],
        "tipo_factura_texto" => tipo_factura_texto($row['tipo_factura']),
        "number" => (int)$row['number'],
        "numero_documento" => $numero_documento,
        "notas" => $row['notas'],
        "fact_empresas_id" => (int)$row['fact_empresas_id'],
        "secuencia_facturacion_id" => (int)$row['secuencia_facturacion_id'],
        "documento_id" => (int)$row['documento_id'],
        "documento_nombre" => $row['documento_nombre'],
        "empresa_nombre" => $row['empresa_nombre'],
        "empresa_rtn" => $row['empresa_rtn']
    );

    responder_json(true, "Factura cargada correctamente.", $data);

} catch (Exception $e) {
    responder_json(false, "No se pudo cargar la factura. " . $e->getMessage());
}

$mysqli->close();