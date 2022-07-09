<?php
session_start();   
include "../funtions.php";

set_include_path('../../fpdf/font');
require('../../fpdf/fpdf.php');  

//CONEXION A DB
$mysqli = connect_mysqli();

header("Content-Type: text/html;charset=utf-8");

$pdf = new FPDF('P','mm',array(80,170));
#Establecemos los márgenes izquierda, arriba y derecha: 
$pdf->SetMargins(6, 0.3 , 65); 

#Establecemos el margen inferior: 
$pdf->SetAutoPageBreak(true,0.5);
$pdf->AddPage();
$pdf->Image('../../img/logo_icono.png' , 26,2, 20, 12,'PNG'); //float x , float y , float w , float h

$pdf->Ln(12);

//CONSULTA
$agenda_id = $_GET['agenda_id'];

//EVALUA EL CONTENIDO DE LA VARIABLE A BUSCAR
//CONSULTA DATOS DE LA AGENDA
$consulta_agenda = "SELECT usuario, DATE_FORMAT(CAST(fecha_cita AS DATE), '%d/%m/%Y') AS 'fecha_cita', CAST(fecha_cita AS DATE) AS 'fecha1', hora, DATE_FORMAT(fecha_registro, '%d/%m/%Y %h:%i:%s %p') AS 'fecha_registro', pacientes_id, colaborador_id, expediente, servicio_id, reprogramo 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";	
$result = $mysqli->query($consulta_agenda);
$consulta_agenda2 = $result->fetch_assoc();

$pacientes_id = "";
$colaborador_id  = "";
$expediente  = "";
$servicio_id  = "";
$usuario_sistema = "";
$fecha_registro = "";
$reprogramo = "";
$reprogramo_cita = "";
$fecha_cita = "";
$hora_cita = "";

if($result->num_rows>0){
	$pacientes_id = $consulta_agenda2['pacientes_id'];
	$colaborador_id  = $consulta_agenda2['colaborador_id'];
	$expediente  = $consulta_agenda2['expediente'];
	$servicio_id  = $consulta_agenda2['servicio_id'];
	$usuario_sistema = $consulta_agenda2['usuario'];
	$fecha_registro = $consulta_agenda2['fecha_registro'];
	$reprogramo = $consulta_agenda2['reprogramo'];
    $fecha_cita = $consulta_agenda2['fecha_cita'];	
	$hora_cita = $consulta_agenda2['hora'];
}

if($reprogramo == 1){
	$reprogramo_cita = "(Reprogramación)";
}else{
	$reprogramo_cita = "";
}

if ($expediente == 0){
	$exp = "TEMP"; 
}else{
	$exp = $expediente;
}	

//OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];	

$query_empresa = "SELECT e.telefono AS 'telefono', e.celular AS 'celular', e.correo AS 'correo', e.eslogan AS 'eslogan', e.horario AS 'horario'
FROM users AS u
INNER JOIN empresa AS e
ON u.empresa_id = e.empresa_id
WHERE u.colaborador_id = '$usuario'";
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);;
$consulta_empresa = $result_empresa->fetch_assoc();

$telefono = '';
$celular = '';
$telefono = '';
$horario = '';
$eslogan = '';
$correo_empresa = 'admision@mentesanahn.com';

if($result_empresa->num_rows>0){
   $telefono = $consulta_empresa['telefono'];
   $celular = $consulta_empresa['celular'];
   $correo = $consulta_empresa['correo'];   
   $horario = $consulta_empresa['horario'];
   $eslogan = $consulta_empresa['eslogan'];   
}  

//CONSULTA DATOS DEL USUARIO
$consulta_usuario = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre', identidad 
    FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_usuario);
$consulta_usuario2 = $result->fetch_assoc();

$nombre_usuario = "";
$identidad_usuario = "";
	
if($result->num_rows>0){
	$nombre_usuario = $consulta_usuario2['nombre'];
	$identidad_usuario = $consulta_usuario2['identidad'];
}
//CONSULTA DATOS DEL MEDICO
$consulta_medico = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre', puesto_id 
   FROM colaboradores 
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_medico);
$consulta_medico2 = $result->fetch_assoc();

$puesto_id  = "";
$nombre_medico = "";

if($result->num_rows>0){
	$puesto_id  = $consulta_medico2['puesto_id'];
	$nombre_medico = $consulta_medico2['nombre'];
}
//CONSULTAR TIPO MEDICO
$consulta_tipo_medico = "SELECT nombre, puesto_id 
    FROM puesto_colaboradores 
	WHERE puesto_id = '$puesto_id'";
$result = $mysqli->query($consulta_tipo_medico);
$consulta_tipo_medico2 = $result->fetch_assoc();
$puesto  = cleanStringStrtolower($consulta_tipo_medico2['nombre']);

$consultar_colaborador = "";

if($result->num_rows>0){
	$consultar_colaborador = $consulta_tipo_medico2['puesto_id'];
}
//CONSULTAR SERVICIO
$consulta_servicio = "SELECT nombre 
    FROM servicios 
	WHERE servicio_id = '$servicio_id'";
$result = $mysqli->query($consulta_servicio);
$consulta_servicio2 = $result->fetch_assoc();
$servicio  = "";

if($result->num_rows>0){
	$servicio  = trim(ucwords(strtolower($consulta_servicio2['nombre']), " "));
}
//CONSULTAR NOMBRE DE USUARIO DEL SISTEMA
$consulta_usuario_sistema = "SELECT CONCAT(nombre,' ',apellido) AS 'nombre' 
     FROM colaboradores 
	 WHERE colaborador_id = '$usuario_sistema'";
$result = $mysqli->query($consulta_usuario_sistema);
$consulta_usuario_sistema2 = $result->fetch_assoc();	
$usuario_sistema_nombre  = "";

if($result->num_rows>0){
	$usuario_sistema_nombre  = trim(ucwords(strtolower($consulta_usuario_sistema2['nombre']), " "));
}
//CONOCER EL TIPO DE USUARIO
$consultar_expediente = "SELECT a.agenda_id AS 'agenda_id'
    FROM agenda AS a
    INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
    WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio_id' AND c.puesto_id = '$consultar_colaborador' AND a.status = 1";
$result = $mysqli->query($consultar_expediente);	
$consultar_expediente1 = $result->fetch_assoc();

$usuario = "";  

if($result->num_rows>0)
	$usuario = 'Subsiguiente'; 
else
	$usuario = 'Nuevo';

$hora = date('g:i a',strtotime($hora_cita));	

//ENCABEZADO DEL CONTENIDO DEL REPORTE
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(8, 3, utf8_decode('Cita N°:').' '.$agenda_id.'', 0);
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(8, 8, 'Fecha Cita: '.$fecha_cita.' Hora: '.$hora, 0);
$pdf->Ln(1);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(8, 14, 'Tipo de Cita: '. $usuario.' '.utf8_decode($reprogramo_cita), 0);
$pdf->Ln(1);
$pdf->Cell(8, 20, 'Nombre: '.utf8_decode($nombre_usuario), 0);
$pdf->Ln(1);
$pdf->Cell(8, 26, 'Identidad: '.$identidad_usuario.'  Exp: '.$exp, 0);
$pdf->Ln(1);
$pdf->Cell(8, 32, utf8_decode('Profesional:').' '.utf8_decode($nombre_medico), 0);
$pdf->Ln(1);
$pdf->Cell(8, 37, 'Servicio: '.utf8_decode($servicio), 0);
$pdf->Ln(1);
$pdf->Cell(8, 43, 'Especialidad: '.utf8_decode($puesto), 0);
$pdf->Ln(1);
$pdf->Cell(8, 49, 'Usuario: '.utf8_decode($usuario_sistema_nombre), 0);

//LLENA EL CUEROP DEL REPORTE			  
$pdf->Ln(7);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(8, 45, utf8_decode('Nota:'), 0);
$pdf->Ln(3);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(8,47,utf8_decode("Por favor estar 15 minutos antes de su cita"), 0);
$pdf->Ln(3);
$pdf->Cell(8,49,utf8_decode("Tomando las medidas de bioseguridad"), 0);
$pdf->Ln(3);
$pdf->Cell(8,52,utf8_decode($eslogan), 0);
$pdf->Ln(3);

$pdf->SetFont('helvetica', '', 8);
$pdf->Ln(3);
$pdf->Cell(8,97,utf8_decode("__________________________"), 0);
$pdf->Ln(2);
$pdf->Cell(8,99,utf8_decode("Firma y Sello"), 0);
$pdf->Ln(2);
$pdf->Cell(8,101,utf8_decode("Nos puede llamar al siguiente número"), 0);
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(8,104,utf8_decode  ("PBX: ".$telefono), 0);

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(8,106,'Fecha Registro: '.$fecha_registro, 0);

$pdf->Output('Citas.pdf','I');

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>