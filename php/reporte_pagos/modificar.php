<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$pagos_id = $_POST['pagos_id'];
$tipo_pago_reporte = $_POST['tipo_pago_reporte'];
$paciente_reporte_efectivo = $_POST['paciente_reporte_efectivo'];
$factura_reporte_tarjeta = $_POST['factura_reporte_tarjeta'];
	
$update = "UPDATE pagos 
   SET 
   	tipo_pago = '$tipo_pago_reporte',
	efectivo = '$paciente_reporte_efectivo',
	tarjeta = '$factura_reporte_tarjeta'	
   WHERE pagos_id = '$pagos_id'";		
$query = $mysqli->query($update);

if($query){
	//CONSULTAMOS LOS DETALLES DEL PAGO
	$consulta = "SELECT pagos_detalles_id
		FROM pagos_detalles
		WHERE pagos_id = '$pagos_id'";
	$result = $mysqli->query($consulta) or die($mysqli->error);			

	//RECORREMOS LOS VALORES Y DE ENCONTRAR ACTUALILZAMOS EL TIPO DE PAGO EN ESA SECCIÓN
	while($consulta2 = $result->fetch_assoc()){
		$pagos_detalles_id = $consulta2['pagos_detalles_id'];

		$update = "UPDATE pagos_detalles
			SET
				tipo_pago_id = '$tipo_pago_reporte'
			WHERE pagos_id = '$pagos_id'";
		$mysqli->query($update);
	}

	$datos = array(
		0 => "Modificado", 
		1 => "Registro Modificado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formulario_reporte_pagos",
		5 => "Registro",
		6 => "ReportePagos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "modal_editar_pagos", //Modals Para Cierre Automatico
	);
}else{
	$datos = array(
		0 => "Error", 
		1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);
}

echo json_encode($datos);
$mysqli->close();//CERRAR CONEXIÓN
?>