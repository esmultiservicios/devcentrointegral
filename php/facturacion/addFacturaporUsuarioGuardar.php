<?php
// addFacturaporUsuarioGuardar.php
session_start();
header('Content-Type: application/json; charset=utf-8');

include "../funtions.php";

/* ============================================================
   CONEXIÓN
============================================================ */

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8mb4");

/* ============================================================
   FUNCIONES AUXILIARES
============================================================ */

function responder($titulo, $mensaje, $icono, $boton, $formulario = "", $accion = "", $tipoTabla = "", $modal = "", $facturas_id = "", $tipo_factura = 0) {
    echo json_encode(array(
        0 => $titulo,
        1 => $mensaje,
        2 => $icono,
        3 => $boton,
        4 => $formulario,
        5 => $accion,
        6 => $tipoTabla,
        7 => $modal,
        8 => $facturas_id,
        9 => $tipo_factura
    ));
    exit;
}

function post_int($key, $default = 0) {
    if (!isset($_POST[$key]) || $_POST[$key] === "") {
        return $default;
    }

    return (int)$_POST[$key];
}

function post_float_array($array, $i, $default = 0) {
    if (!isset($array[$i]) || $array[$i] === "") {
        return $default;
    }

    return (float)$array[$i];
}

function post_int_array($array, $i, $default = 0) {
    if (!isset($array[$i]) || $array[$i] === "") {
        return $default;
    }

    return (int)$array[$i];
}

function post_string_array($array, $i, $default = "") {
    if (!isset($array[$i])) {
        return $default;
    }

    return trim($array[$i]);
}

function correlativo_local($mysqli, $campo, $tabla) {
    $permitidos = array(
        "facturas_detalle" => "facturas_detalle_id"
    );

    if (!isset($permitidos[$tabla]) || $permitidos[$tabla] !== $campo) {
        throw new Exception("Correlativo no permitido.");
    }

    $sql = "SELECT IFNULL(MAX($campo), 0) + 1 AS siguiente FROM $tabla";
    $result = $mysqli->query($sql);

    if (!$result) {
        throw new Exception("Error generando correlativo: " . $mysqli->error);
    }

    $row = $result->fetch_assoc();

    return (int)$row['siguiente'];
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

function ejecutar_sql($mysqli, $sql, $types = "", $params = array()) {
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error preparando operación: " . $mysqli->error);
    }

    if ($types !== "" && count($params) > 0) {
        bind_params_ref($stmt, $types, $params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando operación: " . $stmt->error);
    }

    $stmt->close();

    return true;
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['empresa_id']) || !isset($_SESSION['colaborador_id'])) {
    responder(
        "Error",
        "La sesión ha expirado. Por favor vuelva a iniciar sesión.",
        "error",
        "btn-danger"
    );
}

/* ============================================================
   RECIBIR DATOS
============================================================ */

$facturas_id = post_int("facturas_id");
$pacientes_id = post_int("pacientes_id");
$colaborador_id = post_int("colaborador_id");
$servicio_id = post_int("servicio_id");

$fecha = isset($_POST['fecha']) && $_POST['fecha'] !== "" ? $_POST['fecha'] : date("Y-m-d");

$empresa_id = (int)$_SESSION['empresa_id'];
$usuario = (int)$_SESSION['colaborador_id'];

$notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";

$activo = 1;
$estado = 1; // Borrador
$numero = 0;
$cierre = 2;

$aseguradora_id = post_int("aseguradora_id");
$fact_empresas_id = post_int("fact_empresas_id");

/*
    tipo_factura:
    1 = Contado
    2 = Crédito
    3 = Proforma
*/
$tipo_factura = post_int("facturas_activo", 1);

if (!in_array($tipo_factura, array(1, 2, 3))) {
    $tipo_factura = 1;
}

$proforma_id = null;

if (isset($_POST['proforma_id']) && $_POST['proforma_id'] !== "") {
    $proforma_id = (int)$_POST['proforma_id'];
}

/* ============================================================
   TIPO DE TABLA / RESPUESTA AJAX
============================================================ */

$tipo = "Facturacion1";

if ($tipo_factura == 2) {
    $tipo = "FacturacionCredito";
}

if ($tipo_factura == 3) {
    $tipo = "Proforma";
}

/* ============================================================
   VALIDACIONES PRINCIPALES
============================================================ */

if ($facturas_id <= 0) {
    responder(
        "Error",
        "No se recibió el ID de la factura que desea actualizar.",
        "error",
        "btn-danger"
    );
}

if ($pacientes_id <= 0 || $colaborador_id <= 0 || $servicio_id <= 0) {
    responder(
        "Error",
        "Lo sentimos, el Paciente, Profesional o Servicio no pueden quedar en blanco, por favor corregir.",
        "error",
        "btn-danger"
    );
}

if (!isset($_POST['productName']) || !is_array($_POST['productName'])) {
    responder(
        "Error",
        "Lo sentimos, debe agregar por lo menos una línea en los detalles de la factura.",
        "error",
        "btn-danger"
    );
}

$productNameArray = $_POST['productName'];
$productoIDArray = isset($_POST['productoID']) ? $_POST['productoID'] : array();
$quantityArray = isset($_POST['quantity']) ? $_POST['quantity'] : array();
$priceArray = isset($_POST['price']) ? $_POST['price'] : array();
$discountArray = isset($_POST['discount']) ? $_POST['discount'] : array();
$totalArray = isset($_POST['total']) ? $_POST['total'] : array();

$detalleValido = false;

for ($i = 0; $i < count($productNameArray); $i++) {
    $productoIDTmp = post_int_array($productoIDArray, $i);
    $productNameTmp = post_string_array($productNameArray, $i);
    $quantityTmp = post_float_array($quantityArray, $i);
    $priceTmp = post_float_array($priceArray, $i);
    $totalTmp = post_float_array($totalArray, $i);

    if ($productoIDTmp > 0 && $productNameTmp !== "" && $quantityTmp > 0 && $priceTmp >= 0 && $totalTmp >= 0) {
        $detalleValido = true;
        break;
    }
}

if (!$detalleValido) {
    responder(
        "Error",
        "Lo sentimos, debe agregar por lo menos una línea válida en los detalles de la factura.",
        "error",
        "btn-danger"
    );
}

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

$tablas_bloqueadas = false;

try {

    /*
        Este PHP solo GUARDA:
        - No genera número fiscal.
        - No consume secuencia.
        - No cobra.
        - No descuenta inventario.
    */
    $mysqli->query("
        LOCK TABLES
            facturas WRITE,
            facturas_detalle WRITE,
            secuencia_facturacion READ,
            productos READ,
            isv READ
    ");

    $tablas_bloqueadas = true;

    /* ============================================================
       VALIDAR QUE LA FACTURA EXISTA
    ============================================================ */

    $facturaActual = consultar_uno(
        $mysqli,
        "SELECT facturas_id, tipo_factura, estado, number
        FROM facturas
        WHERE facturas_id = ?
        LIMIT 1",
        "i",
        array($facturas_id)
    );

    if (!$facturaActual) {
        throw new Exception("La factura indicada no existe.");
    }

    /* ============================================================
       CONSULTAR SECUENCIA ACTIVA COMO REFERENCIA
    ============================================================ */

    $secuencia_facturacion_id = 0;

    $secuencia = consultar_uno(
        $mysqli,
        "SELECT 
            secuencia_facturacion_id,
            prefijo,
            siguiente AS numero,
            rango_final,
            fecha_limite,
            incremento,
            relleno
        FROM secuencia_facturacion
        WHERE activo = ? AND empresa_id = ?
        LIMIT 1",
        "ii",
        array($activo, $empresa_id)
    );

    if ($secuencia) {
        $secuencia_facturacion_id = (int)$secuencia['secuencia_facturacion_id'];
    }

    $numero = 0;
    $estado = 1; // Borrador

    /* ============================================================
       ACTUALIZAR CABECERA
    ============================================================ */

    ejecutar_sql(
        $mysqli,
        "UPDATE facturas
        SET
            number = ?,
            secuencia_facturacion_id = ?,
            tipo_factura = ?,
            pacientes_id = ?,
            colaborador_id = ?,
            servicio_id = ?,
            estado = ?,
            notas = ?,
            fecha = ?,
            cierre = ?,
            fact_empresas_id = ?,
            usuario = ?,
            empresa_id = ?,
            aseguradora_id = ?,
            proforma_id = ?
        WHERE facturas_id = ?",
        "iiiiiiissiiiiiii",
        array(
            $numero,
            $secuencia_facturacion_id,
            $tipo_factura,
            $pacientes_id,
            $colaborador_id,
            $servicio_id,
            $estado,
            $notes,
            $fecha,
            $cierre,
            $fact_empresas_id,
            $usuario,
            $empresa_id,
            $aseguradora_id,
            $proforma_id,
            $facturas_id
        )
    );

    /* ============================================================
       LIMPIAR DETALLE ACTUAL
    ============================================================ */

    ejecutar_sql(
        $mysqli,
        "DELETE FROM facturas_detalle
        WHERE facturas_id = ?",
        "i",
        array($facturas_id)
    );

    /* ============================================================
       OBTENER PORCENTAJE ISV
    ============================================================ */

    $porcentajeISV = 0;

    $rowISV = consultar_uno(
        $mysqli,
        "SELECT nombre FROM isv LIMIT 1"
    );

    if ($rowISV) {
        $porcentajeISV = (float)$rowISV['nombre'];
    }

    /* ============================================================
       INSERTAR DETALLE NUEVO
    ============================================================ */

    $total_valor = 0;
    $descuentos = 0;
    $isv_neto = 0;

    for ($i = 0; $i < count($productNameArray); $i++) {

        $productoID = post_int_array($productoIDArray, $i);
        $productName = post_string_array($productNameArray, $i);
        $quantity = post_float_array($quantityArray, $i);
        $price = post_float_array($priceArray, $i);
        $discount = post_float_array($discountArray, $i);
        $total = post_float_array($totalArray, $i);

        if ($productoID <= 0 || $productName === "" || $quantity <= 0 || $price < 0 || $total < 0) {
            continue;
        }

        /* ========================================================
           CONSULTAR SI EL PRODUCTO APLICA ISV
        ======================================================== */

        $aplica_isv = 0;

        $rowProductoISV = consultar_uno(
            $mysqli,
            "SELECT isv
            FROM productos
            WHERE productos_id = ?
            LIMIT 1",
            "i",
            array($productoID)
        );

        if ($rowProductoISV) {
            $aplica_isv = (int)$rowProductoISV['isv'];
        }

        $isv_valor = 0;

        if ($aplica_isv == 1) {
            $isv_valor = ($price * $quantity) * ($porcentajeISV / 100);
        }

        /* ========================================================
           INSERTAR DETALLE
        ======================================================== */

        $facturas_detalle_id = correlativo_local($mysqli, "facturas_detalle_id", "facturas_detalle");

        ejecutar_sql(
            $mysqli,
            "INSERT INTO facturas_detalle (
                facturas_detalle_id,
                facturas_id,
                productos_id,
                cantidad,
                precio,
                isv_valor,
                descuento
            ) VALUES (?, ?, ?, ?, ?, ?, ?)",
            "iiidddd",
            array(
                $facturas_detalle_id,
                $facturas_id,
                $productoID,
                $quantity,
                $price,
                $isv_valor,
                $discount
            )
        );

        $total_valor += ($price * $quantity);
        $descuentos += $discount;
        $isv_neto += $isv_valor;
    }

    $total_despues_isv = ($total_valor + $isv_neto) - $descuentos;

    /* ============================================================
       ACTUALIZAR IMPORTE
    ============================================================ */

    ejecutar_sql(
        $mysqli,
        "UPDATE facturas
        SET importe = ?
        WHERE facturas_id = ?",
        "di",
        array($total_despues_isv, $facturas_id)
    );

    $mysqli->query("UNLOCK TABLES");
    $tablas_bloqueadas = false;

    /* ============================================================
       RESPUESTA
    ============================================================ */

    $mensaje = "Registro almacenado correctamente.";

    if ($tipo_factura == 1) {
        $mensaje = "Factura de contado guardada correctamente.";
    }

    if ($tipo_factura == 2) {
        $mensaje = "Factura al crédito guardada correctamente.";
    }

    if ($tipo_factura == 3) {
        $mensaje = "Proforma guardada correctamente. No se generó pago ni secuencia fiscal.";
    }

    responder(
        "Almacenado",
        $mensaje,
        "success",
        "btn-primary",
        "formulario_facturacion",
        "Registro",
        $tipo,
        "",
        $facturas_id,
        $tipo_factura
    );

} catch (Exception $e) {

    if ($tablas_bloqueadas) {
        $mysqli->query("UNLOCK TABLES");
    }

    responder(
        "Error",
        "No se pudo almacenar este registro. " . $e->getMessage(),
        "error",
        "btn-danger"
    );
}