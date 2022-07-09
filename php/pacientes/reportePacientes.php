<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

$dato = $_GET['dato'];
$estado = $_GET['estado'];

//OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];	

$query_empresa = "SELECT e.nombre AS 'empresa'
FROM users AS u
INNER JOIN empresa AS e
ON u.empresa_id = e.empresa_id
WHERE u.colaborador_id = '$usuario'";
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);;

$empresa_nombre = '';

if($result_empresa->num_rows>0){
	$consulta_empresa = $result_empresa->fetch_assoc();
	$empresa_nombre = $consulta_empresa['empresa'];
}

$fecha = date ("Y-m-d");

$dia=date("d", strtotime($fecha));
$mes = nombremes(date("m", strtotime($fecha)));
$año=date("Y", strtotime($fecha));

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

//REGISTROS
$query = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'paciente', identidad, telefono1, telefono2, fecha_nacimiento, expediente AS 'expediente_', localidad,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN genero = 'H' THEN 'Hombre' ELSE 'Mujer' END) AS 'genero',
(CASE WHEN expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente'
FROM pacientes
WHERE estado = '$estado' AND (expediente LIKE '$dato%' OR nombre LIKE '$dato%' OR apellido LIKE '$dato%' OR CONCAT(apellido,' ',nombre) LIKE '%$dato%' OR CONCAT(nombre,' ',apellido) LIKE '%$dato%' OR telefono1 LIKE '$dato%')
ORDER BY expediente";

$result = $mysqli->query($query);

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("REPORTE DE USUARIOS"); //titulo
 
//inicio estilos
$titulo = new PHPExcel_Style(); //nuevo estilo
$titulo->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 13
    )
));

$subtitulo1 = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo1->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
	  'bold' => true,
      'size' => 12
    ),	
	'alignment' => array( //alineacion
      'wrap' => true
    )
));

 
$subtitulo = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
	  'bold' => true,
      'size' => 12
    ),	
	'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
	'borders' => array( //bordes
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
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

$style = new PHPExcel_Style(); //nuevo estilo
$style->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => false,
      'size' => 10
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
    )
));
//fin estilos
 
$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("REPORTE DE USUARIOS"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('E5'); //INMOVILIZA PANELES 
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
//fin: establecer margenes
 
//incluir imagen
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setPath('../../img/logo.png'); //ruta
$objDrawing->setWidth(200); //Ancho
$objDrawing->setHeight(60); //Alto
$objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); //incluir la imagen

//establecer titulos de impresion en cada hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 6);
 
$fila=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", strtoupper($empresa_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:I$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:I$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "REPORTE DE USUARIOS. ".strtoupper($mes)." $dia, $año");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:I$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:I$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'No.');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Expediente');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Paciente');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45); 
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Genero');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10); 
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Telefono1');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Telefono2');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Fecha Naciemiento');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25); 
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Estado');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:I$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:I$fila")->getFont()->setBold(true); //negrita

$valor = 1;
if($result->num_rows>0){
	while($registro1 = $result->fetch_assoc()){
	   $fila+=1;
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
	   $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro1['expediente']);
	   $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$fila", $registro1['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	   $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro1['paciente']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro1['genero']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro1['telefono1']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro1['telefono2']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro1['fecha_nacimiento']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro1['estado']);
	   $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:I$fila");		   
	   $valor++;
     }		
 }	
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
header('Content-Disposition: attachment; filename="REPORTE DE PACIENTES '.strtoupper($mes).' '.$dia.', '.$año.'.xls"');
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

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN

$objWriter->save('php://output');

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>