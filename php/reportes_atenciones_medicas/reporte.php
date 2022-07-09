<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

//ajuntar la libreria excel
include "../../PHPExcel/Classes/PHPExcel.php";

$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$colaborador = $_GET['colaborador'];
$usuario = $_SESSION['colaborador_id'];	

$mes=nombremes(date("m", strtotime($desde)));
$mes1=nombremes(date("m", strtotime($hasta)));
$año=date("Y", strtotime($desde));
$año2=date("Y", strtotime($hasta));

if($colaborador == ""){
	$where = "WHERE am.fecha BETWEEN '$desde' AND '$hasta'";	
}else{
	$where = "WHERE am.colaborador_id = '$colaborador' AND am.fecha BETWEEN '$desde' AND '$hasta'";	
}

//EJECUTAMOS LA CONSULTA DE BUSQUEDA

$registro = "SELECT am.atencion_id AS 'atencion_id',  DATE_FORMAT(am.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', am.antecedentes AS 'antecedentes', am.historia_clinica AS 'historia_clinica', am.examen_fisico AS 'examen_fisico',am.seguimiento AS 'seguimiento', CONCAT(c.nombre,' ',c.apellido) AS 'colaborador', s.nombre AS 'servicio', am.pacientes_id As 'pacientes_id', am.servicio_id AS 'servicio_id', am.colaborador_id AS 'colaborador_id', am.fecha AS 'fecha_consulta',
(CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h',
(CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm',
(CASE WHEN am.paciente = 'N' THEN 'X' ELSE '' END) AS 'n',
(CASE WHEN am.paciente = 'S' THEN 'X' ELSE '' END) AS 's', d.nombre AS 'departamento', m.nombre AS 'municipio', p.localidad AS 'localidad'
	FROM atenciones_medicas AS am
	INNER JOIN pacientes AS p
	ON am.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
	ON am.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON am.servicio_id = s.servicio_id
	LEFT JOIN departamentos AS d
    ON p.departamento_id = d.departamento_id
    LEFT JOIN municipios AS m
    ON p.municipio_id = m.municipio_id
	".$where."
    ORDER BY am.fecha ASC";
	
$result = $mysqli->query($registro);

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
 
$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("Reporte Atenciones"); //titulo
 
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

//fin estilos
 
$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("Reporte Atenciones"); //establecer titulo de hoja
 
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
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A3:R3");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $empresa_nombre);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A4:R4");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Reporte de Atenciones");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:Q$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:R5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=4;

$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->mergeCells("A4:A5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Fecha');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$objPHPExcel->getActiveSheet()->mergeCells("B4:B5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Nombre del Usuario');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45); 
$objPHPExcel->getActiveSheet()->mergeCells("C4:C5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->mergeCells("D4:D5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Genero');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$objPHPExcel->getActiveSheet()->mergeCells("E4:F4"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Paciente');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
$objPHPExcel->getActiveSheet()->mergeCells("G4:H4"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Procedencia');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(70);
$objPHPExcel->getActiveSheet()->mergeCells("I4:K4"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", 'Antecednetes');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(70);
$objPHPExcel->getActiveSheet()->mergeCells("L4:L5"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("M$fila", 'Historia Clinica');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(70);
$objPHPExcel->getActiveSheet()->mergeCells("M4:M5"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("N$fila", 'Examen Físico');
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(70);
$objPHPExcel->getActiveSheet()->mergeCells("N4:N5"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", 'Seguimiento');
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(70);
$objPHPExcel->getActiveSheet()->mergeCells("O4:O5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("P$fila", 'Colaborador');
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()->mergeCells("P4:P5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", 'Servicio');
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
$objPHPExcel->getActiveSheet()->mergeCells("Q4:Q5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("R$fila", 'Atención');
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
$objPHPExcel->getActiveSheet()->mergeCells("R4:R5"); //unir celdas

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:R$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:R$fila")->getFont()->setBold(true); //negrita

$fila=5;
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'H');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(4);
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'M');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(4);
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Nuevo');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Subsiguiente');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Departamento');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Municipio');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Localidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:R$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:R$fila")->getFont()->setBold(true); //negrita
 
//rellenar con contenido
$valor = 1;
if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){
	   $fila+=1;
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
       $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['fecha']);
       $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['paciente']);
	   
	   if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
	   }else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	   }
	          
	   $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['h']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro2['m']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro2['n']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro2['s']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro2['departamento']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro2['municipio']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['localidad']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro2['antecedentes']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("M$fila", $registro2['historia_clinica']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("N$fila", $registro2['examen_fisico']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $registro2['seguimiento']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("P$fila", $registro2['colaborador']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", $registro2['servicio']);
	   
	   //CONSULTAR LOS PRODUCTOS ENTREGADOS AL PACIENTE
	   $fecha = $registro2['fecha_consulta'];
	   $colaborador_id = $registro2['colaborador_id'];
	   $servicio_id = $registro2['servicio_id'];
	   $pacientes_id = $registro2['pacientes_id'];
	   $atencion = "";
	   
	   $query_productos = "SELECT p.nombre AS 'producto'
			FROM facturas AS f
			INNER JOIN facturas_detalle AS fd
			ON f.facturas_id = fd.facturas_id
			INNER JOIN productos AS p
			ON fd.productos_id = p.productos_id
			WHERE f.fecha = '$fecha' AND f.servicio_id = '$servicio_id' AND f.colaborador_id = '$colaborador_id' AND f.pacientes_id = '$pacientes_id'";
	   $result_atencion = $mysqli->query($query_productos);
	   
	   while($registro_atencion = $result_atencion->fetch_assoc()){
			$atencion .= $registro_atencion['producto'].", ";
	   }
	   
	   $atencion = rtrim($atencion,', ');
	   $objPHPExcel->getActiveSheet()->SetCellValue("R$fila", $atencion);	   
	   
       //Establecer estilo
       $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:R$fila");	
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
header('Content-Disposition: attachment; filename="Reporte de Atenciones '.$mes.'_'.$año.'.xls"');
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