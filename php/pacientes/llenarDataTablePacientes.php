<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include '../funtions.php';

/* ============================================================
   CONEXIÓN A DB
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   FUNCIONES AUXILIARES
============================================================ */

function responder_data($data) {
    echo json_encode(array(
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

function edad_texto($fecha_nacimiento) {
    if ($fecha_nacimiento === "" || $fecha_nacimiento === null || $fecha_nacimiento === "0000-00-00") {
        return "";
    }

    $valores_array = getEdad($fecha_nacimiento);

    $anos = isset($valores_array['anos']) ? (int)$valores_array['anos'] : 0;
    $meses = isset($valores_array['meses']) ? (int)$valores_array['meses'] : 0;
    $dias = isset($valores_array['dias']) ? (int)$valores_array['dias'] : 0;

    if ($anos > 0) {
        return $anos . " " . ($anos == 1 ? "Año" : "Años");
    }

    if ($meses > 0) {
        return $meses . " " . ($meses == 1 ? "Mes" : "Meses");
    }

    return $dias . " " . ($dias == 1 ? "Día" : "Días");
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id'])) {
    responder_data(array());
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$estado = post_int("estado", 1);
$paciente = post_int("paciente", 1);

if (!in_array($estado, array(1, 2))) {
    $estado = 1;
}

/* ============================================================
   CONSULTAR PACIENTES
============================================================ */

try {

    $sql = "
        SELECT 
            pacientes_id,
            CONCAT(nombre, ' ', apellido) AS paciente,
            identidad,
            telefono1,
            telefono2,
            fecha_nacimiento,
            expediente AS expediente_,
            localidad,
            CASE 
                WHEN estado = 1 THEN 'Activo'
                ELSE 'Inactivo'
            END AS estado,
            CASE 
                WHEN genero = 'H' THEN 'Hombre'
                WHEN genero = 'M' THEN 'Mujer'
                ELSE 'No definido'
            END AS genero,
            CASE 
                WHEN expediente = '0' THEN 'TEMP'
                ELSE expediente
            END AS expediente,
            email
        FROM pacientes
        WHERE estado = ?
        ORDER BY 
            CASE 
                WHEN expediente = '0' THEN 999999999
                ELSE CAST(expediente AS UNSIGNED)
            END ASC,
            nombre ASC,
            apellido ASC
    ";

    $result = ejecutar_consulta(
        $mysqli,
        $sql,
        "i",
        array($estado)
    );

    $arreglo = array();

    while ($data = $result->fetch_assoc()) {

        $data['pacientes_id'] = (int)$data['pacientes_id'];
        $data['edad'] = edad_texto($data['fecha_nacimiento']);

        if ($data['telefono1'] === null || $data['telefono1'] === "") {
            $data['telefono1'] = "";
        }

        if ($data['telefono2'] === null || $data['telefono2'] === "") {
            $data['telefono2'] = "";
        }

        if ($data['email'] === null || $data['email'] === "") {
            $data['email'] = "";
        }

        if ($data['localidad'] === null || $data['localidad'] === "") {
            $data['localidad'] = "";
        }

        if ($data['identidad'] === null || $data['identidad'] === "") {
            $data['identidad'] = "";
        }

        $arreglo[] = $data;
    }

    responder_data($arreglo);

} catch (Exception $e) {
    responder_data(array());
}

$mysqli->close();