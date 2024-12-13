<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$profesional = $_GET['colaborador'];
$estado = $_GET['estado'];
$usuario = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];

if($estado == 1){
   $in = "IN(2,4)";
}else if($estado == 4){
	$in = "IN(4)";
 }else{
	$in = "IN(3)";
}

$mes=nombremes(date("m", strtotime($desde)));
$mes1=nombremes(date("m", strtotime($hasta)));
$año=date("Y", strtotime($desde));
$año2=date("Y", strtotime($hasta));
$type = $_SESSION['type'];

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado ".$in;	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado ".$in;
	}
}else{
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado ".$in." AND f.usuario = '$usuario'";	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado ".$in." AND f.usuario = '$usuario'";
	}
}

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$registro = "SELECT f.facturas_id AS 'facturas_id', f.fecha AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre,'',c.apellido) AS 'profesional', sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', f.servicio_id AS 'servicio_id', f.colaborador_id AS 'colaborador_id', f.fecha AS 'fecha_consulta', (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY f.fecha, f.number ASC";
$result = $mysqli->query($registro) or die($mysqli->error);

//OBTENER NOMBRE DE EMPRESA
$query_empresa = "SELECT e.nombre AS 'empresa'
FROM users AS u
INNER JOIN empresa AS e
ON u.empresa_id = e.empresa_id
WHERE u.colaborador_id = '$usuario'";
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);
$consulta_empresa = $result_empresa->fetch_assoc();

$empresa_nombre = '';

if($result_empresa->num_rows>0){
   $empresa_nombre = $consulta_empresa['empresa'];	
}  
 
//OBTENER NOMBRE DEL PROFESIONAL 
$query_profesional = "SELECT CONCAT(nombre,'',apellido) AS 'profesional'
FROM colaboradores
WHERE colaborador_id = '$profesional'";
$result_profesional = $mysqli->query($query_profesional) or die($mysqli->error);
$consulta_profeisonal = $result_profesional->fetch_assoc();

$profesional_nombre = '';

if($result_empresa->num_rows>0){
   $profesional_nombre = $consulta_profeisonal['profesional'];	
} 

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("Reporte Facturacion"); //titulo
 
//inicio estilos
$titulo = new PHPExcel_Style(); //nuevo estilo
$titulo->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 12
    )
));
 
$subtitulo = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
	  'bold' => true,
      'size' => 11
    ),	
	'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),'fill' => array( //relleno de color
      'type' => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array('rgb' => 'bfbfbf')
    ),
	'borders' => array( //bordes
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
));

$texto = new PHPExcel_Style(); //nuevo estilo
$texto->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 10
    ),
	'borders' => array( //bordes
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
));
 
$other = new PHPExcel_Style(); //nuevo estilo
$other->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 10
    )
));

$bordes = new PHPExcel_Style(); //nuevo estilo
 
$bordes->applyFromArray(
  array('borders' => array(
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    ),
	'alignment' => array( //alineacion
      'wrap' => true
    ),
));

$totales = new PHPExcel_Style(); //nuevo estilo
$totales->applyFromArray(
  array('font' => array( //fuente
      'bold' => true,
      'size' => 12
    ),
	'borders' => array(
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
));

//fin estilos
 
$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("Reporte de Facturacion"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('D5'); //INMOVILIZA PANELES
//establecer impresion a pagina completa
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
//fin: establecer impresion a pagina completa
 
//establecer margenes
$margin = 0.5 / 2.54; // 0.5 centimetros
$marginBottom = 1.2 / 2.54; //1.2 centimetros
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($marginBottom);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight($margin);


//incluir imagen

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setPath('../../img/logo.png'); //ruta
$objDrawing->setWidth(200); //Ancho
$objDrawing->setHeight(60); //Alto
$objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); //incluir la imagen
//establecer titulos de impresion en cada hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 5);
 
$fila=1;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A3:O3");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $empresa_nombre);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:O$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:O$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A4:O4");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Reporte de Facturas para el Profesional: $profesional_nombre");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:O$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:O$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:O5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:O$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:O$fila");

$fila=4;

$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Factura');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Fecha');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Paciente');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Factura');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Monto');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'ISV');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Descuento');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Neto');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Consultorio');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", 'Profesional');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
$objPHPExcel->getActiveSheet()->SetCellValue("M$fila", 'Gravada');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("N$fila", 'Excenta');
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", 'Atención');
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(50);

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:O$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:O$fila")->getFont()->setBold(true); //negrita
 
//rellenar con contenido
$valor = 1;
$total_precio_neto = 0; 
$total_isv_neto = 0;
$total_descuento_neto = 0;
$total_total_neto = 0;

if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){
		$facturas_id = $registro2['facturas_id'];
		//CONSULTAR DATOS DETALLE DE Factura
		$query_detalle = "SELECT precio, descuento, cantidad, isv_valor
			FROM facturas_detalle
			WHERE facturas_id = '$facturas_id'";
		$result_detalles = $mysqli->query($query_detalle) or die($mysqli->error);

		$cantidad = 0;
		$descuento = 0;
		$precio = 0;
		$total_precio = 0;
		$total = 0;
		$isv_neto = 0;
		$neto_antes_isv = 0;
	
		while($registrodetalles = $result_detalles->fetch_assoc()){
			$precio += ($registrodetalles["precio"] * $registrodetalles["cantidad"]);
			$cantidad += $registrodetalles["cantidad"];
			$descuento += $registrodetalles["descuento"];
			$total_precio = $registrodetalles["precio"] * $registrodetalles["cantidad"];
			$neto_antes_isv += $total_precio;
			$isv_neto += $registrodetalles["isv_valor"];
		}
	
		$total = ($neto_antes_isv + $isv_neto) - $descuento; 
		
		$fila+=1;

		if($registro2['numero'] != ""){
			$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
		}else{
			$numero = "Aún no se ha generado";
		}
	
		$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['tipo_documento']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['fecha']);

		if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
		}else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
			  
		$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['paciente']);			  
		$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $numero);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $precio);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $isv_neto);		
		$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $descuento);
		$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total);
		$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['servicio']);
		$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro2['profesional']);

		$total_precio_neto += $precio; 
		$total_isv_neto += $isv_neto;
		$total_descuento_neto += $descuento;
		$total_total_neto += $total;	
		
		//CONSULTAR LOS PRODUCTOS ENTREGADOS AL PACIENTE
		$atencion = "";
		
		$query_productos = "SELECT p.nombre AS 'producto'
			FROM facturas AS f
			INNER JOIN facturas_detalle AS fd
			ON f.facturas_id = fd.facturas_id
			INNER JOIN productos AS p
			ON fd.productos_id = p.productos_id
			WHERE f.facturas_id = '$facturas_id'";
		$result_atencion = $mysqli->query($query_productos);

		while($registro_atencion = $result_atencion->fetch_assoc()){
			$atencion .= $registro_atencion['producto'].", ";
		}

		$atencion = rtrim($atencion,', ');
		$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $atencion);			

		//Establecer estilo
		$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:O$fila");	
		$valor++;
   }	
}

$fila+=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $total_precio_neto);
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $total_isv_neto);
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $total_descuento_neto);
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total_total_neto);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:F$fila"); //unir celdas 
$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:J$fila");

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional == ""){
		$where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado'";
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado'";
	}
}else{
	if($profesional == ""){
		$where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado' AND p.usuario = '$usuario'";
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado' AND p.usuario = '$usuario'";
	}
}

//CONSULTAR EL TIPO DE PAGO Y AGRUPARLO
$query_pago = "SELECT tp.nombre AS 'tipo_pago', b.nombre AS 'banco', SUM(pd.efectivo) AS 'neto'
	FROM pagos AS p
	INNER JOIN pagos_detalles AS pd
	ON p.pagos_id = pd.pagos_id
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id
	LEFT JOIN banco AS b						
	ON pd.banco_id = b.banco_id
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	".$where."
	GROUP BY tp.tipo_pago_id";
$result_pago = $mysqli->query($query_pago) or die($mysqli->error);

$fila+=1;
$fila+=1;
$fila+=1;

$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Tipo Pago');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Banco');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Neto');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:D$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:D$fila")->getFont()->setBold(true); //negrita

$valor = 1;
$total = 0;
if($result_pago->num_rows>0){
	while($registro2 = $result_pago->fetch_assoc()){ 
	   $fila+=1;
	   $total += $registro2['neto'];
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);	
       $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['tipo_pago']);			  
	   $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['banco']);  
	   $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro2['neto']);  	   
	   
       //Establecer estilo
       $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:D$fila");	
	   $valor++;
   }	
}
$fila+=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $total);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:C$fila"); //unir celdas 

//CREDITO
if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRAOD, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 4";	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 4";
	}
}else{
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 4 AND f.usuario = '$usuario'";	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 4 AND f.usuario = '$usuario'";
	}
}

//EJECUTAMOS LA CONSULTA DE BUSQUEDA
$registro_credito = "SELECT f.facturas_id AS 'facturas_id', f.fecha AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre,'',c.apellido) AS 'profesional', sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', f.servicio_id AS 'servicio_id', f.colaborador_id AS 'colaborador_id', f.fecha AS 'fecha_consulta', (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY f.fecha, f.number ASC";
$result_credito = $mysqli->query($registro_credito) or die($mysqli->error);

if($result_credito->num_rows>0){
	$fila+=4;
	$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "FACTURAS AL CREDITO");
	$objPHPExcel->getActiveSheet()->mergeCells("A$fila:O$fila"); //unir celdas 
	$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:O$fila");
	
	$valor = 1;

	$total_precio_neto = 0; 
	$total_isv_neto = 0;
	$total_descuento_neto = 0;
	$total_total_neto = 0;	
	
	while($registro2 = $result_credito->fetch_assoc()){
		$facturas_id = $registro2['facturas_id'];
		//CONSULTAR DATOS DETALLE DE Factura
		$query_detalle = "SELECT precio, descuento, cantidad, isv_valor
			FROM facturas_detalle
			WHERE facturas_id = '$facturas_id'";
		$result_detalles = $mysqli->query($query_detalle) or die($mysqli->error);

		$cantidad = 0;
		$descuento = 0;
		$precio = 0;
		$total_precio = 0;
		$total = 0;
		$isv_neto = 0;
		$neto_antes_isv = 0;
	
		while($registrodetalles = $result_detalles->fetch_assoc()){
			$precio += ($registrodetalles["precio"] * $registrodetalles["cantidad"]);
			$cantidad += $registrodetalles["cantidad"];
			$descuento += $registrodetalles["descuento"];
			$total_precio = $registrodetalles["precio"] * $registrodetalles["cantidad"];
			$neto_antes_isv += $total_precio;
			$isv_neto += $registrodetalles["isv_valor"];
		}
	
		$total = ($neto_antes_isv + $isv_neto) - $descuento; 
		
		$fila+=1;

		if($registro2['numero'] != ""){
			$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
		}else{
			$numero = "Aún no se ha generado";
		}
	
		$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", "Crédito");
		$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['fecha']);

		if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
		}else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
			  
		$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['paciente']);			  
		$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $numero);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $precio);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $isv_neto);		
		$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $descuento);
		$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total);
		$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['servicio']);
		$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro2['profesional']);

		$total_precio_neto += $precio; 
		$total_isv_neto += $isv_neto;
		$total_descuento_neto += $descuento;
		$total_total_neto += $total;	
		
		//CONSULTAR LOS PRODUCTOS ENTREGADOS AL PACIENTE
		$atencion = "";
		
		$query_productos = "SELECT p.nombre AS 'producto'
			FROM facturas AS f
			INNER JOIN facturas_detalle AS fd
			ON f.facturas_id = fd.facturas_id
			INNER JOIN productos AS p
			ON fd.productos_id = p.productos_id
			WHERE f.facturas_id = '$facturas_id'";
		$result_atencion = $mysqli->query($query_productos);

		while($registro_atencion = $result_atencion->fetch_assoc()){
			$atencion .= $registro_atencion['producto'].", ";
		}

		$atencion = rtrim($atencion,', ');
		$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $atencion);			

		//Establecer estilo
		$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:O$fila");	
		$valor++;
   }	
   
	$fila+=1;
	$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
	$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $total_precio_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $total_isv_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $total_descuento_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total_total_neto);
	$objPHPExcel->getActiveSheet()->mergeCells("A$fila:F$fila"); //unir celdas 
	$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:J$fila");    
}

//ANULADAS
if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRAOD, ADMINISTRADOR Y CONTADOR GENERAL
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 3";	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 3";
	}
}else{
	if($profesional == ""){
		$where = "WHERE f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 3 AND f.usuario = '$usuario'";	
	}else{
		$where = "WHERE f.colaborador_id = '$profesional' AND f.fecha BETWEEN '$desde' AND '$hasta' AND f.estado = 3 AND f.usuario = '$usuario'";
	}
}

//EJECUTAMOS LA CONSULTA DE BUSQUEDA
$registro_anuladas = "SELECT f.facturas_id AS 'facturas_id', f.fecha AS 'fecha', p.identidad AS 'identidad', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', sc.prefijo AS 'prefijo', f.number AS 'numero', s.nombre AS 'servicio', CONCAT(c.nombre,'',c.apellido) AS 'profesional', sc.relleno AS 'relleno', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', f.pacientes_id AS 'pacientes_id', f.cierre AS 'cierre', f.servicio_id AS 'servicio_id', f.colaborador_id AS 'colaborador_id', f.fecha AS 'fecha_consulta', (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	".$where."
	ORDER BY f.fecha, f.number ASC";
$result_anuladas = $mysqli->query($registro_anuladas) or die($mysqli->error);

if($result_anuladas->num_rows>0){
	$fila+=4;
	$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "FACTURAS ANULADAS");
	$objPHPExcel->getActiveSheet()->mergeCells("A$fila:O$fila"); //unir celdas 
	$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:O$fila");
	
	$valor = 1;
	
	$total_precio_neto = 0; 
	$total_isv_neto = 0;
	$total_descuento_neto = 0;
	$total_total_neto = 0;	
	
	while($registro2 = $result_anuladas->fetch_assoc()){
		$facturas_id = $registro2['facturas_id'];
		//CONSULTAR DATOS DETALLE DE Factura
		$query_detalle = "SELECT precio, descuento, cantidad, isv_valor
			FROM facturas_detalle
			WHERE facturas_id = '$facturas_id'";
		$result_detalles = $mysqli->query($query_detalle) or die($mysqli->error);

		$cantidad = 0;
		$descuento = 0;
		$precio = 0;
		$total_precio = 0;
		$total = 0;
		$isv_neto = 0;
		$neto_antes_isv = 0;
	
		while($registrodetalles = $result_detalles->fetch_assoc()){
			$precio += ($registrodetalles["precio"] * $registrodetalles["cantidad"]);
			$cantidad += $registrodetalles["cantidad"];
			$descuento += $registrodetalles["descuento"];
			$total_precio = $registrodetalles["precio"] * $registrodetalles["cantidad"];
			$neto_antes_isv += $total_precio;
			$isv_neto += $registrodetalles["isv_valor"];
		}
	
		$total = ($neto_antes_isv + $isv_neto) - $descuento; 
		
		$fila+=1;

		if($registro2['numero'] != ""){
			$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
		}else{
			$numero = "Aún no se ha generado";
		}
	
		$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", "Anuladas");
		$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['fecha']);

		if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
		}else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
			  
		$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['paciente']);			  
		$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $numero);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $precio);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $isv_neto);		
		$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $descuento);
		$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total);
		$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['servicio']);
		$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro2['profesional']);

		$total_precio_neto += $precio; 
		$total_isv_neto += $isv_neto;
		$total_descuento_neto += $descuento;
		$total_total_neto += $total;	
		
		//CONSULTAR LOS PRODUCTOS ENTREGADOS AL PACIENTE
		$atencion = "";
		
		$query_productos = "SELECT p.nombre AS 'producto'
			FROM facturas AS f
			INNER JOIN facturas_detalle AS fd
			ON f.facturas_id = fd.facturas_id
			INNER JOIN productos AS p
			ON fd.productos_id = p.productos_id
			WHERE f.facturas_id = '$facturas_id'";
		$result_atencion = $mysqli->query($query_productos);

		while($registro_atencion = $result_atencion->fetch_assoc()){
			$atencion .= $registro_atencion['producto'].", ";
		}

		$atencion = rtrim($atencion,', ');
		$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $atencion);			

		//Establecer estilo
		$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:O$fila");	
		$valor++;
   }	
   
	$fila+=1;
	$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
	$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $total_precio_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $total_isv_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $total_descuento_neto);
	$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $total_total_neto);
	$objPHPExcel->getActiveSheet()->mergeCells("A$fila:F$fila"); //unir celdas 
	$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:J$fila");    
}
//Establecer estilo
//*************Guardar como excel 2003*********************************
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); //Escribir archivo
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setDifferentOddEven(false);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('Página &P / &N');

$objPHPExcel->removeSheetByIndex(
    $objPHPExcel->getIndex(
        $objPHPExcel->getSheetByName('Worksheet')
    )
);
 
// Establecer formado de Excel 2003
header("Content-Type: application/vnd.ms-excel");
 
// nombre del archivo
header('Content-Disposition: attachment; filename="Reporte de Facturacion '.' '.$mes.'_'.$año.'.xls"');
//**********************************************************************
 
//****************Guardar como excel 2007*******************************
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); //Escribir archivo
//
//// Establecer formado de Excel 2007
//header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//
//// nombre del archivo
//header('Content-Disposition: attachment; filename="kiuvox.xlsx"');
//**********************************************************************
 
//forzar a descarga por el navegador
$objWriter->save('php://output');

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>