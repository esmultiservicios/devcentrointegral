<?php
//getDocumentos.php
include "../funtions.php";

header('Content-Type: application/json'); // 🔥 importante

$mysqli = connect_mysqli();

$query = "SELECT documento_id, nombre 
          FROM documento 
          WHERE estado = 1";

$result = $mysqli->query($query);

if (!$result) {
    echo json_encode([
        "error" => true,
        "mensaje" => $mysqli->error
    ]);
    exit;
}

$datos = array();

while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos);

$mysqli->close();