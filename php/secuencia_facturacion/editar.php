<?php
session_start();
include "../funtions.php";

header('Content-Type: application/json; charset=utf-8');

$mysqli = connect_mysqli();

$secuencia_facturacion_id = intval($_POST['secuencia_facturacion_id'] ?? 0);

if ($secuencia_facturacion_id <= 0) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "ID de secuencia inválido"
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    SELECT
        secuencia_facturacion_id,
        empresa_id,
        documento_id,
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
        comentario
    FROM secuencia_facturacion
    WHERE secuencia_facturacion_id = ?
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        "ok" => false,
        "mensaje" => $mysqli->error
    ]);
    exit;
}

$stmt->bind_param("i", $secuencia_facturacion_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();

    echo json_encode([
        "ok" => false,
        "mensaje" => "No se encontró la secuencia"
    ]);
    exit;
}

$row = $result->fetch_assoc();

$datos = [
    "secuencia_facturacion_id" => $row["secuencia_facturacion_id"],
    "empresa_id"              => $row["empresa_id"],
    "documento_id"            => $row["documento_id"],
    "cai"                     => $row["cai"],
    "prefijo"                 => $row["prefijo"],
    "relleno"                 => $row["relleno"],
    "incremento"              => $row["incremento"],
    "siguiente"               => $row["siguiente"],
    "rango_inicial"           => $row["rango_inicial"],
    "rango_final"             => $row["rango_final"],
    "fecha_activacion"        => $row["fecha_activacion"],
    "fecha_limite"            => $row["fecha_limite"],
    "activo"                  => $row["activo"],
    "comentario"              => $row["comentario"]
];

echo json_encode([
    "ok" => true,
    "data" => $datos
]);

$stmt->close();
$mysqli->close();