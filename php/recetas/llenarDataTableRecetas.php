<?php
session_start();
include '../funtions.php';

// CONEXIÓN A DB
$mysqli = connect_mysqli();

$fechai = isset($_POST['fechai']) ? $mysqli->real_escape_string($_POST['fechai']) : '';
$fechaf = isset($_POST['fechaf']) ? $mysqli->real_escape_string($_POST['fechaf']) : '';
$clientes = isset($_POST['clientes']) ? (int)$_POST['clientes'] : 0;
$profesional = isset($_POST['profesional']) ? (int)$_POST['profesional'] : 0;
$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

$busqueda_paciente = '';
$profesional_consulta = '';

if ($clientes > 0) {
    $busqueda_paciente = "AND r.pacientes_id = '$clientes'";
}

if ($profesional > 0) {
    $profesional_consulta = "AND r.colaborador_id = '$profesional'";
}

$consulta = "
SELECT CONCAT(IFNULL(p.nombre, ''), ' ', IFNULL(p.apellido, '')) AS paciente, 
        r.fecha, 
        CONCAT(IFNULL(d.nombre, ''), ' ', IFNULL(d.apellido, '')) AS doctor, 
        rd.productos_id, 
        rd.cantidad, 
        rd.descripcion, 
        prod.nombre AS producto_nombre, 
        e.rtn AS empresa_rtn, 
        e.nombre AS empresa, 
        e.ubicacion AS empresa_ubicacion,
        r.receta_id,
        p.identidad,
        p.pacientes_id,
        LPAD(r.receta_id, 6, '0') AS receta_numero
FROM recetas r
INNER JOIN pacientes p ON r.pacientes_id = p.pacientes_id
INNER JOIN colaboradores d ON r.colaborador_id = d.colaborador_id
INNER JOIN receta_detalles rd ON r.receta_id = rd.receta_id
INNER JOIN productos prod ON rd.productos_id = prod.productos_id
INNER JOIN empresa e ON r.empresa_id = e.empresa_id
WHERE CAST(r.fecha AS DATE) BETWEEN '$fechai' AND '$fechaf' AND r.estado = '$estado' $busqueda_paciente $profesional_consulta";

$result = $mysqli->query($consulta) or die($mysqli->error);

$arreglo = array('data' => []);

header('Content-Type: application/json');

if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {   
        $arreglo['data'][] = $data;
    }    
}

echo json_encode($arreglo);

$result->free();
$mysqli->close();