<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

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

$dato = $_GET['dato'];
$fecha = $_GET['fecha'];
$fechaf = $_GET['fechaf'];
$servicio = $_GET['servicio'];
$medico_general = $_GET['medico_general'];
$atencion = $_GET['atencion'];
$atencion_nombre = "";

if($atencion == 0){
   $atencion_nombre = "Pendientes.";
}

if($atencion == 1){
   $atencion_nombre = "Atendidos.";
}

if($atencion == 2){
   $atencion_nombre = "Ausentes.";
}

$dia=date("d", strtotime($fecha));
$mes=date("F", strtotime($fecha));
$año=date("Y", strtotime($fecha));

$dia1=date("d", strtotime($fechaf));
$mes1=date("F", strtotime($fechaf));
$año1=date("Y", strtotime($fechaf));
$colaborador_nombre = "";

//CONSULTAR SERVICIO
$consulta_servicio = "SELECT nombre 
   FROM servicios 
   WHERE servicio_id = '$servicio'";
$result = $mysqli->query($consulta_servicio) or die($mysqli->error);
$consulta_servicio1 = $result->fetch_assoc();
$servicio_nombre = "";

if($result->num_rows>0){
   $servicio_nombre = $consulta_servicio1['nombre'];
}

$where = "WHERE cast(a.fecha_cita as date) BETWEEN '$fecha' AND '$fechaf' AND a.servicio_id = '$servicio' AND a.status = '$atencion'";
//REGISTROS

$registro = "SELECT DISTINCT p.identidad AS 'identidad', a.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', a.hora AS 'hora', DATE_FORMAT(a.fecha_cita, '%d/%m/%Y') AS 'fecha',
	   CONCAT(c.nombre, ' ', c.apellido) As doctor, p.telefono1 AS 'telefono1', p.telefono2 AS 'telefono2', c.colaborador_id AS 'colaborador_id', a.observacion as 'observacion', a.comentario as 'comentario'
       FROM agenda AS a 
       INNER JOIN pacientes AS p 
       ON a.pacientes_id = p.pacientes_id 
       INNER JOIN colaboradores AS c 
       ON a.colaborador_id = c.colaborador_id
	   INNER JOIN puesto_colaboradores AS pc
	   ON c.puesto_id = pc.puesto_id  	   
       ".$where."
       ORDER BY a.fecha_cita, c.colaborador_id ASC";
$result = $mysqli->query($registro) or die($mysqli->error);	   

$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("AGENDA DIARIA USUARIOS"); //titulo
 
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
    ),
	'alignment' => array( //alineacion
      'wrap' => true
    ),
));
//fin estilos
 
$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("AGENDA DIARIA USUARIOS"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('F7'); //INMOVILIZA PANELES
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
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:L$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:L$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "REPORTE AGENDA DE USUARIOS. ".strtoupper($servicio_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:L$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:L$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "PROFESIONAL: ".strtoupper($colaborador_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:L$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:P$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "USUARIOS ".strtoupper($atencion_nombre)." ".strtoupper($mes)." $dia, $año Hasta".strtoupper($mes1)." $dia1, $año1");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:P$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:L$fila");

$fila=6;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'No.');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Expediente');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Nombre');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Apellido');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25); 
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Fecha Cita');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12); 
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Hora');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8); 
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Medico');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Teléfono1');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Teléfono2');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15); 
$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Observación');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(70);
$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", 'Comentario');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(70);
$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:L$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:L$fila")->getFont()->setBold(true); //negrita

$valor = 1;
if($result->num_rows>0){
	while($registro1 = $result->fetch_assoc()){
	  
	  if ($registro1['expediente'] == 0){
		  $expediente = "TEMP"; 
	  }else{
		  $expediente = $registro1['expediente'];
	  }		  

	  $fecha = date('Y-m-d',strtotime($registro1['fecha']));	  
	       $fila+=1;
	       $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
	   
	       if( strlen($registro1['identidad'])<10 ){
		      $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
    	   }else{
		      $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$fila", $registro1['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	       }		   
          
			   
		   $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $expediente);
           $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro1['nombre']);
           $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro1['apellido']);
           $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro1['fecha']);		   
           $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro1['hora']);
           $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro1['doctor']);
           $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro1['telefono1']);
           $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro1['telefono2']);
           $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro1['observacion']);
           $objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro1['comentario']);		   
           $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:L$fila");			   
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
header('Content-Disposition: attachment; filename="REPORTE AGENDA DIARIA '.strtoupper($servicio_nombre).' '.strtoupper($colaborador_nombre).'_'.strtoupper($mes).' '.$dia.', '.$año.'.xls"');
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