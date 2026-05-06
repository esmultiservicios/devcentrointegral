<?php
//addFactura.php
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
        "facturas" => "facturas_id",
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

    $affected_rows = $stmt->affected_rows;
    $stmt->close();

    return $affected_rows;
}

function numero_documento_existe($mysqli, $empresa_id, $secuencia_facturacion_id, $numero, $facturas_id_actual = 0) {
    if ($facturas_id_actual > 0) {
        return consultar_uno(
            $mysqli,
            "SELECT facturas_id
            FROM facturas
            WHERE empresa_id = ?
              AND secuencia_facturacion_id = ?
              AND number = ?
              AND facturas_id <> ?
            LIMIT 1",
            "iiii",
            array($empresa_id, $secuencia_facturacion_id, $numero, $facturas_id_actual)
        );
    }

    return consultar_uno(
        $mysqli,
        "SELECT facturas_id
        FROM facturas
        WHERE empresa_id = ?
          AND secuencia_facturacion_id = ?
          AND number = ?
        LIMIT 1",
        "iii",
        array($empresa_id, $secuencia_facturacion_id, $numero)
    );
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

$pacientes_id = post_int("pacientes_id");
$facturas_id = post_int("facturas_id");
$facturas_id_original = $facturas_id;

$colaborador_id = post_int("colaborador_id");
$servicio_id = post_int("servicio_id");

$fecha = date("Y-m-d");
$notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";

$usuario = (int)$_SESSION['colaborador_id'];
$empresa_id = (int)$_SESSION['empresa_id'];
$fecha_registro = date("Y-m-d H:i:s");

$activo = 1;
$cierre = 2;
$importe = 0;

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

/*
   documento_id:
   1 = Factura Electrónica: contado y crédito usan esta misma secuencia.
   4 = Proforma: usa su propia secuencia.
*/
$documento_id = 1;

if ($tipo_factura == 3) {
    $documento_id = 4;
}

$proforma_id = null;

if (isset($_POST['proforma_id']) && $_POST['proforma_id'] !== "") {
    $proforma_id = (int)$_POST['proforma_id'];
}

/* ============================================================
   TIPO DE RESPUESTA PARA TU JS
============================================================ */

$tipo = "Facturacion";

if ($tipo_factura == 2) {
    $tipo = "FacturacionCredito";
}

if ($tipo_factura == 3) {
    $tipo = "Facturacion1";
}

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

if (!isset($_POST['productName']) || !is_array($_POST['productName'])) {
    responder(
        "Error",
        "No se pudo almacenar este registro. El detalle de la factura no puede quedar vacío.",
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
        "No se pudo almacenar este registro. Verifique si hay registros en blanco antes de enviar los datos de la factura.",
        "error",
        "btn-danger"
    );
}

/* ============================================================
   PROCESO PRINCIPAL
============================================================ */

$tablas_bloqueadas = false;
$factura_insertada_nueva = false;
$documento_movimiento = "";
$inventario_revertir = array();

try {

    /*
       Se conserva tu lógica actual con LOCK TABLES.
       Esto protege la secuencia aunque haya varios usuarios facturando.
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

    if ($mysqli->error) {
        throw new Exception("No se pudieron bloquear las tablas: " . $mysqli->error);
    }

    $tablas_bloqueadas = true;

    /* ============================================================
       CONSULTAR SECUENCIA SEGÚN DOCUMENTO
    ============================================================ */

    $secuencia = consultar_uno(
        $mysqli,
        "SELECT 
            secuencia_facturacion_id,
            prefijo,
            siguiente AS numero,
            rango_final,
            fecha_limite,
            incremento,
            relleno,
            documento_id
        FROM secuencia_facturacion
        WHERE activo = ?
          AND empresa_id = ?
          AND documento_id = ?
        LIMIT 1",
        "iii",
        array($activo, $empresa_id, $documento_id)
    );

    if (!$secuencia) {
        if ($tipo_factura == 3) {
            throw new Exception("No existe una secuencia activa para Proforma.");
        }

        throw new Exception("No existe una secuencia activa para Factura Electrónica.");
    }

    $secuencia_facturacion_id = (int)$secuencia['secuencia_facturacion_id'];
    $numero_secuencia = (int)$secuencia['numero'];
    $rango_final = (int)$secuencia['rango_final'];
    $fecha_limite = $secuencia['fecha_limite'];

    /*
       IMPORTANTE:
       Contado y crédito usan documento_id = 1.
       Proforma usa documento_id = 4.
       No se busca otro número.
       Se usa exactamente el número que dice secuencia_facturacion.siguiente.
    */
    $incremento = 1;

    if ($numero_secuencia <= 0) {
        throw new Exception("La secuencia activa tiene un número inválido.");
    }

    if ($rango_final > 0 && $numero_secuencia > $rango_final) {
        throw new Exception("La secuencia llegó al rango final permitido.");
    }

    if ($fecha_limite !== "" && $fecha_limite < date("Y-m-d")) {
        throw new Exception("La fecha límite de la secuencia ya venció.");
    }

    /* ============================================================
       VALIDAR FACTURA EXISTENTE
    ============================================================ */

    if ($facturas_id > 0) {
        $facturaActual = consultar_uno(
            $mysqli,
            "SELECT 
                facturas_id,
                tipo_factura,
                estado,
                number,
                secuencia_facturacion_id
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
            throw new Exception("Este documento ya tiene número asignado. No se puede volver a procesar.");
        }
    }

    /* ============================================================
       VALIDAR QUE EL NÚMERO EXACTO NO EXISTA
       No se brinca al siguiente.
    ============================================================ */

    $numeroExistente = numero_documento_existe(
        $mysqli,
        $empresa_id,
        $secuencia_facturacion_id,
        $numero_secuencia,
        $facturas_id
    );

    if ($numeroExistente) {
        throw new Exception("El número fiscal " . $numero_secuencia . " ya existe. Revise la secuencia antes de continuar.");
    }

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
       PREVALIDAR PRODUCTOS ANTES DE CONSUMIR SECUENCIA
    ============================================================ */

    $detalleProcesar = array();

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

        $rowProducto = consultar_uno(
            $mysqli,
            "SELECT 
                productos.productos_id,
                productos.isv,
                productos.cantidad,
                categoria_producto.nombre AS categoria
            FROM productos
            INNER JOIN categoria_producto
                ON productos.categoria_producto_id = categoria_producto.categoria_producto_id
            WHERE productos.productos_id = ?
            LIMIT 1",
            "i",
            array($productoID)
        );

        if (!$rowProducto) {
            throw new Exception("No existe el producto con código " . $productoID . ".");
        }

        $aplica_isv = (int)$rowProducto['isv'];
        $cantidad_actual = (float)$rowProducto['cantidad'];
        $categoria_producto = $rowProducto['categoria'];

        $isv_valor = 0;

        if ($aplica_isv == 1) {
            $isv_valor = ($price * $quantity) * ($porcentajeISV / 100);
        }

        $detalleProcesar[] = array(
            "productos_id" => $productoID,
            "cantidad" => $quantity,
            "precio" => $price,
            "descuento" => $discount,
            "isv_valor" => $isv_valor,
            "categoria" => $categoria_producto,
            "cantidad_actual" => $cantidad_actual
        );
    }

    if (count($detalleProcesar) <= 0) {
        throw new Exception("No se encontró ningún detalle válido para procesar.");
    }

    /* ============================================================
       ESTADO FINAL AL GENERAR DOCUMENTO
    ============================================================ */

    $estado_factura = 2;

    if ($tipo_factura == 2) {
        $estado_factura = 4;
    }

    if ($tipo_factura == 3) {
        $estado_factura = 5;
    }

    /* ============================================================
       INSERTAR O ACTUALIZAR CABECERA
    ============================================================ */

    if ($facturas_id <= 0) {

        $facturas_id = correlativo_local($mysqli, "facturas_id", "facturas");
        $factura_insertada_nueva = true;

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
                $numero_secuencia,
                $tipo_factura,
                $pacientes_id,
                $colaborador_id,
                $servicio_id,
                $importe,
                $notes,
                $fecha,
                $estado_factura,
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
                $numero_secuencia,
                $tipo_factura,
                $pacientes_id,
                $colaborador_id,
                $servicio_id,
                $estado_factura,
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
       INSERTAR DETALLE E INVENTARIO
    ============================================================ */

    $total_valor = 0;
    $descuentos = 0;
    $isv_neto = 0;

    foreach ($detalleProcesar as $detalle) {

        $productoID = (int)$detalle['productos_id'];
        $quantity = (float)$detalle['cantidad'];
        $price = (float)$detalle['precio'];
        $discount = (float)$detalle['descuento'];
        $isv_valor = (float)$detalle['isv_valor'];
        $categoria_producto = $detalle['categoria'];

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

        /*
           Proforma NO descuenta inventario.
           Contado y crédito SÍ descuentan inventario.
        */
        if ($tipo_factura != 3 && $categoria_producto == "Producto") {

            $documento_movimiento = "Factura " . $facturas_id;

            $movimientoExistente = consultar_uno(
                $mysqli,
                "SELECT movimientos_id
                FROM movimientos
                WHERE productos_id = ? AND documento = ?
                LIMIT 1",
                "is",
                array($productoID, $documento_movimiento)
            );

            if (!$movimientoExistente) {

                $rowProductoCantidad = consultar_uno(
                    $mysqli,
                    "SELECT cantidad
                    FROM productos
                    WHERE productos_id = ?
                    LIMIT 1",
                    "i",
                    array($productoID)
                );

                if (!$rowProductoCantidad) {
                    throw new Exception("No se encontró inventario para el producto " . $productoID . ".");
                }

                $cantidad_productos = (float)$rowProductoCantidad['cantidad'];
                $nueva_cantidad = $cantidad_productos - $quantity;

                $inventario_revertir[] = array(
                    "productos_id" => $productoID,
                    "cantidad_anterior" => $cantidad_productos
                );

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
                        $documento_movimiento,
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

    /* ============================================================
       ACTUALIZAR IMPORTE FINAL
    ============================================================ */

    $total_despues_isv = ($total_valor + $isv_neto) - $descuentos;

    ejecutar_sql(
        $mysqli,
        "UPDATE facturas
        SET importe = ?
        WHERE facturas_id = ?",
        "di",
        array($total_despues_isv, $facturas_id)
    );

    /* ============================================================
       CONSUMIR SECUENCIA AL FINAL
       Solo aquí se avanza el correlativo.
       Si algo falló antes, no se consume.
    ============================================================ */

    $numero_siguiente = $numero_secuencia + 1;

    $filasSecuencia = ejecutar_sql(
        $mysqli,
        "UPDATE secuencia_facturacion
        SET siguiente = ?,
            incremento = 1
        WHERE secuencia_facturacion_id = ?
          AND siguiente = ?",
        "iii",
        array(
            $numero_siguiente,
            $secuencia_facturacion_id,
            $numero_secuencia
        )
    );

    if ($filasSecuencia <= 0) {
        throw new Exception("La secuencia fue modificada antes de finalizar. No se consumió el número.");
    }

    $mysqli->query("UNLOCK TABLES");
    $tablas_bloqueadas = false;

    /* ============================================================
       RESPUESTA AJAX
    ============================================================ */

    $mensaje = "Registro almacenado correctamente.";

    if ($tipo_factura == 1) {
        $mensaje = "Factura de contado generada correctamente.";
    }

    if ($tipo_factura == 2) {
        $mensaje = "Factura al crédito generada correctamente.";
    }

    if ($tipo_factura == 3) {
        $mensaje = "Proforma generada correctamente.";
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

    /*
       Limpieza manual básica porque no estamos cambiando tablas ni usando transacciones.
       Si falló una factura nueva antes de consumir secuencia, se elimina para no dejar
       documentos incompletos con número fiscal.
    */
    if ($tablas_bloqueadas) {

        if ($factura_insertada_nueva && $facturas_id > 0) {

            if ($documento_movimiento !== "") {
                $mysqli->query("DELETE FROM movimientos WHERE documento = '" . $mysqli->real_escape_string($documento_movimiento) . "'");
            } else {
                $mysqli->query("DELETE FROM movimientos WHERE documento = 'Factura " . (int)$facturas_id . "'");
            }

            for ($r = 0; $r < count($inventario_revertir); $r++) {
                $prod_id_revertir = (int)$inventario_revertir[$r]['productos_id'];
                $cantidad_anterior = (float)$inventario_revertir[$r]['cantidad_anterior'];

                $mysqli->query("
                    UPDATE productos
                    SET cantidad = " . $cantidad_anterior . "
                    WHERE productos_id = " . $prod_id_revertir . "
                ");
            }

            $mysqli->query("DELETE FROM facturas_detalle WHERE facturas_id = " . (int)$facturas_id);
            $mysqli->query("DELETE FROM facturas WHERE facturas_id = " . (int)$facturas_id);
        }

        $mysqli->query("UNLOCK TABLES");
    }

    responder(
        "Error",
        "No se pudo almacenar el registro. " . $e->getMessage(),
        "error",
        "btn-danger"
    );
}

$mysqli->close();