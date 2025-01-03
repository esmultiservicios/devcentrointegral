<?php
session_start();
include "../funtions.php";  // Incluye funciones y conexión

header("Content-Type: text/html;charset=utf-8");

require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

//CONEXION A DB
$mysqli = connect_mysqli();  // Usas la conexión definida en funtions.php
$db_main = DB_MAIN;

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$fecha = $_GET['fecha'];
$anulada = '';

$mysqli->set_charset('utf8');

$query = "SELECT e.nombre AS 'empresa', CONCAT(c.nombre, ' ', c.apellido) AS 'usuario'
	FROM facturas AS f
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	INNER JOIN empresa AS e
	ON sf.empresa_id = e.empresa_id
    INNER JOIN pagos AS p
    ON f.facturas_id = p.facturas_id
	INNER JOIN colaboradores AS c
	ON p.usuario = c.colaborador_id  
	GROUP BY e.empresa_id";	
$result = $mysqli->query($query) or die($mysqli->error);

//OBTENER DETALLE DE FACTURA
$query_factura_detalle = "SELECT CONCAT(sf.prefijo, '', LPAD(f.number,sf.relleno,'0')) AS 'factura', f.importe AS 'importe'
	FROM facturas AS f
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = f.secuencia_facturacion_id
	WHERE f.estado = 2 AND f.fecha = '$fecha'
	GROUP BY f.facturas_id";
$result_factura_detalle = $mysqli->query($query_factura_detalle) or die($mysqli->error);	

if($result->num_rows > 0) {
	$consulta_registro = $result->fetch_assoc();	

	// Obtener fecha y hora actual para el nombre del archivo
	$fecha_hora = date("Y-m-d_H-i-s");

	// Incluir la plantilla HTML
	ob_start();
	include 'cierreCaja.php';
	$html = ob_get_clean();

	// Generar el PDF
	$dompdf->loadHtml($html);
	$dompdf->setPaper('B7', 'portrait');
	$dompdf->render();

	// Descargar o mostrar el PDF con la fecha y hora en el nombre
	$dompdf->stream("Reporte_Cierre_de_Caja_{$fecha_hora}.pdf", ["Attachment" => false]);
}