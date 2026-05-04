<?php
session_start();
include "../funtions.php";

$mysqli = connect_mysqli();

$secuencia_facturacion_id = intval($_POST['secuencia_facturacion_id'] ?? 0);

if ($secuencia_facturacion_id <= 0) {
    echo 2;
    exit;
}

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
    echo 1; // Tiene facturas
} else {
    echo 0; // No tiene facturas
}

$stmt->close();
$mysqli->close();