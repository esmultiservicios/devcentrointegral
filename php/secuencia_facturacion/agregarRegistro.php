<?php
session_start();
include "../funtions.php";

$mysqli = connect_mysqli();

// ==========================
// FECHAS
// ==========================
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

// ==========================
// USUARIO
// ==========================
$usuario = $_SESSION['colaborador_id'] ?? 0;

// ==========================
// DATOS
// ==========================
$secuencia_facturacion_id = intval($_POST['secuencia_facturacion_id'] ?? 0);
$empresa                  = intval($_POST['empresa'] ?? 0);
$cai                      = $_POST['cai'] ?? '';
$prefijo                  = $_POST['prefijo'] ?? '';
$relleno                  = intval($_POST['relleno'] ?? 0);
$incremento               = intval($_POST['incremento'] ?? 1);
$siguiente                = intval($_POST['siguiente'] ?? 1);
$rango_inicial            = $_POST['rango_inicial'] ?? 0;
$rango_final              = $_POST['rango_final'] ?? 0;
$fecha_activacion         = $_POST['fecha_activacion'] ?? $fecha;
$fecha_limite             = $_POST['fecha_limite'] ?? $fecha;
$estado                   = intval($_POST['estado'] ?? 0);
$documento_id             = intval($_POST['documento_id'] ?? 0);

$comentario = "";

// ==========================
// VALIDACIONES BÁSICAS
// ==========================
if ($secuencia_facturacion_id <= 0) {
    echo 2;
    exit;
}

if ($estado <= 0) {
    echo 2;
    exit;
}

if ($fecha_limite == "") {
    echo 2;
    exit;
}

// ==========================
// OBTENER DATOS ACTUALES
// ==========================
$stmt_actual = $mysqli->prepare("
    SELECT 
        empresa_id,
        cai,
        prefijo,
        relleno,
        incremento,
        siguiente,
        rango_inicial,
        rango_final,
        fecha_activacion,
        fecha_limite,
        comentario,
        activo,
        usuario,
        fecha_registro,
        documento_id
    FROM secuencia_facturacion
    WHERE secuencia_facturacion_id = ?
    LIMIT 1
");

if (!$stmt_actual) {
    echo 2;
    exit;
}

$stmt_actual->bind_param("i", $secuencia_facturacion_id);
$stmt_actual->execute();
$result_actual = $stmt_actual->get_result();

if ($result_actual->num_rows == 0) {
    $stmt_actual->close();
    echo 2;
    exit;
}

$data_actual = $result_actual->fetch_assoc();
$stmt_actual->close();

// ==========================
// VALIDAR SI YA TIENE FACTURAS
// ==========================
$tiene_facturas = false;

$stmt_facturas = $mysqli->prepare("
    SELECT facturas_id
    FROM facturas
    WHERE secuencia_facturacion_id = ?
    LIMIT 1
");

if (!$stmt_facturas) {
    echo 2;
    exit;
}

$stmt_facturas->bind_param("i", $secuencia_facturacion_id);
$stmt_facturas->execute();
$result_facturas = $stmt_facturas->get_result();

if ($result_facturas->num_rows > 0) {
    $tiene_facturas = true;
}

$stmt_facturas->close();

// ==========================
// SI YA TIENE FACTURAS
// SOLO PERMITIR FECHA LÍMITE Y ESTADO
// ==========================
if ($tiene_facturas) {

    $stmt = $mysqli->prepare("
        UPDATE secuencia_facturacion SET
            fecha_limite = ?,
            activo = ?
        WHERE secuencia_facturacion_id = ?
    ");

    if (!$stmt) {
        echo 2;
        exit;
    }

    $stmt->bind_param(
        "sii",
        $fecha_limite,
        $estado,
        $secuencia_facturacion_id
    );

    if (!$stmt->execute()) {
        $stmt->close();
        echo 2;
        exit;
    }

    $stmt->close();

    $historial_numero = historial();
    $estado_historial = "Modificar";
    $modulo = "Secuencia Facturación";
    $observacion = "Secuencia con facturas modificada. Solo se actualizó fecha límite y estado. Prefijo " . $data_actual['prefijo'];

    $cero1 = 0;
    $cero2 = 0;
    $cero3 = 0;
    $cero4 = 0;

    $stmt_hist = $mysqli->prepare("
        INSERT INTO historial 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    if (!$stmt_hist) {
        echo 2;
        exit;
    }

    $stmt_hist->bind_param(
        "iiisiiisssis",
        $historial_numero,
        $cero1,
        $cero2,
        $modulo,
        $secuencia_facturacion_id,
        $cero3,
        $cero4,
        $fecha,
        $estado_historial,
        $observacion,
        $usuario,
        $fecha_registro
    );

    if (!$stmt_hist->execute()) {
        $stmt_hist->close();
        echo 2;
        exit;
    }

    $stmt_hist->close();

    echo 1;
    $mysqli->close();
    exit;
}

// ==========================
// SI NO TIENE FACTURAS
// PERMITIR MODIFICACIÓN COMPLETA
// ==========================
if ($empresa <= 0) {
    echo 2;
    exit;
}

if ($relleno <= 0) {
    echo 2;
    exit;
}

if ($documento_id <= 0) {
    echo 2;
    exit;
}

if ($cai == "") {
    echo 2;
    exit;
}

if ($prefijo == "") {
    echo 2;
    exit;
}

if ($fecha_activacion == "") {
    echo 2;
    exit;
}

// ==========================
// FORMATO
// ==========================
$rango_inicial = str_pad($rango_inicial, $relleno, "0", STR_PAD_LEFT);
$rango_final   = str_pad($rango_final, $relleno, "0", STR_PAD_LEFT);

// ==========================
// VALIDAR DUPLICADO ACTIVO
// ==========================
if ($estado == 1) {

    $stmt_dup = $mysqli->prepare("
        SELECT secuencia_facturacion_id
        FROM secuencia_facturacion
        WHERE activo = 1
        AND empresa_id = ?
        AND documento_id = ?
        AND secuencia_facturacion_id <> ?
        LIMIT 1
    ");

    if (!$stmt_dup) {
        echo 2;
        exit;
    }

    $stmt_dup->bind_param("iii", $empresa, $documento_id, $secuencia_facturacion_id);
    $stmt_dup->execute();
    $result_dup = $stmt_dup->get_result();

    if ($result_dup->num_rows > 0) {
        $stmt_dup->close();
        echo 3;
        exit;
    }

    $stmt_dup->close();
}

// ==========================
// UPDATE COMPLETO
// ==========================
$stmt = $mysqli->prepare("
    UPDATE secuencia_facturacion SET
        empresa_id = ?,
        cai = ?,
        prefijo = ?,
        relleno = ?,
        incremento = ?,
        siguiente = ?,
        rango_inicial = ?,
        rango_final = ?,
        fecha_activacion = ?,
        fecha_limite = ?,
        comentario = ?,
        activo = ?,
        documento_id = ?
    WHERE secuencia_facturacion_id = ?
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param(
    "issiiisssssiii",
    $empresa,
    $cai,
    $prefijo,
    $relleno,
    $incremento,
    $siguiente,
    $rango_inicial,
    $rango_final,
    $fecha_activacion,
    $fecha_limite,
    $comentario,
    $estado,
    $documento_id,
    $secuencia_facturacion_id
);

if (!$stmt->execute()) {
    $stmt->close();
    echo 2;
    exit;
}

$stmt->close();

// ==========================
// HISTORIAL GENERAL
// ==========================
$historial_numero = historial();
$estado_historial = "Modificar";
$modulo = "Secuencia Facturación";
$observacion = "Secuencia modificada ($documento_id) prefijo $prefijo";

$cero1 = 0;
$cero2 = 0;
$cero3 = 0;
$cero4 = 0;

$stmt_hist = $mysqli->prepare("
    INSERT INTO historial 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt_hist) {
    echo 2;
    exit;
}

$stmt_hist->bind_param(
    "iiisiiisssis",
    $historial_numero,
    $cero1,
    $cero2,
    $modulo,
    $secuencia_facturacion_id,
    $cero3,
    $cero4,
    $fecha,
    $estado_historial,
    $observacion,
    $usuario,
    $fecha_registro
);

if (!$stmt_hist->execute()) {
    $stmt_hist->close();
    echo 2;
    exit;
}

$stmt_hist->close();

// ==========================
// TODO OK
// ==========================
echo 1;

$mysqli->close();