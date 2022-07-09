<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();
 
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

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

$dato = $_GET['dato'];
$estatus = $_GET['status_valor'];
$fecha = date ("Y-m-d");

$dia=date("d", strtotime($fecha));
$mes=date("F", strtotime($fecha));
$año=date("Y", strtotime($fecha));


//EJECUTAMOS LA CONSULTA DE BUSQUEDA

//REGISTROS
if ($dato == ""){
$query = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, 
       u.type AS tipo, u.estatus AS estatus, tipo.nombre AS 'tipo_usuario'
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   INNER JOIN tipo_user AS tipo
	   ON u.type = tipo.tipo_user_id
	   WHERE u.estatus = '$estatus'
	   ORDER BY u.id ASC";		   
}else{
$query = "SELECT u.id AS id, c.nombre AS nombre, c.apellido AS apellido, u.username AS username, u.email AS email, e.nombre AS empresa, 
       u.type AS tipo, u.estatus AS estatus, tipo.nombre AS 'tipo_usuario'
       FROM users AS u
       INNER JOIN colaboradores AS c
       ON u.colaborador_id = c.colaborador_id 
       INNER JOIN empresa AS e
       ON c.empresa_id = e.empresa_id
	   INNER JOIN tipo_user AS tipo
	   ON u.type = tipo.tipo_user_id	   
	   WHERE u.estatus = '$estatus' AND (u.id LIKE '$dato%' OR CONCAT(c.nombre,' ',c.apellido) LIKE '$dato%' OR u.username LIKE '$dato%' OR tipo.nombre LIKE '$dato%' OR u.email LIKE '$dato%')
	   ORDER BY u.id ASC";   
}

$result = $mysqli->query($query);

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("REPORTE USUARIOS DEL SISTEMA"); //titulo
 
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
$objPHPExcel->getActiveSheet()->setTitle("REPORTE USUARIOS DEL SISTEMA"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
 
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
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:H$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:H$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "REPORTE DE AGENDA DE USUARIOS. ".strtoupper($mes)." $dia, $año");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:H$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:H$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'Código');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Nombre');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Apellido');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Username');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'E-mail');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40); 
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Empresa');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40); 
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Tipo');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Estatus');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10); 
$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:H$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:H$fila")->getFont()->setBold(true); //negrita

$valor = 1;
if($result->num_rows>0){
		while($registro1 = $result->fetch_assoc()){
	       $fila+=1;

		if ($registro1['tipo'] == 1)
			$tipo = "Administrador";
		else if ($registro1['tipo'] == 2)
		    $tipo = "Médicos";	
		else if ($registro1['tipo'] == 3)
		    $tipo = "Usuarios";	
		else if ($registro1['tipo'] == 4)
		    $tipo = "Atención Usuarios";				
		else if ($registro1['tipo'] == 5)
		    $tipo = "Agenda";		
		else if ($registro1['tipo'] == 6)
		    $tipo = "Archivo";		
       else if ($registro1['tipo'] == 7)
		    $tipo = "Gerencia";				
	   else if ($registro1['tipo'] == 8)
		    $tipo = "Asistencial";	
	   else if ($registro1['tipo'] == 9)
		    $tipo = "Información Usuarios";	
       else if ($registro1['tipo'] == 11)
		    $tipo = "Enfermeras";				
			
		   if ($registro1['estatus'] == 1)
		      $status = "Activo";
		   else
		      $status = "Inactivo";		   
	       $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $registro1['id']);
           $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro1['nombre']);
           $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro1['apellido']);
           $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro1['username']);
           $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro1['email']);
           $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro1['empresa']);
           $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $tipo);
           $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $status);
		   $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:H$fila");
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
header('Content-Disposition: attachment; filename="USUARIOS DEL SISTEMA '.strtoupper($mes).' '.$dia.', '.$año.'.xls"');
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
?>