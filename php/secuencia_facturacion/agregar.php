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
$empresa           = intval($_POST['empresa'] ?? 0);
$cai               = $_POST['cai'] ?? '';
$prefijo           = $_POST['prefijo'] ?? '';
$relleno           = intval($_POST['relleno'] ?? 0);
$incremento        = intval($_POST['incremento'] ?? 1);
$siguiente         = intval($_POST['siguiente'] ?? 1);
$rango_inicial     = $_POST['rango_inicial'] ?? 0;
$rango_final       = $_POST['rango_final'] ?? 0;
$fecha_activacion  = $_POST['fecha_activacion'] ?? $fecha;
$fecha_limite      = $_POST['fecha_limite'] ?? $fecha;
$estado            = intval($_POST['estado'] ?? 0);
$documento_id      = intval($_POST['documento_id'] ?? 0);

$comentario = "";

// ==========================
// VALIDACIONES BÁSICAS
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

// ==========================
// FORMATO
// ==========================
$rango_inicial = str_pad($rango_inicial, $relleno, "0", STR_PAD_LEFT);
$rango_final   = str_pad($rango_final, $relleno, "0", STR_PAD_LEFT);

// ==========================
// VALIDAR DUPLICADO
// ==========================
$stmt = $mysqli->prepare("
    SELECT secuencia_facturacion_id 
    FROM secuencia_facturacion 
    WHERE activo = 1 
    AND empresa_id = ? 
    AND documento_id = ?
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param("ii", $empresa, $documento_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo 3;
    exit;
}

$stmt->close();

// ==========================
// CORRELATIVO
// ==========================
$correlativo = correlativo('secuencia_facturacion_id', 'secuencia_facturacion');

// ==========================
// INSERT SECUENCIA FACTURACIÓN
// ==========================
$stmt = $mysqli->prepare("
    INSERT INTO secuencia_facturacion 
    (
        secuencia_facturacion_id, 
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
    ) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param(
    "iissiiisssssiisi",
    $correlativo,
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
    $usuario,
    $fecha_registro,
    $documento_id
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
$estado_historial = "Agregar";
$modulo = "Secuencia Facturación";
$observacion = "Nueva secuencia ($documento_id) prefijo $prefijo rango $rango_inicial - $rango_final";

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

/*
    Orden de valores:

    1  historial_numero   int
    2  cero1              int
    3  cero2              int
    4  modulo             string
    5  correlativo        int
    6  cero3              int
    7  cero4              int
    8  fecha              string/date
    9  estado_historial   string
    10 observacion        string
    11 usuario            int
    12 fecha_registro     string/datetime

    Tipos:
    i i i s i i i s s s i s
*/

$stmt_hist->bind_param(
    "iiisiiisssis",
    $historial_numero,
    $cero1,
    $cero2,
    $modulo,
    $correlativo,
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