<?php
session_start();
include "../funtions.php";

$mysqli = connect_mysqli();

// ==========================
// VARIABLES
// ==========================
$secuencia_facturacion_id = isset($_POST['secuencia_facturacion_id']) ? intval($_POST['secuencia_facturacion_id']) : 0;
$comentario = isset($_POST['comentario']) ? cleanStringStrtolower($_POST['comentario']) : "";
$usuario = $_SESSION['colaborador_id'] ?? 0;
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");

// Validar ID recibido
if ($secuencia_facturacion_id <= 0) {
    echo 2;
    exit;
}

// ==========================
// 1. OBTENER DATOS DE LA SECUENCIA
// ==========================
$stmt = $mysqli->prepare("
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
        activo, 
        documento_id
    FROM secuencia_facturacion
    WHERE secuencia_facturacion_id = ?
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param("i", $secuencia_facturacion_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();
    echo 2;
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// ==========================
// 2. VALIDAR SI EXISTE EN FACTURAS
// ==========================
$stmt = $mysqli->prepare("
    SELECT facturas_id 
    FROM facturas 
    WHERE secuencia_facturacion_id = ? 
    LIMIT 1
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param("i", $secuencia_facturacion_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo 3; // No se puede eliminar porque ya tiene facturas asociadas
    exit;
}

$stmt->close();

// ==========================
// 3. ELIMINAR SECUENCIA
// ==========================
$stmt = $mysqli->prepare("
    DELETE FROM secuencia_facturacion 
    WHERE secuencia_facturacion_id = ?
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param("i", $secuencia_facturacion_id);

if (!$stmt->execute()) {
    $stmt->close();
    echo 2;
    exit;
}

$stmt->close();

// ==========================
// 4. INSERTAR EN HISTORIAL DE SECUENCIA
// ==========================
$historial_id = correlativo('secuencia_facturacion_historial_id', 'secuencia_facturacion_historial');

$empresa_id     = intval($data['empresa_id']);
$cai            = $data['cai'];
$prefijo        = $data['prefijo'];
$relleno        = intval($data['relleno']);
$incremento     = intval($data['incremento']);
$siguiente      = intval($data['siguiente']);
$rango_inicial  = $data['rango_inicial'];
$rango_final    = $data['rango_final'];
$fecha_act      = $data['fecha_activacion'];
$fecha_lim      = $data['fecha_limite'];
$activo         = intval($data['activo']);

$stmt = $mysqli->prepare("
    INSERT INTO secuencia_facturacion_historial
    (
        secuencia_facturacion_historial_id, 
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
        activo, 
        usuario, 
        comentario, 
        fecha_registro
    )
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo 2;
    exit;
}

$stmt->bind_param(
    "iissiiisssssiiss",
    $historial_id,
    $secuencia_facturacion_id,
    $empresa_id,
    $cai,
    $prefijo,
    $relleno,
    $incremento,
    $siguiente,
    $rango_inicial,
    $rango_final,
    $fecha_act,
    $fecha_lim,
    $activo,
    $usuario,
    $comentario,
    $fecha_registro
);

if (!$stmt->execute()) {
    $stmt->close();
    echo 2;
    exit;
}

$stmt->close();

// ==========================
// 5. INSERTAR EN HISTORIAL GENERAL
// ==========================
$historial_numero = historial();
$estado_historial = "Eliminar";
$observacion = "Se eliminó la secuencia con prefijo $prefijo rango $rango_inicial - $rango_final";
$modulo = "Secuencia Facturación";

$cero1 = 0;
$cero2 = 0;
$cero3 = 0;
$cero4 = 0;

$stmt = $mysqli->prepare("
    INSERT INTO historial
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo 2;
    exit;
}

/*
    Orden de tipos corregido:

    1  historial_numero          i
    2  cero1                     i
    3  cero2                     i
    4  modulo                    s
    5  secuencia_facturacion_id  i
    6  cero3                     i
    7  cero4                     i
    8  fecha                     s
    9  estado_historial          s
    10 observacion               s
    11 usuario                   i
    12 fecha_registro            s
*/

$stmt->bind_param(
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

if (!$stmt->execute()) {
    $stmt->close();
    echo 2;
    exit;
}

$stmt->close();

// ==========================
// TODO OK
// ==========================
echo 1;

$mysqli->close();