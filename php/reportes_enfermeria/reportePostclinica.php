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

$desde = $_GET['desde'];
$hasta = $_GET['hasta'];
$servicio = $_GET['servicio'];
$colaborador_usuario = $_GET['colaborador_usuario'];

$mes=nombremes(date("m", strtotime($desde)));
$mes1=nombremes(date("m", strtotime($hasta)));
$año=date("Y", strtotime($desde));
$año2=date("Y", strtotime($hasta));

$servicio_name = "";
$colaborador_name = "";
$atencion = "";

//OBTENER NOMBRE SERVICIO
$consulta_servicio = "SELECT nombre 
    FROM servicios 
	WHERE servicio_id = '$servicio'";
$result = $mysqli->query($consulta_servicio);
$consulta_servicio1 = $result->fetch_assoc();
$servicio_name = $consulta_servicio1['nombre'];

//EJECUTAMOS LA CONSULTA DE BUSQUEDA
if($colaborador_usuario == ""){
	$where = "WHERE post.fecha BETWEEN '$desde' AND '$hasta' and post.servicio_id = '$servicio'";
}else{
	//OBTENERR NOMBRE COLABORADOR/USUARIO DEL SISTEMA
    $consulta_colaborador_nombre = "SELECT CONCAT(apellido,' ',nombre) AS 'usuario' 
	     FROM colaboradores 
	     WHERE colaborador_id = '$colaborador_usuario'";
    $result = $mysqli->query($consulta_colaborador_nombre);
    $consulta_colaborador_nombre2 = $result->fetch_assoc();
    $colaborador_name = $consulta_colaborador_nombre2['usuario'];
	
	$atencion = " Realizado por: ".$colaborador_name;
	
	$where = "WHERE post.fecha BETWEEN '$desde' AND '$hasta' and post.servicio_id = '$servicio' AND post.usuario = '$colaborador_usuario'";	
}

$registro = "SELECT post.postclinica_id AS 'postclinica_id', DATE_FORMAT(post.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(p.apellido,' ',p.nombre) AS 'nombre', post.expediente AS 'expediente', p.identidad AS 'identidad', post.edad AS 'edad', p.sexo AS 'sexo', pa.patologia_id AS 'patologia', pa.nombre AS 'diagnostico', post.fecha_cita AS 'fecha_cita', post.hora AS 'hora', s.nombre AS 'servicio', CONCAT(c.nombre,' ',c.apellido) AS 'medico', post.instrucciones AS 'instrucciones', post.precedimiento As 'procedimiento', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
      FROM postclinica AS post
      INNER JOIN pacientes AS p
      ON post.expediente = p.expediente
      INNER JOIN patologia AS pa
      ON post.diagnostico = pa.id
      INNER JOIN servicios AS s
      ON post.servicio_id = s.servicio_id	
      INNER JOIN colaboradores AS c
      ON post.colaborador_id = c.colaborador_id
      INNER JOIN colaboradores AS c1
      ON post.usuario = c1.colaborador_id
      ".$where."
      ORDER BY post.fecha, post.expediente ASC";
$result = $mysqli->query($registro);	  
 
$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("Postclínica"); //titulo
 
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

$subtitulo1 = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo1->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
	  'bold' => true,
      'size' => 11
    ),	
	'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
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

//BORDES INFERIORES
$bordes1 = new PHPExcel_Style(); //nuevo estilo
 
$bordes1->applyFromArray(
  array('borders' => array(
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    ),
	'alignment' => array( //alineacion
      'wrap' => true
    ),
));

//fin estilos
 
$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("Reporte Prclinica"); //establecer titulo de hoja
 
//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
 
//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
$objPHPExcel->getActiveSheet()->freezePane('F6'); //INMOVILIZA PANELES
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

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setPath('../../img/sesal_logo.png'); //ruta
$objDrawing->setHeight(60); //altura
$objDrawing->setCoordinates('P1');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); //incluir la imagen
//fin: establecer margenes
 
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
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A3:P3");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", strtoupper($empresa_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:P$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:P$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A4:P4");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Registro de Usuarios en Postclínica ($servicio_name)");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:P$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:P$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:P5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Desde: $mes $año Hasta: $mes1 $año2");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:P$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:P$fila");

$fila=4;
$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A5:P5");
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $atencion);
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:P$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:P$fila");

$fila=5;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'N°');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->mergeCells("A5:A5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("B$fila", 'Fecha');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$objPHPExcel->getActiveSheet()->mergeCells("B5:B5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Nombre del Usuario');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45); 
$objPHPExcel->getActiveSheet()->mergeCells("C5:C5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Expediente');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13); 
$objPHPExcel->getActiveSheet()->mergeCells("D5:D5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Identidad');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->mergeCells("E5:E5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("F$fila", 'Edad');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$objPHPExcel->getActiveSheet()->mergeCells("F5:F5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("G$fila", 'Sexo');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
$objPHPExcel->getActiveSheet()->mergeCells("G5:G5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'CIE-10');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->mergeCells("H5:H5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'CIE-10 Completo');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
$objPHPExcel->getActiveSheet()->mergeCells("I5:I5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Fecha de Cita');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->mergeCells("J5:J5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Hora');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
$objPHPExcel->getActiveSheet()->mergeCells("K5:K5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("L$fila", 'Servicio');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->mergeCells("L5:L5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("M$fila", 'Médico');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
$objPHPExcel->getActiveSheet()->mergeCells("M5:M5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("N$fila", 'Instrucciones');
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
$objPHPExcel->getActiveSheet()->mergeCells("N5:N5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("O$fila", 'Procedimiento');
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
$objPHPExcel->getActiveSheet()->mergeCells("O5:O5"); //unir celdas
$objPHPExcel->getActiveSheet()->SetCellValue("P$fila", 'Usuario');
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
$objPHPExcel->getActiveSheet()->mergeCells("P5:P5"); //unir celdas

$objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:P$fila"); //establecer estilo
$objPHPExcel->getActiveSheet()->getStyle("A$fila:P$fila")->getFont()->setBold(true); //negrita
$fila+=1; 
//rellenar con contenido
$valor = 1;
if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){
	   $fila+=1;
	   $postclinica_id = $registro2['postclinica_id'];
	   
	   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
       $objPHPExcel->getActiveSheet()->SetCellValue("B$fila", $registro2['fecha']);
       $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['nombre']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro2['expediente']);
	   
	   if( strlen($registro2['identidad'])<10 ){
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
	   }else{
		   $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	   }
	   
	   if ($registro2['sexo'] == 'H'){
		   $sexo = "Hombre";
	   }else{
		   $sexo = "Mujer";
	   }
	          
	   $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro2['edad']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $sexo);
	   $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro2['patologia']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro2['diagnostico']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro2['fecha_cita']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", date('H:i',strtotime($registro2['hora'])));
	   $objPHPExcel->getActiveSheet()->SetCellValue("L$fila", $registro2['servicio']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("M$fila", $registro2['medico']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("N$fila", $registro2['instrucciones']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("O$fila", $registro2['procedimiento']);
	   $objPHPExcel->getActiveSheet()->SetCellValue("P$fila", $registro2['usuario']);	
	   
	   $registro_tratamiento = "SELECT postd.medicamento As 'medicamento', postd.dosis As 'dosis', postd.via As 'via', postd.frecuencia As 'frecuencia', postd.recomendaciones AS 'recomendaciones'
           FROM postclinica_detalle AS postd
           INNER JOIN postclinica AS post
           ON postd.postclinica_id = post.postclinica_id
           WHERE postd.postclinica_id = '$postclinica_id'
           ORDER BY postd.postclinica_id ASC";
	   $result_tratamiento = $mysqli->query($registro_tratamiento);
		   
	   if($result_tratamiento->num_rows>0){
		   $fila+=1;
		   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Tratamiento");	
           $objPHPExcel->getActiveSheet()->mergeCells("A$fila:J$fila"); //unir celdas
		   $objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo1, "A$fila:Q$fila"); //establecer estilo
		   $objPHPExcel->getActiveSheet()->getStyle("A$fila:J$fila")->getFont()->setBold(true); //negrita
		   
		   $fila+=1;			   
           $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", 'Medicamento');
           $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
		   $objPHPExcel->getActiveSheet()->mergeCells("A$fila:B$fila"); //unir celdas
           $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", 'Dosis');
           $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
           $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", 'Vía');
           $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13); 
           $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", 'Frecuencia');
           $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
           $objPHPExcel->getActiveSheet()->mergeCells("E$fila:H$fila"); //unir celdas			   
           $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Recomendaciones');
           $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		   $objPHPExcel->getActiveSheet()->mergeCells("H$fila:J$fila"); //unir celdas
		   $objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo1, "A$fila:P$fila"); //establecer estilo
		   $objPHPExcel->getActiveSheet()->getStyle("A$fila:P$fila")->getFont()->setBold(true); //negrita
		   
		   while($registro2 = $result_tratamiento->fetch_assoc()){			    			   
			   $fila+=1;			   
			   $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $registro2['medicamento']);
			   $objPHPExcel->getActiveSheet()->mergeCells("A$fila:B$fila"); //unir celdas
			   $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $registro2['dosis']);
			   $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro2['via']);
			   $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['frecuencia']);
			   $objPHPExcel->getActiveSheet()->mergeCells("E$fila:H$fila"); //unir celdas
			   $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro2['recomendaciones']);
			   $objPHPExcel->getActiveSheet()->mergeCells("H$fila:J$fila"); //unir celdas
		   }
		   $objPHPExcel->getActiveSheet()->setSharedStyle($bordes1, "A$fila:P$fila");
	   }  
	   $valor++;
   }	
}
 
$fila+=5; 
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "HSJD_".strtoupper($mes)."_$año");
$fila+=1; 
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "Nombre y Firma del Responsable __________________________________________________________________________________"); 
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
header('Content-Disposition: attachment; filename="Postclinica '.$servicio_name.' '.$mes.'_'.$año.'.xls"');
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
$result_tratamiento->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>