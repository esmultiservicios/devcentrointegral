<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include "../funtions.php";

/* ============================================================
   CONEXIÓN A DB
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   FUNCIONES AUXILIARES
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

function consultar_todos($mysqli, $sql, $types = "", $params = array()) {
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
    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();

    return $data;
}

function edad_detalle($fecha_nacimiento) {
    $respuesta = array(
        "anos" => 0,
        "meses" => 0,
        "dias" => 0,
        "edad_completa" => "0 Años, 0 Meses y 0 Días"
    );

    if ($fecha_nacimiento === "" || $fecha_nacimiento === null || $fecha_nacimiento === "0000-00-00") {
        return $respuesta;
    }

    $valores_array = getEdad($fecha_nacimiento);

    $anos = isset($valores_array['anos']) ? (int)$valores_array['anos'] : 0;
    $meses = isset($valores_array['meses']) ? (int)$valores_array['meses'] : 0;
    $dias = isset($valores_array['dias']) ? (int)$valores_array['dias'] : 0;

    $palabra_anos = ($anos == 1) ? "Año" : "Años";
    $palabra_mes = ($meses == 1) ? "Mes" : "Meses";
    $palabra_dia = ($dias == 1) ? "Día" : "Días";

    $respuesta["anos"] = $anos;
    $respuesta["meses"] = $meses;
    $respuesta["dias"] = $dias;
    $respuesta["edad_completa"] = $anos . " " . $palabra_anos . ", " . $meses . " " . $palabra_mes . " y " . $dias . " " . $palabra_dia;

    return $respuesta;
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

$pacientes_id = post_int("pacientes_id");
$agenda_id = post_int("agenda_id");

if ($pacientes_id <= 0 || $agenda_id <= 0) {
    responder_json(false, "No se recibió el paciente o la agenda correctamente.");
}

/* ============================================================
   PROCESO
============================================================ */

try {

    /* ============================================================
       CONSULTAR DATOS DEL PACIENTE / AGENDA
    ============================================================ */

    $paciente = consultar_uno(
        $mysqli,
        "SELECT 
            a.agenda_id,
            a.pacientes_id,
            a.servicio_id,
            a.colaborador_id,
            CAST(a.fecha_cita AS DATE) AS fecha_cita,
            p.identidad,
            p.fecha_nacimiento,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente,
            p.localidad,
            p.religion_id,
            p.profesion_id
        FROM agenda AS a
        INNER JOIN pacientes AS p
            ON a.pacientes_id = p.pacientes_id
        WHERE a.agenda_id = ?
          AND a.pacientes_id = ?
        LIMIT 1",
        "ii",
        array($agenda_id, $pacientes_id)
    );

    if (!$paciente) {
        responder_json(false, "No se encontró información para esta agenda.");
    }

    $fecha_nacimiento = $paciente['fecha_nacimiento'];
    $edad = edad_detalle($fecha_nacimiento);

    $servicio_id = (int)$paciente['servicio_id'];
    $colaborador_id = (int)$paciente['colaborador_id'];
    $fecha_cita = $paciente['fecha_cita'];

    /* ============================================================
       ÚLTIMA HISTORIA CLÍNICA DEL PACIENTE
    ============================================================ */

    $historia = consultar_uno(
        $mysqli,
        "SELECT 
            antecedentes,
            historia_clinica,
            examen_fisico,
            diagnostico
        FROM atenciones_medicas
        WHERE pacientes_id = ?
        ORDER BY atencion_id DESC
        LIMIT 1",
        "i",
        array($pacientes_id)
    );

    $antecedentes = "";
    $historia_clinica = "";
    $examen_fisico = "";
    $diagnostico = "";

    if ($historia) {
        $antecedentes = $historia['antecedentes'];
        $historia_clinica = $historia['historia_clinica'];
        $examen_fisico = $historia['examen_fisico'];
        $diagnostico = $historia['diagnostico'];
    }

    /* ============================================================
       SEGUIMIENTOS DEL PACIENTE
    ============================================================ */

    $seguimientos = consultar_todos(
        $mysqli,
        "SELECT 
            fecha,
            seguimiento
        FROM atenciones_medicas
        WHERE pacientes_id = ?
        ORDER BY fecha DESC, atencion_id DESC",
        "i",
        array($pacientes_id)
    );

    $seguimiento_consulta = "";

    foreach ($seguimientos as $registro_seguimiento) {
        $fecha = $registro_seguimiento['fecha'];
        $seguimiento = $registro_seguimiento['seguimiento'];

        if (trim($seguimiento) !== "") {
            $seguimiento_consulta .= "Fecha: " . $fecha . "\n" . $seguimiento . "\n\n";
        }
    }

    /* ============================================================
       PRECLÍNICA
    ============================================================ */

    $preclinica = "";

    $datos_preclinica = consultar_uno(
        $mysqli,
        "SELECT 
            pre.preclinica_id,
            pre.pa,
            pre.fr,
            pre.fc,
            pre.t AS temperatura,
            pre.peso,
            pre.talla,
            pre.observacion
        FROM preclinica AS pre
        WHERE pre.pacientes_id = ?
          AND pre.colaborador_id = ?
          AND pre.servicio_id = ?
          AND pre.fecha = ?
        LIMIT 1",
        "iiis",
        array($pacientes_id, $colaborador_id, $servicio_id, $fecha_cita)
    );

    if ($datos_preclinica) {
        $preclinica = "PA: " . $datos_preclinica['pa'] .
            " FR: " . $datos_preclinica['fr'] .
            " FC: " . $datos_preclinica['fc'] .
            " Temperatura: " . $datos_preclinica['temperatura'] .
            " Peso: " . $datos_preclinica['peso'] .
            " Talla: " . $datos_preclinica['talla'];
    }

    /* ============================================================
       RESPUESTA JSON
    ============================================================ */

    $data = array(
        "agenda_id" => (int)$paciente['agenda_id'],
        "pacientes_id" => (int)$paciente['pacientes_id'],
        "identidad" => $paciente['identidad'],
        "paciente" => $paciente['paciente'],
        "fecha_nacimiento" => $paciente['fecha_nacimiento'],
        "edad_anos" => $edad['anos'],
        "edad_completa" => $edad['edad_completa'],
        "localidad" => $paciente['localidad'],
        "religion_id" => (int)$paciente['religion_id'],
        "profesion_id" => (int)$paciente['profesion_id'],
        "fecha_cita" => $paciente['fecha_cita'],
        "servicio_id" => $servicio_id,
        "colaborador_id" => $colaborador_id,
        "antecedentes" => $antecedentes,
        "historia_clinica" => $historia_clinica,
        "examen_fisico" => $examen_fisico,
        "diagnostico" => $diagnostico,
        "seguimiento" => $seguimiento_consulta,
        "preclinica" => $preclinica
    );

    responder_json(true, "Datos cargados correctamente.", $data);

} catch (Exception $e) {
    responder_json(false, "No se pudo cargar la información. " . $e->getMessage());
}

$mysqli->close();