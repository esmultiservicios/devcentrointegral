<?php
if (!isset($_SESSION)) {
	session_start();
}

include '../funtions.php';

header('Content-Type: text/html;charset=utf-8');

require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;

// CONEXION A DB
$mysqli = connect_mysqli();

$noFactura = $_GET['facturas_id'];
$anulada = '';

$query = "SELECT CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', p.identidad AS 'identidad', p.expediente AS 'expediente', p.telefono1 AS 'tel_paciente', p.localidad AS 'localidad_paciente', e.nombre AS 'empresa', e.ubicacion AS 'direccion_empresa', e.telefono AS 'empresa_telefono', e.correo AS 'empresa_correo', CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', s.nombre AS 'servicio', sf.prefijo AS 'prefijo', sf.siguiente AS 'numero', sf.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', time(f.fecha_registro) AS 'hora', sf.cai AS 'cai', e.rtn AS 'rtn', sf.fecha_activacion AS 'fecha_activacion', sf.fecha_limite AS 'fecha_limite', pc.nombre AS 'puesto', f.estado AS 'estado', sf.rango_inicial AS 'rango_inicial', sf.rango_final AS 'rango_final', f.number AS 'numero_factura', f.notas AS 'notas', e.otra_informacion As 'otra_informacion', e.eslogan AS 'eslogan', e.celular As 'celular', p.fecha_nacimiento AS 'fecha_nacimiento', f.colaborador_id AS 'colaborador_id'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	INNER JOIN empresa AS e
	ON c.empresa_id = e.empresa_id    
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
	ON c.puesto_id = pc.puesto_id\t
	WHERE f.facturas_id = '$noFactura'";
$result = $mysqli->query($query) or die($mysqli->error);

// OBTENER DETALLE DE FACTURA
$query_factura_detalle = "SELECT p.nombre AS 'producto', fd.cantidad As 'cantidad', fd.precio AS 'precio', fd.descuento AS 'descuento', fd.productos_id  AS 'productos_id', fd.isv_valor AS 'isv_valor'
FROM facturas_detalle AS fd
INNER JOIN productos AS p
ON fd.productos_id = p.productos_id
WHERE facturas_id = '$noFactura'";
$result_factura_detalle = $mysqli->query($query_factura_detalle) or die($mysqli->error);

if ($result->num_rows > 0) {
	$consulta_registro = $result->fetch_assoc();
	/*********************************************************************************/
	// CONSULTA AÑO, MES y DIA DEL PACIENTE
	$valores_array = getEdad($consulta_registro['fecha_nacimiento']);
	$edad = $valores_array['anos'];
	$meses = $valores_array['meses'];
	$dias = $valores_array['dias'];
	/*********************************************************************************/

	$no_factura_ = $consulta_registro['colaborador_id'] . '_' . str_pad($consulta_registro['numero_factura'], $consulta_registro['relleno'], '0', STR_PAD_LEFT);
	$no_factura = str_pad($consulta_registro['numero_factura'], $consulta_registro['relleno'], '0', STR_PAD_LEFT);

	if ($consulta_registro['estado'] == 3) {
		$anulada = '<img class="anulada" src="' . SERVERURL . 'img/anulado.png" alt="Anulada">';
	}

	ob_start();
	include (dirname('__FILE__') . '/factura.php');
	$html = ob_get_clean();

	// instantiate and use the dompdf class
	$dompdf = new Dompdf();

	$dompdf->set_option('isRemoteEnabled', true);

	$dompdf->loadHtml(utf8_decode(utf8_encode($html)));
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('letter', 'portrait');
	// Render the HTML as PDF
	$dompdf->render();

	file_put_contents(dirname('__FILE__') . '/Facturas/factura_' . $no_factura_ . '.pdf', $dompdf->output());

	// Output the generated PDF to Browser
	$dompdf->stream('factura_' . $no_factura . '.pdf', array('Attachment' => 0));

	exit;
}
?>