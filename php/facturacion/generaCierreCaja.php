<?php
session_start();   
include "../funtions.php";

header("Content-Type: text/html;charset=utf-8");

include_once "../../dompdf/autoload.inc.php";
require_once '../../pdf/vendor/autoload.php';

use Dompdf\Dompdf;
	 	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha = $_GET['fecha'];
$anulada = '';

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

if($result->num_rows>0){
	$consulta_registro = $result->fetch_assoc();	

	ob_start();
	include(dirname('__FILE__').'/cierreCaja.php');
	$html = ob_get_clean();

	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	
	$dompdf->set_option('isRemoteEnabled', true);

	$dompdf->loadHtml(utf8_decode(utf8_encode($html)));
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('B7', 'portrait');
	// Render the HTML as PDF
	$dompdf->render();
	
	// Output the generated PDF to Browser
	$dompdf->stream('Reporte Cierre de Caja.pdf',array('Attachment'=>0));
	
	exit;	
}
?>