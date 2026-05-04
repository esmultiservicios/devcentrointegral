<?php
// addFacturaporUsuario1.php
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
        "facturas_detalle" => "facturas_detalle_id",
        "movimientos" => "movimientos_id"
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
$fecha_registro = date("Y-m-d H:i:s");

$notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";

$activo = 1;
$cierre = 2;

$aseguradora_id = post_int("aseguradora_id");
$fact_empresas_id = post_int("fact_empresas_id");

/*
    tipo_factura:
    1 = Contado
    2 = Crédito
    3 = Proforma
*/
$tipo_factura = post_int("facturas_activo", 2);

if (!in_array($tipo_factura, array(1, 2, 3))) {
    $tipo_factura = 2;
}

$proforma_id = null;

if (isset($_POST['proforma_id']) && $_POST['proforma_id'] !== "") {
    $proforma_id = (int)$_POST['proforma_id'];
}

/* ============================================================
   RESPUESTA PARA DATATABLE / JS
============================================================ */

$tipo = "Facturacion";

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
        "No se recibió el ID de la factura que desea procesar.",
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
        Este PHP FINALIZA/GENERA factura real:
        - Contado y Crédito generan número fiscal.
        - Contado y Crédito descuentan inventario.
        - Proforma no genera número fiscal ni descuenta inventario.
    */
    $mysqli->query("
        LOCK TABLES
            facturas WRITE,
            facturas_detalle WRITE,
            secuencia_facturacion WRITE,
            productos WRITE,
            movimientos WRITE,
            categoria_producto READ,
            isv READ
    ");

    $tablas_bloqueadas = true;

    /* ============================================================
       VALIDAR FACTURA ACTUAL
    ============================================================ */

    $facturaActual = consultar_uno(
        $mysqli,
        "SELECT 
            facturas_id,
            tipo_factura,
            estado,
            number,
            importe
        FROM facturas
        WHERE facturas_id = ?
        LIMIT 1",
        "i",
        array($facturas_id)
    );

    if (!$facturaActual) {
        throw new Exception("La factura indicada no existe.");
    }

    $numero_actual = isset($facturaActual['number']) ? (int)$facturaActual['number'] : 0;

    if ($numero_actual > 0 && $tipo_factura != 3) {
        throw new Exception("Esta factura ya fue generada fiscalmente. No se puede volver a procesar para evitar duplicar secuencia o inventario.");
    }

    /* ============================================================
       CONSULTAR SECUENCIA
    ============================================================ */

    $secuencia_facturacion_id = 0;
    $prefijo = "";
    $numero = 0;
    $rango_final = 0;
    $fecha_limite = "";
    $incremento = 1;
    $relleno = 8;
    $no_factura = "";

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

    if ($tipo_factura != 3) {
        if (!$secuencia) {
            throw new Exception("No existe una secuencia de facturación activa para esta empresa.");
        }

        $secuencia_facturacion_id = (int)$secuencia['secuencia_facturacion_id'];
        $prefijo = $secuencia['prefijo'];
        $numero = (int)$secuencia['numero'];
        $rango_final = (int)$secuencia['rango_final'];
        $fecha_limite = $secuencia['fecha_limite'];
        $incremento = (int)$secuencia['incremento'];
        $relleno = (int)$secuencia['relleno'];

        if ($incremento <= 0) {
            $incremento = 1;
        }

        if ($relleno <= 0) {
            $relleno = 8;
        }

        $no_factura = $prefijo . str_pad($numero, $relleno, "0", STR_PAD_LEFT);

        if ($rango_final > 0 && $numero > $rango_final) {
            throw new Exception("La secuencia de facturación llegó al rango final permitido.");
        }

        if ($fecha_limite !== "" && $fecha_limite < date("Y-m-d")) {
            throw new Exception("La fecha límite de la secuencia de facturación ya venció.");
        }
    }

    /* ============================================================
       ESTADO SEGÚN TIPO
    ============================================================ */

    $estado = 1;

    if ($tipo_factura == 2) {
        $estado = 4; // Crédito
    }

    if ($tipo_factura == 3) {
        $estado = 1;
        $numero = 0;
        $secuencia_facturacion_id = 0;
    }

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
       CONSUMIR SECUENCIA SOLO SI ES FACTURA REAL
    ============================================================ */

    if ($tipo_factura != 3) {
        $numero_siguiente = $numero + $incremento;

        ejecutar_sql(
            $mysqli,
            "UPDATE secuencia_facturacion
            SET siguiente = ?
            WHERE secuencia_facturacion_id = ?",
            "ii",
            array($numero_siguiente, $secuencia_facturacion_id)
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
       INSERTAR DETALLE / INVENTARIO
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
           CONSULTAR SI PRODUCTO APLICA ISV
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

        /* ========================================================
           INVENTARIO
           Solo factura real descuenta inventario.
           Proforma NO descuenta inventario.
        ======================================================== */

        if ($tipo_factura != 3) {

            $rowCategoria = consultar_uno(
                $mysqli,
                "SELECT categoria_producto.nombre AS categoria
                FROM productos
                INNER JOIN categoria_producto
                    ON productos.categoria_producto_id = categoria_producto.categoria_producto_id
                WHERE productos.productos_id = ?
                GROUP BY productos.productos_id
                LIMIT 1",
                "i",
                array($productoID)
            );

            $categoria_producto = "";

            if ($rowCategoria) {
                $categoria_producto = $rowCategoria['categoria'];
            }

            if ($categoria_producto == "Producto") {

                $rowProductoCantidad = consultar_uno(
                    $mysqli,
                    "SELECT cantidad
                    FROM productos
                    WHERE productos_id = ?
                    LIMIT 1",
                    "i",
                    array($productoID)
                );

                $cantidad_productos = 0;

                if ($rowProductoCantidad) {
                    $cantidad_productos = (float)$rowProductoCantidad['cantidad'];
                }

                $nueva_cantidad = $cantidad_productos - $quantity;

                ejecutar_sql(
                    $mysqli,
                    "UPDATE productos
                    SET cantidad = ?
                    WHERE productos_id = ?",
                    "di",
                    array($nueva_cantidad, $productoID)
                );

                $rowMovimiento = consultar_uno(
                    $mysqli,
                    "SELECT saldo
                    FROM movimientos
                    WHERE productos_id = ?
                    ORDER BY movimientos_id DESC
                    LIMIT 1",
                    "i",
                    array($productoID)
                );

                $saldo_productos = 0;

                if ($rowMovimiento) {
                    $saldo_productos = (float)$rowMovimiento['saldo'];
                }

                $saldo = $saldo_productos - $quantity;

                $cantidad_entrada = 0;
                $cantidad_salida = $quantity;

                if ($no_factura !== "") {
                    $documento = "Factura " . $no_factura;
                } else {
                    $documento = "Factura " . $facturas_id;
                }

                $movimientos_id = correlativo_local($mysqli, "movimientos_id", "movimientos");
                $comentario_movimientos = "Salida por Facturación";

                ejecutar_sql(
                    $mysqli,
                    "INSERT INTO movimientos
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    "iisdddss",
                    array(
                        $movimientos_id,
                        $productoID,
                        $documento,
                        $cantidad_entrada,
                        $cantidad_salida,
                        $saldo,
                        $fecha_registro,
                        $comentario_movimientos
                    )
                );
            }
        }

        $total_valor += ($price * $quantity);
        $descuentos += $discount;
        $isv_neto += $isv_valor;
    }

    $total_despues_isv = ($total_valor + $isv_neto) - $descuentos;

    /* ============================================================
       ACTUALIZAR IMPORTE FINAL
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
        $mensaje = "Factura de contado generada correctamente.";
    }

    if ($tipo_factura == 2) {
        $mensaje = "Factura al crédito generada correctamente.";
    }

    if ($tipo_factura == 3) {
        $mensaje = "Proforma guardada correctamente. No se generó número fiscal, pago ni movimiento de inventario.";
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