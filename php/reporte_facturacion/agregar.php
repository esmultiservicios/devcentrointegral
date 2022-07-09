<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$usuario = $_SESSION['colaborador_id'];
$fecha_b = $_POST['fechai'];
$fecha_f = $_POST['fechaf'];
$colaborador_id = $_POST['colaborador_id'];
$profesional = $_POST['profesional'];
$estado = 2; //1. Borrador 2. Pagada 3. Cancelado
$estado_cargos = 1; //1. Pendiente 2. Pagada
$cierre = 2; //1. Sí 2. No
$cierre_nuevo = 1; //1. Sí 2. No
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$comentario = cleanStringStrtolower($_POST['comentario']);

$query = "SELECT f.colaborador_id AS 'colaborador_id', CONCAT(c.nombre,' ',c.apellido) AS 'profesional', tp.nombre, SUM(f.monto) AS 'monto', SUM(f.descuento) AS 'descuento', SUM(f.neto) AS 'neto', tp.tipo_pago_id AS 'tipo_pago_id', f.servicio_id AS 'servicio_id'
	FROM factura AS f
	INNER JOIN metodo_pago As mp
	ON f.metodo_pago_id = mp.metodo_pago_id
	INNER JOIN tipo_pago AS tp
	ON mp.tipo_pago_id = tp.tipo_pago_id
	INNER JOIN colaboradores AS c
	ON f.colaborador_id = c.colaborador_id
	WHERE f.colaborador_id = '$colaborador_id' AND f.estado = '$estado' AND f.cierre = '$cierre' AND f.fecha BETWEEN '$fecha_b' AND '$fecha_f'
	GROUP BY mp.tipo_pago_id
	ORDER BY f.colaborador_id";
$result = $mysqli->query($query) or die($mysqli->error);

$flag = 2;

//CONSULTAMOS SI HAY CARGOS ALMACENADOS PARA ESA FECHA
$query_cargos = "SELECT cargo_id
   FROM cargos
   WHERE colaborador_id = '$colaborador_id' AND fecha_i = '$fecha_b' AND fecha_f = '$fecha_f'";
$result_cargos = $mysqli->query($query_cargos) or die($mysqli->error);

if($result_cargos->num_rows==0){
	while($registro2 = $result->fetch_assoc()){
		$servicio_id = $registro2['servicio_id'];
		$tipo_pago_id = $registro2['tipo_pago_id'];
		$monto = $registro2['monto'];
		$descuento = $registro2['descuento'];
		$neto = $registro2['neto'];	
		
		//GUARDAMOS LOS DATOS DEL REGISTRO
		$cargo_id = correlativo('cargo_id', 'cargos');
		$insert = "INSERT INTO cargos VALUES('$cargo_id','$colaborador_id','$servicio_id','$fecha_b','$fecha_f','$tipo_pago_id','$monto','$descuento','$neto','$usuario','$estado_cargos','$fecha_registro')";
		$query = $mysqli->query($insert) or die($mysqli->error);
		
		if($query){		
			//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
			$historial_numero = historial();
			$estado_historial = "Agregar";
			$observacion_historial = "Se ha calculado los cargos para el profesional";
			$modulo = "Cargos";
			$insert = "INSERT INTO historial 
			   VALUES('$historial_numero','0','0','$modulo','$cargo_id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
			$mysqli->query($insert) or die($mysqli->error);
			/********************************************/			
		} 
		$flag = 1;
	}

	if($flag == 1){
		echo 1;//REGISTRO GENERADO CORRECTAMENTE
		//ACTUALIZAMOS LOS VALORES DE LA FACTURA PARA CAMBIAR EL CAMPO CIERRE
		$query_factura = "SELECT factura_id
		   FROM factura
		   WHERE fecha BETWEEN '$fecha_b' AND '$fecha_f' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND estado = '$estado' AND cierre = '$cierre'";
		$result_factura = $mysqli->query($query_factura) or die($mysqli->error);
		
		   while($registrofactura = $result_factura->fetch_assoc()){
			   $factura_id = $registrofactura['factura_id'];
			   
			   //ACTUALIZAMOS EL CIERRE DE LA FACTURA
			   $update_factura = "UPDATE factura SET cierre = '$cierre_nuevo' WHERE factura_id = '$factura_id'";
			   $mysqli->query($update_factura) or die($mysqli->error);
		   }
	}else{
		echo 2;//ERROR AL GENERAR EL REGISTRO
	}
}else{
	echo 3;//ESTE REGISTRO YA EXISTE NO SE PUEDE ALMACENAR NUEVAMENTE
}

$mysqli->close();//CERRAR CONEXIÓN
?>