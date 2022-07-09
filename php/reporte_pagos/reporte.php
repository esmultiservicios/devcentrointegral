<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$dato = $_GET['dato'];
$profesional = $_GET['profesional'];
$usuario = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];	
$estado = 1;
$type = $_SESSION['type'];

$mes=nombremes(date("m", strtotime($desde)));
$mes1=nombremes(date("m", strtotime($hasta)));
$año=date("Y", strtotime($desde));
$año2=date("Y", strtotime($hasta));

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
  if($profesional != ""){
    $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND f.colaborador_id = '$profesional' AND p.estado = '$estado'";
  }else if($dato != ""){
    $where = "WHERE p.estado = '$estado' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%')";
  }else{
    $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado'";
  }
}else{
    if($profesional != ""){
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND f.colaborador_id = '$profesional' AND p.estado = '$estado' AND p.usuario = '$usuario'";
    }else if($dato != ""){
      $where = "WHERE p.estado = '$estado' AND p.usuario = '$usuario' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%')";
    }else{
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado' AND p.usuario = '$usuario'";
    }
}

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$registro = "SELECT p.pagos_id AS 'pagos_id', p.fecha AS 'fecha_pago', p.importe AS 'importe', sc.prefijo AS 'prefijo', f.number AS 'numero', CONCAT(pac.nombre,' ',pac.apellido) AS 'paciente', pac.identidad AS 'identidad', sc.relleno AS 'relleno', tp.nombre AS 'tipo_pago', p.efectivo AS 'efectivo', p.tarjeta AS 'tarjeta'
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN secuencia_facturacion AS sc
	ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
	INNER JOIN pacientes AS pac
	ON f.pacientes_id = pac.pacientes_id
	INNER JOIN pagos_detalles AS pd
	ON p.pagos_id = pd.pagos_id
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id
	".$where."
	ORDER BY p.fecha DESC";	
$result = $mysqli->query($registro) or die($mysqli->error);

//OBTENER NOMBRE DE EMPRESA
$query_empresa = "SELECT e.nombre AS 'empresa'
FROM users AS u
INNER JOIN empresa AS e
ON u.empresa_id = e.empresa_id
WHERE u.colaborador_id = '$usuario'";
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);;
$consulta_empresa = $result_empresa->fetch_assoc();

$empresa_nombre = '';

if($result_empresa->num_rows>0){
   $empresa_nombre = $consulta_empresa['empresa'];	
}  
 
//OBTENER NOMBRE DEL PROFESIONAL 
$query_profesional = "SELECT CONCAT(nombre,'',apellido) AS 'profesional'
FROM colaboradores
WHERE colaborador_id = '$profesional'";
$result_profesional = $mysqli->query($query_profesional) or die($mysqli->error);;
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
$objPHPExcel->getActiveSheet()->setTitle("Transito Enviadas"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('D6'); //INMOVILIZA PANELES
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
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A3:I3");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $empresa_nombre);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:I$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:I$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A4:I4");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Reporte de Pagos para el Profesional: $profesional_nombre");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:I$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:I$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:I5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:I$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:I$fila");

$fila=4;

$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Fecha');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Pciente');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50); 
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Factura');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Pago');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Efectivo');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Tarjeta');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Tipo Pago');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:I$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:I$fila")->getFont()->setBold(true); //negrita
 
//rellenar con contenido
$valor = 1;
$total_neto = 0;

if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){

		$fila+=1;
		$numero = $registro2['prefijo'].''.rellenarDigitos($registro2['numero'], $registro2['relleno']);
		
		$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['fecha_pago']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['paciente']);			  

		if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
		}else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
			  
		$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $numero);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro2['importe']);
    $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro2['efectivo']);
    $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro2['tarjeta']);
		$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro2['tipo_pago']);

		$total_neto += $registro2['importe'];

		//Establecer estilo
		$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:I$fila");	
		$valor++;
   }	
}

$fila+=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $total_neto);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:E$fila"); //unir celdas 
$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:I$fila");

//DETALLE DE PAGOS

if($type == 1 || $type == 2 || $type == 4){//SUPER ADMINISTRADOR, ADMINISTRADOR Y CONTADOR GENERAL
    if($profesional != ""){
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND f.colaborador_id = '$profesional' AND p.estado = '$estado'";
    }else if($dato != ""){
      $where = "WHERE p.estado = '$estado' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%')";
    }else{
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado'";
    }
}else{
    if($profesional != ""){
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND f.colaborador_id = '$profesional' AND p.estado = '$estado' AND p.usuario = '$usuario'";
    }else if($dato != ""){
      $where = "WHERE p.estado = '$estado' AND p.usuario = '$usuario' AND (CONCAT(pac.nombre,' ',pac.apellido) LIKE '%$dato%' OR pac.apellido LIKE '$dato%' OR pac.identidad LIKE '$dato%')";
    }else{
      $where = "WHERE p.fecha BETWEEN '$desde' AND '$hasta' AND p.estado = '$estado' AND p.usuario = '$usuario'";
    }
}

//CONSULTAR EL TIPO DE PAGO Y AGRUPARLO
$query_pago = "SELECT tp.nombre AS 'tipo_pago', b.nombre AS 'banco', SUM(pd.efectivo) AS 'neto'
	FROM pagos AS p
	INNER JOIN facturas AS f
	ON p.facturas_id = f.facturas_id
	INNER JOIN pagos_detalles AS pd
	ON p.pagos_id = pd.pagos_id
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id
	LEFT JOIN banco AS b						
	ON pd.banco_id = b.banco_id
	INNER JOIN pacientes AS pac
	ON f.pacientes_id = pac.pacientes_id	
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
$objPHPExcel->getActiveSheet()->mergeCells("B$fila:C$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Banco');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Neto');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:E$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:E$fila")->getFont()->setBold(true); //negrita

$valor = 1;
$total = 0;
if($result_pago->num_rows>0){
	while($registro2 = $result_pago->fetch_assoc()){ 
	   $fila+=1;
	   $total += $registro2['neto'];
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);	
       $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['tipo_pago']);	
       $objPHPExcel->getActiveSheet()->mergeCells("B$fila:C$fila"); //unir celdas		  
	   $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro2['banco']);  
	   $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['neto']);  	   
	   
       //Establecer estilo
       $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:E$fila");	
	   $valor++;
   }	
}
$fila+=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL");
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $total);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:D$fila"); //unir celdas 
//Establecer estilo
$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:E$fila");
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