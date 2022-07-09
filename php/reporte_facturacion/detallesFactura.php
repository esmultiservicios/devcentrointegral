<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$fecha_consulta = date('Y-m-d');
$facturas_id = $_POST['facturas_id'];
$mensaje = "";
$error;

//INICIO DATOS DEL USUARIO
$query = "SELECT CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.identidad AS 'identidad', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', s.nombre AS 'servicio', DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha', CONCAT(c1.nombre,' ',c1.apellido) AS 'usuario'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN pagos AS pa
    ON f.facturas_id = pa.facturas_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
	ON f.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c1
	ON f.usuario = c1.colaborador_id
	WHERE f.facturas_id = '$facturas_id'";
$result_ = $mysqli->query($query) or die($mysqli->error);
$consulta_datos = $result_->fetch_assoc(); 

$mensaje = '';
$error = '';
$paciente = '';
$identidad = '';
$profesional = '';
$servicio = '';
$fecha = '';
$usuario = '';

if($result_->num_rows>0){
	$paciente = $consulta_datos['paciente'];
	$identidad = $consulta_datos['identidad'];
	$profesional = $consulta_datos['profesional'];
	$servicio = $consulta_datos['servicio'];
	$fecha = $consulta_datos['fecha'];
	$usuario = $consulta_datos['usuario'];

	$mensaje = "
		<div class='form-row'>
			<div class='col-md-12 mb-3'>
				<p style='color: #483D8B;' align='center'><b>Detalles Facturación</b></p>
			</div>				
		</div>
		<div class='form-row'>
			<div class='col-md-6 mb-3'>
				<p><b>Paciente:</b> $paciente</p>
			</div>	
			<div class='col-md-6 mb-3'>
				<p><b>Identidad:</b> $identidad</p>
			</div>					
		</div>
		<div class='form-row'>
			<div class='col-md-6 mb-3'>
				<p><b>Profesional:</b> $profesional</p>
			</div>	
			<div class='col-md-6 mb-3'>
				<p><b>Servicio:</b> $servicio</p>			
			</div>				
		</div>		
		<div class='form-row'>
			<div class='col-md-6 mb-3'>
				<p><b>Fecha:</b> $fecha</p>
			</div>	
			<div class='col-md-6 mb-3'>
				<p><b>Usuario:</b> $usuario</p>			
			</div>					
		</div>	
	";

    $mensaje = $mensaje;	
}else{
	$error = "
		<div class='form-row'>
			<div class='col-md-6 mb-3'>
				<p style='color: #FF0000;' align='center'><b>No hay datos que mostrar</b></p>
			</div>						
		</div> 
	";	
	$mensaje = $error;	
}

$query_detalle_paagos = "SELECT tp.nombre AS 'tipo_pago', b.nombre AS 'banco', SUM(pd.efectivo) AS 'neto'
	FROM facturas AS f
	INNER JOIN pagos AS pa
	ON f.facturas_id = pa.facturas_id
	INNER JOIN pagos_detalles AS pd
	ON pa.pagos_id  = pd.pagos_id 
	INNER JOIN tipo_pago AS tp
	ON pd.tipo_pago_id = tp.tipo_pago_id
    LEFT JOIN banco AS b
	ON pd.banco_id = b.banco_id
	WHERE f.facturas_id = '$facturas_id'";
$result_detalle_pagos = $mysqli->query($query_detalle_paagos) or die($mysqli->error);

$tipo_pago = '';
$banco = '';
$efectivo = '';
$total = 0;

if($result_detalle_pagos->num_rows>0){
	while($consulta_detalles_pagos = $result_detalle_pagos->fetch_assoc()){
		$tipo_pago = $consulta_detalles_pagos['tipo_pago'];
		$banco = $consulta_detalles_pagos['banco'];
		$efectivo = $consulta_detalles_pagos['neto'];
		$total += $consulta_detalles_pagos['neto'];	
		
		$mensaje .= "
			<div class='form-row'>
				<div class='col-md-12 mb-3'>
					<p style='color: #483D8B;' align='center'><b>Detalles de Pago</b></p>
				</div>				
			</div>
			<div class='form-row'>
				<div class='col-md-4 mb-3'>
					<p><b>Tipo Pago:</b> $tipo_pago</p>
				</div>	
				<div class='col-md-4 mb-3'>
					<p><b>Banco:</b> $banco</p>
				</div>
				<div class='col-md-4 mb-3'>
					<p><b>Efectivo:</b> L. ".number_format($efectivo,2)."</p>
				</div>				
			</div>
		";			
	}

	$mensaje .= "
		<div class='form-row'>
			<div class='col-md-4 mb-3'>
				<p><b>Neto:</b> L. ".number_format($total,2)."</p>
			</div>				
		</div>
	";		
}
//FIN DATOS DEL USUARIO

echo $mensaje;

$mysqli->close();//CERRAR CONEXIÓN
?>