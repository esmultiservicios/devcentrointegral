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
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);

$empresa_nombre = '';

if($result_empresa->num_rows>0){
	$consulta_empresa = $result_empresa->fetch_assoc();
	$empresa_nombre = $consulta_empresa['empresa'];
}

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$profesional = $_GET['profesional'];
$usuario = $_GET['usuario'];

$mes=nombremes(date("m", strtotime($fechai)));
$mes1=nombremes(date("m", strtotime($fechaf)));
$año=date("Y", strtotime($fechai));
$año2=date("Y", strtotime($fechaf));

$unidad_name = "";
$usuario_nombre = "";

if($profesional != "" && $usuario == ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf'";	
}if($profesional != "" && $usuario != ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND s.user = '$usuario' ";	

    //OBTENER NOMBRE DE USUARIOS
    $consulta_nombre_usuario = "SELECT nombre, apellido 
	      FROM colaboradores 
		  WHERE colaborador_id = '$usuario'";
	$result = $mysqli->query($consulta_nombre_usuario);
    $consulta_nombre_usuario1 = $result->fetch_assoc();
	$nombre_ = explode(" ", $consulta_nombre_usuario1['nombre']);
    $nombre_usuario = $nombre_[0];
	$apellido_ = explode(" ", $consulta_nombre_usuario1['apellido']);	
	$nombre_apellido = $apellido_[0];
	$usuario_nombre = "Reporte por: ".$nombre_usuario.' '.$nombre_apellido;   
}else{
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf'";		
}
//EJECUTAMOS LA CONSULTA DE BUSQUEDA
//REGISTROS

$registro = "SELECT CONCAT(c.nombre,' ',c.apellido) AS 'colaborador',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 1 THEN s.paciente END) AS '1',  
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 2 THEN s.paciente END) AS '2',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 3 THEN s.paciente END) AS '3',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 4 THEN s.paciente END) AS '4',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 5 THEN s.paciente END) AS '5',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 6 THEN s.paciente END) AS '6',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 7 THEN s.paciente END) AS '7',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 8 THEN s.paciente END) AS '8',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 9 THEN s.paciente END) AS '9',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 10 THEN s.paciente END) AS '10',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 11 THEN s.paciente END) AS '11',  
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 12 THEN s.paciente END) AS '12',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 13 THEN s.paciente END) AS '13',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 14 THEN s.paciente END) AS '14',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 15 THEN s.paciente END) AS '15',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 16 THEN s.paciente END) AS '16',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 17 THEN s.paciente END) AS '17',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 18 THEN s.paciente END) AS '18',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 19 THEN s.paciente END) AS '19',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 20 THEN s.paciente END) AS '20',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 21 THEN s.paciente END) AS '21',  
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 22 THEN s.paciente END) AS '22',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 23 THEN s.paciente END) AS '23',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 24 THEN s.paciente END) AS '24',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 25 THEN s.paciente END) AS '25',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 26 THEN s.paciente END) AS '26',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 27 THEN s.paciente END) AS '27',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 28 THEN s.paciente END) AS '28',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 29 THEN s.paciente END) AS '29',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 30 THEN s.paciente END) AS '30',
COUNT(CASE WHEN DAY((CAST(s.fecha_registro AS DATE))) = 31 THEN s.paciente END) AS '31',
COUNT(s.paciente) AS 'Total'
FROM sms AS s
INNER JOIN colaboradores AS c
ON s.user = c.colaborador_id
".$where."
GROUP BY c.colaborador_id";
$result = $mysqli->query($registro);

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("REPORTE DIARIO DE SMS"); //titulo
 
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

$firma = new PHPExcel_Style(); //nuevo estilo
$firma->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'size' => 12,
	  'bold' => true
    ),
	'borders' => array(
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
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
$objPHPExcel->getActiveSheet()->setTitle("REPORTE DIARIO SMS"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('C5'); //INMOVILIZA PANELES 
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
$objDrawing->setHeight(160); //altura
$objDrawing->setWidth(160); //anchura
$objDrawing->setCoordinates('A1');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); //incluir la imagen

//establecer titulos de impresion en cada hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 5);
 
$fila=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", strtoupper($empresa_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:AG$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Reporte Diario SMS");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:AG$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:AG$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $usuario_nombre);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:AG$fila");

$fila=5;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'Colaborador');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", '1');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", '2');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", '3');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", '4');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", '5');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", '6');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", '7');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", '8');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", '9');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", '10');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", '11');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("M$fila", '12');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("N$fila", '13');
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", '14');
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("P$fila", '15');
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", '16');
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("R$fila", '17');
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("S$fila", '18');
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("T$fila", '19');
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("U$fila", '20');
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("V$fila", '21');
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("W$fila", '22');
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("X$fila", '23');
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("Y$fila", '24');
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("Z$fila", '25');
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AA$fila", '26');
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AB$fila", '27');
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AC$fila", '28');
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AD$fila", '29');
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AE$fila", '30');
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AF$fila", '31');
$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(4); 
$objPHPExcel->getActiveSheet()->SetCellValue("AG$fila", 'Total');
$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(6);  

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:AG$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:AG$fila")->getFont()->setBold(true); //negrita

$total = 0;
//rellenar con contenido
if($result->num_rows>0){
	while($registro1 = $result->fetch_assoc()){
	    $fila+=1;
       
        $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $registro1['colaborador']);
        $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro1['1']);
        $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro1['2']);
        $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro1['3']);
        $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro1['4']);
        $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro1['5']);
        $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro1['6']);
        $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro1['7']);
        $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro1['8']);
        $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro1['9']);
        $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro1['10']);
        $objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro1['11']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("M$fila", $registro1['12']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("N$fila", $registro1['13']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $registro1['14']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("P$fila", $registro1['15']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", $registro1['16']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("R$fila", $registro1['17']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("S$fila", $registro1['18']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("T$fila", $registro1['19']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("U$fila", $registro1['20']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("V$fila", $registro1['21']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("W$fila", $registro1['22']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("X$fila", $registro1['23']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("Y$fila", $registro1['24']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("Z$fila", $registro1['25']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AA$fila", $registro1['26']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AB$fila", $registro1['27']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AC$fila", $registro1['28']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AD$fila", $registro1['29']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AE$fila", $registro1['30']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AF$fila", $registro1['31']);  
        $objPHPExcel->getActiveSheet()->SetCellValue("AG$fila", $registro1['Total']);  	
        $total = $total + $registro1['Total'];	
        //Establecer estilo
        $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:AG$fila");	     
   }   
}	

$fila+=1;
//$registro_total['Total'];
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "TOTAL"); 
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:AF$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("AG$fila", $total);  		   
//Establecer estilo
$objPHPExcel->getActiveSheet()->setSharedStyle($totales, "A$fila:AG$fila");

$fila+=10; 
$objPHPExcel->getActiveSheet()->SetCellValue("X$fila", "FIRMA ADMISIÓN");
$objPHPExcel->getActiveSheet()->mergeCells("X$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($firma, "X$fila:AG$fila"); 


$fila+=7; 
$objPHPExcel->getActiveSheet()->SetCellValue("X$fila", "FIRMA GESTIÓN PACIENTES");
$objPHPExcel->getActiveSheet()->mergeCells("X$fila:AG$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($firma, "X$fila:AG$fila");  
//*************Guardar como excel 2003*********************************
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); //Escribir archivo
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setDifferentOddEven(false);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('Página &P / &N');
// Establecer formado de Excel 2003
header("Content-Type: application/vnd.ms-excel");
 
// nombre del archivo
header('Content-Disposition: attachment; filename="REPORTE DIARIO SMS '.strtoupper($mes).'_'.$año.'.xls"');
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