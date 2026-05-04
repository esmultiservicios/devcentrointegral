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

function post_string($key, $default = "") {
    if (!isset($_POST[$key])) {
        return $default;
    }

    return trim($_POST[$key]);
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

function obtener_tipo_factura() {
    /*
       En tu formulario normalmente viene como:
       name="facturas_activo"

       Pero dejo varias opciones por seguridad, por si en otro formulario
       lo mandás como tipo_factura, factura_tipo o tipo.
    */

    $valor = "";

    if (isset($_POST['facturas_activo'])) {
        $valor = $_POST['facturas_activo'];
    } elseif (isset($_POST['tipo_factura'])) {
        $valor = $_POST['tipo_factura'];
    } elseif (isset($_POST['factura_tipo'])) {
        $valor = $_POST['factura_tipo'];
    } elseif (isset($_POST['tipo'])) {
        $valor = $_POST['tipo'];
    }

    $valor = trim((string)$valor);
    $valor_lower = strtolower($valor);

    if ($valor_lower === "contado") {
        return 1;
    }

    if ($valor_lower === "credito" || $valor_lower === "crédito") {
        return 2;
    }

    if ($valor_lower === "proforma") {
        return 3;
    }

    $tipo = (int)$valor;

    if (in_array($tipo, array(1, 2, 3))) {
        return $tipo;
    }

    /*
       Si por alguna razón no viene nada, lo dejamos como contado.
       Esto evita que vuelva a guardarse todo como proforma por defecto.
    */
    return 1;
}

function correlativo_local($mysqli, $campo, $tabla) {
    $permitidos = array(
        "facturas" => "facturas_id",
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

function texto_tipo_factura($tipo_factura) {
    $tipo_factura = (int)$tipo_factura;

    if ($tipo_factura === 1) {
        return "factura contado";
    }

    if ($tipo_factura === 2) {
        return "factura crédito";
    }

    if ($tipo_factura === 3) {
        return "proforma";
    }

    return "documento";
}

/* ============================================================
   VALIDAR SESIÓN
============================================================ */

if (!isset($_SESSION['colaborador_id']) || !isset($_SESSION['empresa_id'])) {
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
$fecha = post_string("fecha", date("Y-m-d"));
$colaborador_id = post_int("colaborador_id");
$servicio_id = post_int("servicio_id");

$notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";

$usuario = (int)$_SESSION['colaborador_id'];
$empresa_id = (int)$_SESSION['empresa_id'];
$fecha_registro = date("Y-m-d H:i:s");

$aseguradora_id = post_int("aseguradora_id");
$fact_empresas_id = post_int("fact_empresas_id");

/*
   AQUÍ ESTABA EL ERROR:
   Antes estaba fijo como 3.
   Ahora respeta lo que el usuario seleccionó en el formulario.
*/
$tipo_factura = obtener_tipo_factura();

/*
   Si es contado o crédito, usa documento_id = 1.
   Si es proforma, usa documento_id = 4.
*/
$documento_id = ($tipo_factura === 3) ? 4 : 1;

/*
   Documento temporal:
   - No genera número.
   - No consume secuencia.
   - No descuenta inventario.
*/
$estado = 1;
$cierre = 2;
$importe = 0;
$numero = 0;
$proforma_id = null;

/* ============================================================
   VALIDACIONES PRINCIPALES
============================================================ */

if ($pacientes_id <= 0 || $colaborador_id <= 0 || $servicio_id <= 0) {
    responder(
        "Error",
        "Lo sentimos, el Paciente, Profesional o Servicio no pueden quedar en blanco, por favor corregir.",
        "error",
        "btn-danger"
    );
}

if (!in_array($tipo_factura, array(1, 2, 3))) {
    responder(
        "Error",
        "Debe seleccionar un tipo de factura válido: Contado, Crédito o Proforma.",
        "error",
        "btn-danger"
    );
}

if (!isset($_POST['productName']) || !is_array($_POST['productName'])) {
    responder(
        "Error",
        "No se pudo almacenar este registro. El detalle no puede quedar vacío.",
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
        "No se pudo almacenar este registro. Verifique si hay registros en blanco antes de enviar los datos.",
        "error",
        "btn-danger"
    );
}

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

$tablas_bloqueadas = false;

try {

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
       CONSULTAR SECUENCIA ACTIVA SEGÚN TIPO DE DOCUMENTO

       documento_id = 1 => Factura Electrónica
       documento_id = 4 => Proforma

       Se guarda la relación, pero NO se consume el número.
    ============================================================ */

    $secuencia = consultar_uno(
        $mysqli,
        "SELECT 
            secuencia_facturacion_id,
            prefijo,
            siguiente,
            relleno,
            documento_id
        FROM secuencia_facturacion
        WHERE activo = ?
          AND empresa_id = ?
          AND documento_id = ?
        LIMIT 1",
        "iii",
        array(1, $empresa_id, $documento_id)
    );

    if (!$secuencia) {
        if ($documento_id === 1) {
            throw new Exception("No existe una secuencia activa para Factura Electrónica.");
        }

        throw new Exception("No existe una secuencia activa para Proforma.");
    }

    $secuencia_facturacion_id = (int)$secuencia['secuencia_facturacion_id'];

    /* ============================================================
       INSERTAR O ACTUALIZAR CABECERA
    ============================================================ */

    if ($facturas_id <= 0) {

        $facturas_id = correlativo_local($mysqli, "facturas_id", "facturas");

        ejecutar_sql(
            $mysqli,
            "INSERT INTO facturas (
                facturas_id,
                secuencia_facturacion_id,
                number,
                tipo_factura,
                pacientes_id,
                colaborador_id,
                servicio_id,
                importe,
                notas,
                fecha,
                estado,
                cierre,
                fact_empresas_id,
                usuario,
                empresa_id,
                fecha_registro,
                aseguradora_id,
                proforma_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            "iiiiiiidssiiiiisii",
            array(
                $facturas_id,
                $secuencia_facturacion_id,
                $numero,
                $tipo_factura,
                $pacientes_id,
                $colaborador_id,
                $servicio_id,
                $importe,
                $notes,
                $fecha,
                $estado,
                $cierre,
                $fact_empresas_id,
                $usuario,
                $empresa_id,
                $fecha_registro,
                $aseguradora_id,
                $proforma_id
            )
        );

    } else {

        $facturaActual = consultar_uno(
            $mysqli,
            "SELECT facturas_id, number, tipo_factura, estado
            FROM facturas
            WHERE facturas_id = ?
            LIMIT 1",
            "i",
            array($facturas_id)
        );

        if (!$facturaActual) {
            throw new Exception("El documento indicado no existe.");
        }

        if ((int)$facturaActual['number'] > 0) {
            throw new Exception("Este documento ya tiene número generado. No se puede modificar desde esta pantalla.");
        }

        ejecutar_sql(
            $mysqli,
            "UPDATE facturas
            SET
                secuencia_facturacion_id = ?,
                number = ?,
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
                $secuencia_facturacion_id,
                $numero,
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
    }

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
       INSERTAR DETALLE
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

    responder(
        "Almacenado",
        "Documento almacenado correctamente como " . texto_tipo_factura($tipo_factura) . ".",
        "success",
        "btn-primary",
        "formulario_facturacion",
        "Registro",
        "Facturacion1",
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
        "No se pudo almacenar el documento. " . $e->getMessage(),
        "error",
        "btn-danger"
    );
}

$mysqli->close();