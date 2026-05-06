<?php
//addPagoCheque.php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['factura_id_cheque'];
$fecha = date("Y-m-d");
$fecha_registro = date("Y-m-d H:i:s");
$importe = $_POST['monto_efectivo'];
$cambio = 0;

$empresa_id = $_SESSION['empresa_id'];	
$usuario = $_SESSION['colaborador_id'];			

$tipo_pago_id = 3; // CHEQUE		
$banco_id = $_POST['bk_nm_chk'];
$tipo_pago = 1; // 1. CONTADO 2. CRÉDITO	
$estado_pago = 1; // ACTIVO
$estado = 2; // FACTURA PAGADA

$efectivo = 0;
$tarjeta = $importe;			

$referencia_pago1 = cleanStringConverterCase($_POST['check_num']); // NUMERO DE CHEQUE
$referencia_pago2 = "";
$referencia_pago3 = "";

/*
   IMPORTANTE:
   Este archivo YA NO consulta secuencia_facturacion.
   Este archivo YA NO actualiza facturas.number.
   Este archivo YA NO mueve secuencia_facturacion.siguiente.

   El número fiscal debe generarse únicamente al crear/generar la factura.
   El pago con cheque solo registra el pago y marca la factura como pagada.
*/

// VERIFICAMOS QUE NO SE HA INGRESADO EL PAGO
$query_factura = "SELECT pagos_id
	FROM pagos
	WHERE facturas_id = '$facturas_id'";
$result_factura = $mysqli->query($query_factura) or die($mysqli->error);	

// SI NO SE HA INGRESADO, ALMACENAMOS EL PAGO
if ($result_factura->num_rows == 0) {

	$pagos_id  = correlativo('pagos_id', 'pagos');

	$insert = "INSERT INTO pagos 
		VALUES (
			'$pagos_id',
			'$facturas_id',
			'$tipo_pago',
			'$fecha',
			'$importe',
			'$efectivo',
			'$cambio',
			'$tarjeta',
			'$usuario',
			'$estado_pago',
			'$empresa_id',
			'$fecha_registro'
		)";
	$query = $mysqli->query($insert);	

	if ($query) {

		// ACTUALIZAMOS LOS DETALLES DEL PAGO
		$pagos_detalles_id  = correlativo('pagos_detalles_id', 'pagos_detalles');

		$insert = "INSERT INTO pagos_detalles 
			VALUES (
				'$pagos_detalles_id',
				'$pagos_id',
				'$tipo_pago_id',
				'$banco_id',
				'$importe',
				'$referencia_pago1',
				'$referencia_pago2',
				'$referencia_pago3'
			)";
		$query = $mysqli->query($insert);

		if ($query) {

			// CONSULTAMOS EL TIPO DE FACTURA
			$query_tipo_factura = "SELECT tipo_factura
				FROM facturas
				WHERE facturas_id = '$facturas_id'";
			$resultTipoFactura = $mysqli->query($query_tipo_factura) or die($mysqli->error);
			$consulta2TipoFactura = $resultTipoFactura->fetch_assoc();

			$tipo_factura = "";

			if ($resultTipoFactura->num_rows > 0) {
				$tipo_factura = $consulta2TipoFactura['tipo_factura'];		
			}	

			// ACTUALIZAMOS SOLO EL ESTADO DE LA FACTURA
			$update_factura = "UPDATE facturas
				SET estado = '$estado'
				WHERE facturas_id = '$facturas_id'";
			$mysqli->query($update_factura) or die($mysqli->error);	

			$datos = array(
				0 => "Guardar", 
				1 => "Pago Realizado Correctamente", 
				2 => "info",
				3 => "btn-primary",
				4 => "formEfectivoBill",
				5 => "Registro",
				6 => "Pagos",
				7 => "modal_pagos",
				8 => $facturas_id,
				9 => "Guardar",
			);		

		} else {

			$datos = array(
				0 => "Error", 
				1 => "No se pudo almacenar el detalle del pago, por favor corregir.", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
		}

	} else {

		$datos = array(
			0 => "Error", 
			1 => "No se pudo almacenar este registro, los datos son incorrectos por favor corregir.", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}	

} else {

	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos, no se puede almacenar el pago. Por favor valide si ya existe un pago para esta factura.", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);
}

echo json_encode($datos);