<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
 
$facturas_id = $_POST['facturas_id'];

//CONSULTAR DATOS DEL METODO DE PAGO
$query = "SELECT f.facturas_id AS facturas_id, DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', p.pacientes_id AS 'pacientes_id', CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', f.colaborador_id AS 'colaborador_id', f.estado AS 'estado', s.nombre AS 'consultorio', f.servicio_id AS 'servicio_id', f.fecha AS 'fecha_factura', f.notas AS 'notas', f.empresa_nombre AS 'empresa_nombre', f.empresa_rtn AS 'empresa_rtn'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	WHERE facturas_id = '$facturas_id'";
$result = $mysqli->query($query) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();   
     
$pacientes_id = "";
$paciente = "";
$profesional = "";
$colaborador_id = "";
$servicio_id = "";
$fecha_factura = "";
$notas = "";
$empresa_nombre = "";
$empresa_rtn = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$pacientes_id = $consulta_registro['pacientes_id'];
	$paciente = $consulta_registro['paciente'];
	$profesional = $consulta_registro['profesional'];
	$colaborador_id = $consulta_registro['colaborador_id'];	
	$servicio_id = $consulta_registro['servicio_id'];
	$fecha_factura = $consulta_registro['fecha_factura'];
	$notas = $consulta_registro['notas'];
	$empresa_nombre = $consulta_registro['empresa_nombre'];
	$empresa_rtn = $consulta_registro['empresa_rtn'];	
}
	
$datos = array(
	 0 => $pacientes_id, 
	 1 => $paciente, 
	 2 => $fecha_factura,
	 3 => $colaborador_id,  	 
	 4 => $profesional, 	 
	 5 => $servicio_id, 
	 6 => $notas, 	
	 7 => $empresa_nombre, 
	 8 => $empresa_rtn, 		  
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>