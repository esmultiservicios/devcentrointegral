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

function responder_json($tabla, $paginacion) {
    echo json_encode(array(
        0 => $tabla,
        1 => $paginacion
    ));
    exit;
}

function post_int($key, $default = 0) {
    if (!isset($_POST[$key]) || $_POST[$key] === "") {
        return $default;
    }

    return (int)$_POST[$key];
}

function post_string($key, $default = "") {
    if (!isset($_POST[$key])) {
        return $default;
    }

    return trim($_POST[$key]);
}

function limpiar_texto($texto) {
    return htmlspecialchars($texto ?? "", ENT_QUOTES, "UTF-8");
}

function limpiar_js_string($texto) {
    return str_replace(
        array("\\", "'", "\"", "\r", "\n"),
        array("\\\\", "\\'", "&quot;", "", ""),
        $texto ?? ""
    );
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

function calcular_edad_texto($fecha_nacimiento) {
    if ($fecha_nacimiento === "" || $fecha_nacimiento === null || $fecha_nacimiento === "0000-00-00") {
        return "Sin fecha";
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

function acciones_paciente($pacientes_id, $expediente_original) {
    $pacientes_id = (int)$pacientes_id;
    $expediente_original = limpiar_js_string($expediente_original);

    return '
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle px-3" type="button" id="dropdownPaciente'.$pacientes_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i> Opciones
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownPaciente'.$pacientes_id.'">

                <a class="dropdown-item" href="javascript:showExpediente('.$pacientes_id.');void(0);">
                    <i class="fas fa-folder-open text-info mr-2"></i> Ver expediente
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="javascript:modal_agregar_expediente('.$pacientes_id.', \''.$expediente_original.'\');void(0);">
                    <i class="fas fa-plus-circle text-success mr-2"></i> Asignar expediente
                </a>

                <a class="dropdown-item" href="javascript:modal_agregar_expediente_manual('.$pacientes_id.');void(0);">
                    <i class="fas fa-keyboard text-warning mr-2"></i> Asignar manual
                </a>

                <a class="dropdown-item" href="javascript:editarRegistro('.$pacientes_id.');void(0);">
                    <i class="fas fa-user-edit text-primary mr-2"></i> Editar paciente
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="javascript:modal_eliminar('.$pacientes_id.');void(0);">
                    <i class="fas fa-trash mr-2"></i> Eliminar paciente
                </a>

            </div>
        </div>';
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id'])) {
    responder_json(
        '<div class="alert alert-danger">La sesión ha expirado. Por favor vuelva a iniciar sesión.</div>',
        ''
    );
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$paginaActual = post_int("partida", 1);
$estado = post_int("estado", 1);
$paciente = post_string("paciente");
$dato = post_string("dato");

if ($paginaActual <= 0) {
    $paginaActual = 1;
}

if (!in_array($estado, array(1, 2))) {
    $estado = 1;
}

/* ============================================================
   ARMAR WHERE SEGURO
============================================================ */

$where = array();
$params = array();
$types = "";

$where[] = "estado = ?";
$params[] = $estado;
$types .= "i";

if ($dato !== "") {
    $where[] = "(
        expediente LIKE ?
        OR nombre LIKE ?
        OR apellido LIKE ?
        OR CONCAT(apellido, ' ', nombre) LIKE ?
        OR CONCAT(nombre, ' ', apellido) LIKE ?
        OR telefono1 LIKE ?
        OR telefono2 LIKE ?
        OR identidad LIKE ?
        OR email LIKE ?
        OR localidad LIKE ?
    )";

    $buscarInicio = $dato . "%";
    $buscarCompleto = "%" . $dato . "%";

    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarInicio;
    $params[] = $buscarCompleto;
    $params[] = $buscarCompleto;

    $types .= "ssssssssss";
}

$where_sql = "WHERE " . implode(" AND ", $where);

/* ============================================================
   CONSULTA BASE
============================================================ */

$select_sql = "
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
        END AS estado_texto,
        CASE 
            WHEN genero = 'H' THEN 'Hombre'
            WHEN genero = 'M' THEN 'Mujer'
            ELSE 'No definido'
        END AS genero_texto,
        CASE 
            WHEN expediente = '0' THEN 'TEMP'
            ELSE expediente
        END AS expediente,
        email
    FROM pacientes
    $where_sql
";

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

try {

    /* ============================================================
       CONTAR REGISTROS
    ============================================================ */

    $sql_count = "
        SELECT COUNT(*) AS total
        FROM pacientes
        $where_sql
    ";

    $result_count = ejecutar_consulta($mysqli, $sql_count, $types, $params);
    $row_count = $result_count->fetch_assoc();

    $nroProductos = (int)$row_count['total'];

    $nroLotes = 15;
    $nroPaginas = ($nroProductos > 0) ? ceil($nroProductos / $nroLotes) : 1;

    if ($paginaActual > $nroPaginas) {
        $paginaActual = $nroPaginas;
    }

    $limit = ($paginaActual <= 1) ? 0 : $nroLotes * ($paginaActual - 1);

    /* ============================================================
       PAGINACIÓN
    ============================================================ */

    $lista = '';

    if ($paginaActual > 1) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination(1);void(0);">Inicio</a></li>';
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual - 1).');void(0);">Anterior '.($paginaActual - 1).'</a></li>';
    }

    if ($paginaActual < $nroPaginas) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.($paginaActual + 1).');void(0);">Siguiente '.($paginaActual + 1).' de '.$nroPaginas.'</a></li>';
    }

    if ($paginaActual > 1) {
        $lista .= '<li class="page-item"><a class="page-link" href="javascript:pagination('.$nroPaginas.');void(0);">Última</a></li>';
    }

    /* ============================================================
       CONSULTAR REGISTROS
    ============================================================ */

    $sql_registro = "
        $select_sql
        ORDER BY 
            CASE 
                WHEN expediente = '0' THEN 999999999
                ELSE CAST(expediente AS UNSIGNED)
            END ASC,
            paciente ASC
        LIMIT ?, ?
    ";

    $params_registro = $params;
    $types_registro = $types . "ii";
    $params_registro[] = $limit;
    $params_registro[] = $nroLotes;

    $result = ejecutar_consulta($mysqli, $sql_registro, $types_registro, $params_registro);

    /* ============================================================
       TABLA
       Mismo concepto visual que paginar facturas:
       table-striped, table-hover, bg-info text-white y dropdown Opciones.
    ============================================================ */

    $tabla = '
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr class="bg-info text-white">
                        <th width="3%">No.</th>
                        <th width="8%">Expediente</th>
                        <th width="10%">Identidad</th>
                        <th width="16%">Paciente</th>
                        <th width="8%">Género</th>
                        <th width="8%">Edad</th>
                        <th width="9%">Teléfono 1</th>
                        <th width="9%">Teléfono 2</th>
                        <th width="13%">Correo</th>
                        <th width="12%">Dirección</th>
                        <th width="8%">Estado</th>
                        <th width="8%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
    ';

    $i = $limit + 1;

    while ($registro2 = $result->fetch_assoc()) {

        $pacientes_id = (int)$registro2['pacientes_id'];
        $edad = calcular_edad_texto($registro2['fecha_nacimiento']);

        $expediente = limpiar_texto($registro2['expediente']);
        $expediente_original = $registro2['expediente_'];

        $identidad = limpiar_texto($registro2['identidad']);
        $paciente_nombre = limpiar_texto($registro2['paciente']);
        $genero = limpiar_texto($registro2['genero_texto']);
        $telefono1 = limpiar_texto($registro2['telefono1']);
        $telefono2 = limpiar_texto($registro2['telefono2']);
        $email = limpiar_texto($registro2['email']);
        $localidad = limpiar_texto($registro2['localidad']);
        $estado_texto = limpiar_texto($registro2['estado_texto']);

        if ($telefono1 === "") {
            $telefono1 = '<span class="text-muted">No registrado</span>';
        }

        if ($telefono2 === "") {
            $telefono2 = '<span class="text-muted">No registrado</span>';
        }

        if ($email === "") {
            $email = '<span class="text-muted">No registrado</span>';
        }

        if ($localidad === "") {
            $localidad = '<span class="text-muted">No registrada</span>';
        }

        $tabla .= '
            <tr>
                <td>'.limpiar_texto($i).'</td>

                <td>
                    <a style="text-decoration:none;" class="font-weight-bold text-primary"
                        title="Información de Usuario"
                        href="javascript:showExpediente('.$pacientes_id.');void(0);">
                        '.$expediente.'
                    </a>
                </td>

                <td>'.$identidad.'</td>
                <td>'.$paciente_nombre.'</td>
                <td>'.$genero.'</td>
                <td>'.limpiar_texto($edad).'</td>
                <td>'.$telefono1.'</td>
                <td>'.$telefono2.'</td>
                <td>'.$email.'</td>
                <td>'.$localidad.'</td>
                <td>'.$estado_texto.'</td>
                <td>'.acciones_paciente($pacientes_id, $expediente_original).'</td>
            </tr>
        ';

        $i++;
    }

    if ($nroProductos === 0) {
        $tabla .= '
            <tr>
                <td colspan="12" class="text-center text-danger">
                    No se encontraron resultados.
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="12">
                    <b><p align="center">Total de Registros Encontrados '.number_format($nroProductos).'</p></b>
                </td>
            </tr>
        ';
    }

    $tabla .= '
                </tbody>
            </table>
        </div>
    ';

    responder_json($tabla, $lista);

} catch (Exception $e) {

    responder_json(
        '<div class="alert alert-danger">Error: '.limpiar_texto($e->getMessage()).'</div>',
        ''
    );
}

$mysqli->close();