<?php
session_start();
include '../funtions.php';

// CONEXIÓN A DB
$mysqli = connect_mysqli();

$colaborador_id = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$clientes = $_POST['clientes'];
$profesional = $_POST['profesional'];
$estado = $_POST['estado'];
$usuario = $_SESSION['colaborador_id'];

if ($estado == 1) {
    $in = 'IN(2,4)';
} else if ($estado == 4) {
    $in = 'IN(4)';
} else {
    $in = 'IN(3)';
}

$busqueda_paciente = '';
$profesional_consulta = '';

if ($clientes != '') {
    $busqueda_paciente = "AND f.pacientes_id = '$clientes'";
}

if ($profesional != '') {
    $profesional_consulta = "AND f.colaborador_id = '$profesional'";
}

$consulta = "
SELECT 
    f.facturas_id AS 'facturas_id', 
    f.fecha AS 'fecha', 
    p.identidad AS 'identidad', 
    CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', 
    sc.prefijo AS 'prefijo', 
    f.number AS 'numero', 
    s.nombre AS 'servicio', 
    CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', 
    sc.relleno AS 'relleno', 
    DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', 
    f.pacientes_id AS 'pacientes_id', 
    f.cierre AS 'cierre', 
    (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento', 
    f.tipo_factura,
    -- Detalles de facturación (subconsulta)
    (
        SELECT SUM(fd.precio * fd.cantidad) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total_precio',
    (
        SELECT SUM(fd.cantidad) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'cantidad',
    (
        SELECT SUM(fd.descuento) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'descuento',
    (
        SELECT SUM(fd.isv_valor) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'isv_neto',
    (
        SELECT SUM(fd.precio * fd.cantidad) + SUM(fd.isv_valor) - SUM(fd.descuento)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total',
    (
        SELECT SUM(fd.precio)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'precio'  -- Aquí agregamos 'precio'
FROM facturas AS f
INNER JOIN pacientes AS p ON f.pacientes_id = p.pacientes_id
INNER JOIN secuencia_facturacion AS sc ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
INNER JOIN servicios AS s ON f.servicio_id = s.servicio_id
INNER JOIN colaboradores AS c ON f.colaborador_id = c.colaborador_id
WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado $in
$busqueda_paciente
$profesional_consulta
ORDER BY f.number DESC";

$result = $mysqli->query($consulta) or die($mysqli->error);

$arreglo = array();
$arreglo['data'] = array();

while ($data = $result->fetch_assoc()) {
    $facturas_id = $data['facturas_id'];

    $numero = $data['numero'] == 0 ? 'Aún no se ha generado' : $data['prefijo'] . rellenarDigitos($data['numero'], $data['relleno']);
    $data['factura'] = $numero;

    $estado_ = match ($estado) {
        1 => 'Borrador',
        2 => 'Pagada',
        3 => 'Cancelada',
        4 => 'Crédito',
        default => ''
    };

    $data['estado'] = $estado_;

    $arreglo['data'][] = $data;
}

// Consulta para obtener el total por tipo de pago
$consulta_pagos = "
SELECT 
    tp.nombre AS tipo_pago,
    SUM(p.importe) AS total_pago
FROM pagos AS p
JOIN tipo_pago AS tp ON p.tipo_pago = tp.tipo_pago_id
WHERE p.facturas_id IN (SELECT facturas_id FROM facturas WHERE fecha BETWEEN '$fechai' AND '$fechaf' AND estado = 2) 
GROUP BY tp.nombre";

$resultados_pagos = $mysqli->query($consulta_pagos);
$tipos_de_pago = [];
while ($row = $resultados_pagos->fetch_assoc()) {
    $tipos_de_pago[$row['tipo_pago']] = $row['total_pago'];
}

// Devolver tanto las facturas como los totales por tipo de pago
echo json_encode([
    'data' => $arreglo['data'],
    'tipos_de_pago' => $tipos_de_pago  // Aquí estamos enviando el total por tipo de pago
]);

$result->free();
$resultados_pagos->free();
$mysqli->close();
