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
$dato = $_GET['dato'];

$mes=nombremes(date("m", strtotime($fechai)));
$mes1=nombremes(date("m", strtotime($fechaf)));
$año=date("Y", strtotime($fechai));
$año2=date("Y", strtotime($fechaf));

//EJECUTAMOS LA CONSULTA DE BUSQUEDA
$usuario_nombre = "";
if($profesional != "" && $usuario == ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";	
}if($profesional != "" && $usuario != ""){
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND s.user = '$usuario' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";	

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
  $where = "WHERE CAST(s.fecha_registro AS DATE) BETWEEN '$fechai ' AND '$fechaf' AND (p.expediente LIKE '%$dato%' OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' OR p.apellido LIKE '$dato%' OR p.identidad LIKE '$dato%' OR p.telefono1 LIKE '$dato%' OR p.telefono2 LIKE '$dato%')";		
}
	
$registro = "SELECT s.*, CAST(s.fecha_registro AS DATE) 'fecha_envio', c.nombre AS 'colaborador_nombre', c.apellido As 'colaborador_apellido', c1.colaborador_id AS 'user_id', c1.nombre AS 'usuario_nombre', c1.apellido As 'usuario_apellido', p.identidad, p.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', serv.nombre AS 'servicio', s.dias AS 'dias', CONCAT(p.apellido, ' ', p.nombre) As 'paciente', (CASE WHEN s.paciente = 'N' THEN 'X' ELSE '' END) AS 'nuevo', (CASE WHEN s.paciente = 'S' THEN 'X' ELSE '' END) AS 'subsiguiente', (CASE WHEN p.genero = 'H' THEN 'X' ELSE '' END) AS 'h', (CASE WHEN p.genero = 'M' THEN 'X' ELSE '' END) AS 'm'
       FROM sms AS s 
       INNER JOIN colaboradores AS c 
       ON s.colaborador_id = c.colaborador_id 
       INNER JOIN colaboradores AS c1 
       ON s.user = c1.colaborador_id 
       INNER JOIN pacientes AS p 
       ON s.pacientes_id = p.pacientes_id  
	   INNER JOIN servicios AS serv
       ON s.servicio_id = serv.servicio_id	
       ".$where."
	   ORDER BY s.fecha_registro, s.pacientes_id";   
$result = $mysqli->query($registro);
   
  
$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("Reporte Pendientes"); //titulo
 
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
$objPHPExcel->getActiveSheet()->setTitle("Reporte Pendientes"); //establecer titulo de hoja
 
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
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A3:R3");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", strtoupper($empresa_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A4:R4");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Registro de SMS Enviados");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:R5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $usuario_nombre);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:R$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:R$fila");

$fila=5;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$objPHPExcel->getActiveSheet()->mergeCells("A5:A6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Fecha Envío');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$objPHPExcel->getActiveSheet()->mergeCells("B5:B6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Expediente');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
$objPHPExcel->getActiveSheet()->mergeCells("C5:C6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->mergeCells("D5:D6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Nombre del Usuario');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40); 
$objPHPExcel->getActiveSheet()->mergeCells("E5:E6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Sexo');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(24); 
$objPHPExcel->getActiveSheet()->mergeCells("F5:G5"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Paciente');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25); 
$objPHPExcel->getActiveSheet()->mergeCells("H5:I5"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Fecha de Cita');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13); 
$objPHPExcel->getActiveSheet()->mergeCells("J5:J6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Servicio');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->mergeCells("K5:K6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", 'Profesional');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->mergeCells("L5:L6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("M$fila", 'De');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
$objPHPExcel->getActiveSheet()->mergeCells("M5:M6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("N$fila", 'Para');
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
$objPHPExcel->getActiveSheet()->mergeCells("N5:N6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", 'Mensaje');
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(40);
$objPHPExcel->getActiveSheet()->mergeCells("O5:O6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("P$fila", 'Estado');
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(8);
$objPHPExcel->getActiveSheet()->mergeCells("P5:P6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", 'Días');
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(8);
$objPHPExcel->getActiveSheet()->mergeCells("Q5:Q6"); //unir celdas

$objPHPExcel->getActiveSheet()->SetCellValue("R$fila", 'Usuario');
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(16);
$objPHPExcel->getActiveSheet()->mergeCells("R5:R6"); //unir celdas

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:R$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:R$fila")->getFont()->setBold(true); //negrita

$fila=6; 
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Hombre');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12); 

$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Mujer');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12); 

$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Nuevo');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10); 

$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Subisugiente');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:R$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:R$fila")->getFont()->setBold(true); //negrita

//rellenar con contenido
$valor = 1;
if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){
	   $fila+=1;
	   if ($registro2['expediente'] == 0){
		    $expediente = "TEMP"; 
	   }else{
		   $expediente = $registro2['expediente'];
	   }	 
		
	   if ($registro2['dias'] == 1){
			$dias_ = $registro2['dias']. " Día";
	   }else{
		   $dias_ = $registro2['dias']." Días";
	   }
	   
       $usuario_nombre = explode(" ", $registro2['usuario_nombre']);
       $nombre_usuario = $usuario_nombre[0];
	   $usuario_apellido = explode(" ", $registro2['usuario_apellido']);	
	   $apellido_usuario = $usuario_apellido[0];
       $nombre_completo_usuario = $apellido_usuario.' '.$nombre_usuario;
		
	   $colaborador_nombre = explode(" ", $registro2['colaborador_nombre']);
       $nombre_colaborador = $colaborador_nombre[0];
	   $colaborador_apellido = explode(" ", $registro2['colaborador_apellido']);	
	   $apellido_colaborador = $colaborador_apellido[0];
       $nombre_completo_colaborador = $apellido_colaborador.' '.$nombre_colaborador;	   
		
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
	   $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['fecha_envio']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $expediente); 
	   if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
	   }else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	   }	  
       $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['paciente']);	
       $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro2['h']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro2['m']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro2['nuevo']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro2['subsiguiente']);
       $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro2['fecha']);  
       $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['servicio']);  	   
	   $objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $nombre_completo_colaborador);
	   $objPHPExcel->getActiveSheet()->SetCellValue("M$fila", $registro2['de']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("N$fila", $registro2['para']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $registro2['mensaje']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("P$fila", $registro2['status']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("Q$fila", $dias_ );
       $objPHPExcel->getActiveSheet()->SetCellValue("R$fila", $nombre_completo_usuario);	   
	   
       //Establecer estilo
       $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:R$fila");	
	   $valor++;
   }	
}
 
//*************Guardar como excel 2003*********************************
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); //Escribir archivo
 
$fila+=5; 
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "HSJD_".strtoupper($mes)."_$año");
$fila+=1; 
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Nombre y Firma del Responsable __________________________________________________________________________________");
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setDifferentOddEven(false);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('Página &P / &N');
// Establecer formado de Excel 2003
header("Content-Type: application/vnd.ms-excel");
 
// nombre del archivo
header('Content-Disposition: attachment; filename="Reporte SMS enviados '.$mes.'_'.$año.'.xls"');
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