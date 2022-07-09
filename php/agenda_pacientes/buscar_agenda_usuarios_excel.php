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

$fecha = $_GET['fecha'];
$fechaf = $_GET['fechaf'];
$servicio = $_GET['servicio'];
$atencion = $_GET['atencion'];
$mes = nombremes(date("m", strtotime($fecha)));
$dia = date("d", strtotime($fecha));
$año = date("Y", strtotime($fecha));

$mes1 = nombremes(date("m", strtotime($fechaf)));
$dia1 = date("d", strtotime($fechaf));
$año1 = date("Y", strtotime($fechaf));

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

$where = "WHERE cast(a.fecha_cita as date) BETWEEN '$fecha' AND '$fechaf' AND a.servicio_id = '$servicio' AND a.status = '$atencion'";

$registro = "SELECT COUNT(c.colaborador_id) AS 'conteo', c.puesto_id AS 'puesto_id', c.colaborador_id AS 'colaborador_id', CONCAT(c.nombre,' ',c.apellido) 'colaborador', s.nombre AS 'servicio'
   FROM jornada_colaboradores AS jc
   INNER JOIN colaboradores AS c
   ON jc.colaborador_id = c.colaborador_id
   INNER JOIN puesto_colaboradores AS pc
   ON c.puesto_id = pc.puesto_id
   INNER JOIN users AS u
   ON jc.colaborador_id = u.colaborador_id
   INNER JOIN agenda AS a
   ON c.colaborador_id = a.colaborador_id
   INNER JOIN servicios AS s
   ON a.servicio_id = s.servicio_id
   ".$where."
   GROUP BY c.colaborador_id
   ORDER BY c.colaborador_id DESC";	
$result = $mysqli->query($registro) or die($mysqli->error);   


$objPHPExcel = new PHPExcel(); //nueva instancia
 
$objPHPExcel->getProperties()->setCreator("ING. EDWIN VELASQUEZ"); //autor
$objPHPExcel->getProperties()->setTitle("AGENDA DIARIA USUARIOS"); //titulo
 
//inicio estilos
$titulo = new PHPExcel_Style(); //nuevo estilo
$titulo->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => true,
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

$subtitulo1 = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo1->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
      'size' => 12
    ),	
	'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
));

$subtitulo2 = new PHPExcel_Style(); //nuevo estilo
 
$subtitulo2->applyFromArray(
  array('font' => array( //fuente
      'arial' => true,
      'size' => 12,
	  'bold' => true,
    ),	
	'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
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
      'wrap' => true,
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
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 10
    ),'alignment' => array( //alineacion
      'wrap' => true,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
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
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 

//establecer titulos de impresion en cada hoja
//$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 6);
 
$fila=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", strtoupper($empresa_nombre));
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:K$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:K$fila");

$fila=2;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "REPORTE AGENDA DE USUARIOS");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:K$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:K$fila");

$fila=3;
$objPHPExcel->getActiveSheet()->SetCellValue("A$fila", "USUARIOS ".strtoupper($atencion_nombre)." ".strtoupper($mes)." $dia, $año Hasta ".strtoupper($mes1)." $dia1, $año1");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:K$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:K$fila");

$fila+=1;
$valor = 1;

if($result->num_rows>0){
	while($registro2 = $result->fetch_assoc()){
	   $fila+=1;
	   
	   if($registro2['puesto_id'] == 1){
		  $dato = "LIC";
	   }else if($registro2['puesto_id'] == 10){
		  $dato = "LIC";
	   }else{
		  $dato = utf8_decode("DR(A)");
	   }
		
       $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $dato.". ".$registro2['colaborador'].' ('.$registro2['servicio'].')');	
	   $objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo2, "A$fila:M$fila");
       $objPHPExcel->getActiveSheet()->mergeCells("A$fila:M$fila"); //unir celdas	   
	   
	   $colaborador_id = $registro2['colaborador_id'];
	   
	   $where = "WHERE c.colaborador_id = '$colaborador_id' AND CAST(a.fecha_cita AS DATE) BETWEEN '$fecha' AND '$fechaf'";		   
	   
	   $registro_agenda = "SELECT a.pacientes_id AS 'pacientes_id', c.puesto_id AS 'puesto_id', a.servicio_id AS 'servicio_id', 
	       a.expediente AS 'expediente', p.nombre AS 'nombre', p.apellido AS 'apellido', a.hora AS 'hora', DATE_FORMAT(CAST(a.fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha', 
		   a.observacion AS 'observacion',  a.color AS 'color', p.identidad AS 'identidad', p.telefono1 AS 'telefono1', p.telefono2 AS 'telefono2', a.comentario as 'comentario'
           FROM agenda AS a 
           INNER JOIN pacientes AS p 
           ON a.pacientes_id = p.pacientes_id 
           INNER JOIN colaboradores AS c 
           ON a.colaborador_id = c.colaborador_id
	       INNER JOIN puesto_colaboradores AS pc
	       ON c.puesto_id = pc.puesto_id  		   
           ".$where."
           ORDER BY a.fecha_cita, a.pacientes_id ASC";
	   $result_agenda = $mysqli->query($registro_agenda) or die($mysqli->error);
		   
	   if($result_agenda->num_rows>0){
		   $fila+=1;			   
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
           $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", 'Teléfono1');
           $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12); 
           $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", 'Teléfono2');
           $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12); 
           $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", 'Observación');
           $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
           $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", 'Comenario');
           $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);		   
           $objPHPExcel->getActiveSheet()->setSharedStyle($subtitulo, "A$fila:K$fila"); //establecer estilo
           $objPHPExcel->getActiveSheet()->getStyle("A$fila:K$fila")->getFont()->setBold(true); //negrita
		   
		   while($registro2 = $result_agenda->fetch_assoc()){			    			   
			   $fila+=1;
	           if ($registro2['expediente'] == 0){
		          $expediente = "TEMP"; 
	           }else{
		          $expediente = $registro2['expediente'];
	           }	   
                		
               $color = $registro2['color'];		
               $estatus = "";
			   
			   if($color == '#008000'){
				   $estatus = '(pv)';
			   }
			  	  	
	           $objPHPExcel->getActiveSheet()->SetCellValue("A$fila", $valor);
    		   
	           if( strlen($registro2['identidad'])<10 ){
		         $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$fila", 'No porta identidad', PHPExcel_Cell_DataType::TYPE_STRING);		   
	           }else{
		         $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$fila", $registro2['identidad'], PHPExcel_Cell_DataType::TYPE_STRING);
	           }
			   
               $objPHPExcel->getActiveSheet()->SetCellValue("C$fila", $expediente);
               $objPHPExcel->getActiveSheet()->SetCellValue("D$fila", $registro2['nombre']);
               $objPHPExcel->getActiveSheet()->SetCellValue("E$fila", $registro2['apellido']." ".$estatus);
               $objPHPExcel->getActiveSheet()->SetCellValue("F$fila", $registro2['fecha']);		   
               $objPHPExcel->getActiveSheet()->SetCellValue("G$fila", $registro2['hora']);
               $objPHPExcel->getActiveSheet()->SetCellValue("H$fila", $registro2['telefono1']);
               $objPHPExcel->getActiveSheet()->SetCellValue("I$fila", $registro2['telefono2']);
               $objPHPExcel->getActiveSheet()->SetCellValue("J$fila", $registro2['observacion']);
               $objPHPExcel->getActiveSheet()->SetCellValue("K$fila", $registro2['comentario']);		   
               $objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "A$fila:K$fila");
			   $valor++;
		   }
           $fila+=1;
           $valor = 1;		   
	   }  	   
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
header('Content-Disposition: attachment; filename="REPORTE AGENDA DIARIA DE USUARIOS '.strtoupper($mes).' '.$dia.', '.$año.'.xls"');
//**********************************************************************
 
//forzar a descarga por el navegador
$objWriter->save('php://output');

$result->free();//LIMPIAR RESULTADO
$result_agenda->free();//LIMPIAR RESULTADO
$result_profesional->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>