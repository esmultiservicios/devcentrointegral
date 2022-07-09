<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT f.facturas_id AS facturas_id, DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', p.pacientes_id AS 'pacientes_id', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', f.colaborador_id AS 'colaborador_id', f.estado AS 'estado', s.nombre AS 'consultorio', f.servicio_id AS 'servicio_id', f.fecha AS 'fecha_factura', f.notas AS 'notas'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	WHERE facturas_id = '$facturas_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$paciente = "";
$fecha_factura = "";
$importe = 0;

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$paciente = $consulta_registro['paciente'];
	$fecha_factura = $consulta_registro['fecha_factura'];	
}

$query_factura_detalles = "SELECT fd.productos_id AS 'productos_id', p.nombre AS 'producto', fd.cantidad AS 'cantidad', fd.precio AS 'precio', fd.isv_valor AS 'isv_valor', fd.descuento AS 'descuento'
	FROM facturas_detalle AS fd
	INNER JOIN facturas As f
	ON fd.facturas_id = f.facturas_id
	INNER JOIN productos AS p
	ON fd.productos_id = p.productos_id
WHERE fd.facturas_id = '$facturas_id'";
$result_factura = $mysqli->query($query_factura_detalles);

while($registro2 = $result_factura->fetch_assoc()){
	$cantidad = $registro2['cantidad'];
	$precio = $registro2['precio'];
	$descuento = $registro2['descuento'];
	$isv_valor = $registro2['isv_valor'];
	$importe += (($cantidad * $precio) - $descuento) + $isv_valor;
}	

$datos = array(
	 0 => $paciente, 
	 1 => $fecha_factura, 
	 2 => $importe,	 
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>