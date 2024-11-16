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

if($estado == 1){
   $in = "IN(2,4)";
} else if($estado == 4){
   $in = "IN(4)";
} else {
   $in = "IN(3)";
}

$busqueda_paciente = "";
$profesional_consulta = "";

if($clientes != ""){
   $busqueda_paciente = "AND f.pacientes_id = '$clientes'";
}

if($profesional != ""){
   $profesional_consulta = "AND f.colaborador_id = '$profesional'";
}

$consulta = "SELECT f.facturas_id AS 'facturas_id', f.fecha AS 'fecha', p.identidad AS 'identidad', 
                   CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', 
                   f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', 
                   sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', 
                   f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', 
                   (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento', 
                   f.tipo_factura 
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

while ($data = $result->fetch_assoc()) {
   $facturas_id = $data['facturas_id'];

   $numero = $data['numero'] == 0 ? "Aún no se ha generado" : $data['prefijo'] . rellenarDigitos($data['numero'], $data['relleno']);
   $data['factura'] = $numero;

   // Consultar detalle de facturación
   $query_detalle = "SELECT cantidad, precio, descuento, isv_valor FROM facturas_detalle WHERE facturas_id = '$facturas_id'";
   $result_detalles = $mysqli->query($query_detalle) or die($mysqli->error);

   $cantidad = $descuento = $precio = $total_precio = $neto_antes_isv = $isv_neto = $total = 0;

   while ($registrodetalles = $result_detalles->fetch_assoc()) {
      $precio += $registrodetalles["precio"];
      $cantidad += $registrodetalles["cantidad"];
      $descuento += $registrodetalles["descuento"];
      $total_precio = $registrodetalles["precio"] * $registrodetalles["cantidad"];
      $neto_antes_isv += $total_precio;
      $isv_neto += $registrodetalles["isv_valor"];
   }

   $total = ($neto_antes_isv + $isv_neto) - $descuento;

   $data['precio'] = $precio;
   $data['cantidad'] = $cantidad;
   $data['descuento'] = $descuento;
   $data['total_precio'] = $total_precio;
   $data['neto_antes_isv'] = $neto_antes_isv;
   $data['isv_neto'] = $isv_neto;
   $data['total'] = $total;

   $estado_ = match ($estado) {
      1 => "Borrador",
      2 => "Pagada",
      3 => "Cancelada",
      4 => "Crédito",
      default => ""
   };

   $data['estado'] = $estado_;

   $arreglo['data'][] = $data;
}

echo json_encode($arreglo);

$result->free();
$mysqli->close();

