<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

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

$dato = $_GET['dato'];
$fecha = date ("Y-m-d");

$dia=date("d", strtotime($fecha));
$mes=date("F", strtotime($fecha));
$año=date("Y", strtotime($fecha));


//EJECUTAMOS LA CONSULTA DE BUSQUEDA

//REGISTROS
if ($dato == ""){
$registro = "SELECT c.colaborador_id As 'codigo', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', p.nombre AS 'puesto', e.nombre AS 'empresa', c.identidad as 'identidad'
      FROM colaboradores AS c
      INNER JOIN empresa AS e
      ON c.empresa_id = e.empresa_id
      INNER JOIN puesto_colaboradores AS p
      ON c.puesto_id = p.puesto_id 
      ORDER BY c.colaborador_id ASC";	
}else{
$registro = "SELECT c.colaborador_id As 'codigo', CONCAT(c.nombre, ' ', c.apellido) AS 'nombre', p.nombre AS 'puesto', s.nombre AS 'servicio', e.nombre AS 'empresa', c.identidad as 'identidad'
      FROM colaboradores AS c
      INNER JOIN empresa AS e
      ON c.empresa_id = e.empresa_id
      INNER JOIN puesto_colaboradores AS p
      ON c.puesto_id = p.puesto_id 
	  WHERE c.colaborador_id LIKE '%$dato%' OR c.nombre LIKE '%$dato%' OR c.apellido LIKE '%$dato%' OR s.nombre LIKE '%$dato%' OR p.nombre LIKE '%$dato%'
      ORDER BY c.colaborador_id ASC";	
}

$result = $mysqli->query($registro);

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("REPORTE COLABORADORES"); //titulo
 
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
$objPHPExcel->getActiveSheet()->setTitle("REPORTE COLABORADORES"); //establecer titulo de hoja
 
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
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:E$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:E$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "REPORTE COLABORADORES. ".strtoupper($mes)." $dia, $año");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:E$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:E$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'Código');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Nombre');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35); 
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35); 
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Puesto');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25); 
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Empresa');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);  
$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:E$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:E$fila")->getFont()->setBold(true); //negrita

$valor = 1;
if($result->num_rows>0){
		while($registro1 = $result->fetch_assoc()){
	       $fila+=1;
	       $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $registro1['codigo']);
           $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro1['nombre']);

	       if( strlen($registro1['identidad'])<10 ){
		      $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
	       }else{
		      $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$fila", $registro1['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	       }
	   
           $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro1['puesto']);
           $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro1['empresa']);
           $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:E$fila");		   
		   $valor++;
     }		
 }	
//*************Guardar como excel 2003*********************************
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); //Escribir archivo

$objPHPExcel->removeSheetByIndex(
    $objPHPExcel->getIndex(
        $objPHPExcel->getSheetByName('Worksheet')
    )
);
 
// Establecer formado de Excel 2003
header("Content-Type: application/vnd.ms-excel");
 
// nombre del archivo
header('Content-Disposition: attachment; filename="REPORTE COLABORADORES '.strtoupper($mes).' '.$dia.', '.$año.'.xls"');
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