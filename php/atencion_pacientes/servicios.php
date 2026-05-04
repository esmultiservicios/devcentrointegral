<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

include "../funtions.php";

/* ============================================================
   CONEXIÓN A DB
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   RECIBIR DATOS
============================================================ */

$agenda_id = 0;
$servicio_seleccionado = 0;

if (isset($_POST['agenda_id']) && $_POST['agenda_id'] !== "") {
    $agenda_id = (int)$_POST['agenda_id'];
}

/* ============================================================
   SI VIENE AGENDA, BUSCAR EL SERVICIO DE ESA AGENDA
============================================================ */

if ($agenda_id > 0) {
    $stmt_agenda = $mysqli->prepare("
        SELECT servicio_id
        FROM agenda
        WHERE agenda_id = ?
        LIMIT 1
    ");

    if ($stmt_agenda) {
        $stmt_agenda->bind_param("i", $agenda_id);
        $stmt_agenda->execute();

        $result_agenda = $stmt_agenda->get_result();

        if ($result_agenda && $result_agenda->num_rows > 0) {
            $row_agenda = $result_agenda->fetch_assoc();
            $servicio_seleccionado = (int)$row_agenda['servicio_id'];
        }

        $stmt_agenda->close();
    }
}

/* ============================================================
   CARGAR TODOS LOS CONSULTORIOS / SERVICIOS
============================================================ */

$options = '<option value="">Seleccione</option>';

$stmt = $mysqli->prepare("
    SELECT servicio_id, nombre
    FROM servicios
    ORDER BY nombre ASC
");

if ($stmt) {
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $servicio_id = (int)$row['servicio_id'];
        $nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');

        $selected = "";

        if ($servicio_id === $servicio_seleccionado) {
            $selected = "selected";
        }

        $options .= '<option value="'.$servicio_id.'" '.$selected.'>'.$nombre.'</option>';
    }

    $stmt->close();
}

$mysqli->close();

echo $options;