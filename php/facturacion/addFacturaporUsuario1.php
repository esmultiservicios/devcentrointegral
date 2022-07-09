<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];
$pacientes_id = $_POST['pacientes_id'];
$fecha = $_POST['fecha'];
$colaborador_id = $_POST['colaborador_id'];
$empresa_id = $_SESSION['empresa_id'];
$servicio_id = $_POST['servicio_id'];
$notes = cleanStringStrtolower($_POST['notes']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$activo = 1;
$estado = 4;//ESTADO FACTURA CREDITO

if(isset($_POST['facturas_activo'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['facturas_activo'] == ""){
		$tipo_factura = 2;
		$tipo = "FacturacionCredito";
	}else{
		$tipo_factura = $_POST['facturas_activo'];
		$tipo = "Facturacion";
	}
}else{
	$tipo_factura = 2;
	$tipo = "FacturacionCredito";
}

$empresa_nombre = $_POST['empresa_nombre'];
$empresa_rtn = $_POST['empresa_rtn'];

//CONSULTAR DATOS DE LA SECUENCIA DE FACTURACION
$query_secuencia = "SELECT secuencia_facturacion_id, prefijo, siguiente AS 'numero', rango_final, fecha_limite, incremento, relleno
   FROM secuencia_facturacion
   WHERE activo = '$activo' AND empresa_id = '$empresa_id'";
$result = $mysqli->query($query_secuencia) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$secuencia_facturacion_id = "";
$prefijo = "";
$numero = "";
$rango_final = "";
$fecha_limite = "";
$incremento = "";
$no_factura = "";

if($result->num_rows>0){
	$secuencia_facturacion_id = $consulta2['secuencia_facturacion_id'];	
	$prefijo = $consulta2['prefijo'];
	$numero = $consulta2['numero'];
	$rango_final = $consulta2['rango_final'];
	$fecha_limite = $consulta2['fecha_limite'];	
	$incremento = $consulta2['incremento'];
	$no_factura = $consulta2['prefijo']."".str_pad($consulta2['numero'], $consulta2['relleno'], "0", STR_PAD_LEFT);		
}

//OBTENEMOS EL TAMAÑO DE LA TABLA
if(isset($_POST['productName'])){	
	if($_POST['productName'][0] != "" && $_POST['quantity'][0] && $_POST['price'][0]){
		$tamano_tabla = count( $_POST['productName']);
	}else{
		$tamano_tabla = 0;
	}
}else{
	$tamano_tabla = 0;
}

if($pacientes_id != "" && $colaborador_id != "" && $servicio_id != ""){
	if($tamano_tabla >0){
		//ACTUALIZAMOS LA FACTURA
		$update = "UPDATE facturas
			SET 
				number = '$numero',
				secuencia_facturacion_id = '$secuencia_facturacion_id',
				estado = '$estado',
				notas = '$notes',
				empresa_nombre = '$empresa_nombre',
				empresa_rtn = '$empresa_rtn'				
			WHERE facturas_id = '$facturas_id'";
		$query = $mysqli->query($update);

		if($query){	
			//ACTUALIZAMOS LA SECUENCIA DE FACTURACION AL NUMERO SIGUIENTE
			$numero += $incremento;
			$update = "UPDATE secuencia_facturacion 
				SET 
					siguiente = '$numero' 
				WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
			$mysqli->query($update);

			$total_valor = 0;
			$descuentos = 0;
			$isv_neto = 0;
			$total_despues_isv = 0;
			
			for ($i = 0; $i < count( $_POST['productName']); $i++){//INICIO DEL CICLO
				$facturas_detalle_id = correlativo("facturas_detalle_id","facturas_detalle");
				$productoID = $_POST['productoID'][$i];
				$productName = $_POST['productName'][$i];
				$quantity = $_POST['quantity'][$i];
				$price = $_POST['price'][$i];
				$discount = $_POST['discount'][$i];
				$total = $_POST['total'][$i];			
				$isv_valor = 0;
				
				if($productoID != "" && $productName != "" && $quantity != "" && $price != "" && $total != ""){					
					//OBTENER EL ISV
					$query_isv = "SELECT nombre
						FROM isv";
					$result_isv = $mysqli->query($query_isv) or die($mysqli->error);
					
					$porcentajeISV = 0;
					
					if($result_isv->num_rows>0){
						$consulta_isv_valor = $result_isv->fetch_assoc();
						$porcentajeISV = $consulta_isv_valor["nombre"];						
					}
				
					//CONSULTAMOS EL ISV ACTIVO EN EL PRODUCTO
					$query_isv_activo = "SELECT isv
						FROM productos
						WHERE productos_id  = '$productoID'";
					$result_productos_isv_activo = $mysqli->query($query_isv_activo) or die($mysqli->error);
					$aplica_isv = 0;
					
					if($result_productos_isv_activo->num_rows>0){
						$consulta_aplica_isv_productos = $result_productos_isv_activo->fetch_assoc();
						$aplica_isv = $consulta_aplica_isv_productos["isv"];						
					}

					$porcentaje_isv = 0;
					
					if($aplica_isv == 1){
						$porcentaje_isv = ($porcentajeISV / 100);
						$isv_valor = $price * $quantity * $porcentaje_isv;
					}					
					
					//VERIFICAMOS SI NO EXISTE LA FACTURA, DE NO EXISTIR LA ACTUALIZAMOS
					$query_factura_detalle = "SELECT facturas_id
						FROM facturas_detalle
						WHERE facturas_id = '$facturas_id' AND productos_id = '$productoID'";
					$result_factura_detalle = $mysqli->query($query_factura_detalle) or die($mysqli->error);	

					if($result_factura_detalle->num_rows>0){
						//ACTUALIZAMOS EL DETALLE DE LA FACTURA
						$update_factura_detalle = "UPDATE facturas_detalle
							SET 
								cantidad = '$quantity',
								precio = '$price',
								isv_valor = '$isv_valor',
								descuento = '$discount'
							WHERE facturas_id = '$facturas_id' AND productos_id = '$productoID'";
						$mysqli->query($update_factura_detalle);				
					}else{
						$facturas_detalle_id = correlativo("facturas_detalle_id","facturas_detalle");
						$insert_detalle = "INSERT INTO facturas_detalle 
							VALUES('$facturas_detalle_id','$facturas_id','$productoID','$quantity','$price','$isv_valor','$discount')";
						$mysqli->query($insert_detalle);
					}
					
					//CONSULTAMOS LA CATEGORIA DEL PRODUCTO
					$query_categoria = "SELECT cp.nombre AS 'categoria'
						FROM productos AS p
						INNER JOIN categoria_producto AS cp
						ON p.categoria_producto_id = cp.categoria_producto_id
						WHERE p.productos_id = '$productoID'
						GROUP BY p.productos_id";
					$result_categoria = $mysqli->query($query_categoria) or die($mysqli->error);
					
					$categoria_producto = "";
					
					if($result_categoria->num_rows>0){
						$consulta_categoria = $result_categoria->fetch_assoc();
						$categoria_producto = $consulta_categoria["categoria"];
						
						if($categoria_producto == "Producto"){
							//CONSULTAMOS LA CANTIDAD EN LA ENTIDAD PRODUCTOS
							$query_productos = "SELECT cantidad
								FROM productos
								WHERE productos_id = '$productoID'";
							$result_productos = $mysqli->query($query_productos) or die($mysqli->error);			  

							$cantidad_productos = "";
							
							if($result_productos->num_rows>0){
								$consulta = $result_productos->fetch_assoc();
								$cantidad_productos = $consulta['cantidad'];
							}		
							
							$cantidad = $cantidad_productos - $quantity;
							
							//ACTUALIZAMOS LA NUEVA CANTIDAD EN LA ENTIDAD PRODUCTOS
							$update_productos = "UPDATE productos
								SET
									cantidad = '$cantidad'
								WHERE productos_id = '$productoID'";
							$mysqli->query($update_productos);
							
							//CONSULTAMOS EL SALDO DEL PRODUCTO EN LA ENTIDAD MOVIMIENTOS
							$query_movimientos = "SELECT saldo
								FROM movimientos
								WHERE productos_id = '$productoID'
								ORDER BY movimientos_id DESC LIMIT 1";
							$result_movimientos = $mysqli->query($query_movimientos) or die($mysqli->error);
							
							$saldo_productos = 0;
							
							if($result_movimientos->num_rows>0){
								$consulta = $result_movimientos->fetch_assoc();
								$saldo_productos = $consulta['saldo'];
							}
							
							$saldo = $saldo_productos - $quantity;						
																					
							$cantidad_entrada = 0;
							$cantidad_salida = $quantity;
							$documento = "Factura ".$no_factura;
						
							 $movimientos_id = correlativo("movimientos_id","movimientos");
							 $insert_movimiento = "INSERT INTO movimientos 
								VALUES('$movimientos_id','$productoID','$documento','$cantidad_entrada','$cantidad_salida','$saldo','$fecha_registro')";
							 $mysqli->query($insert_movimiento);
						}
					}

					$total_valor += ($price * $quantity);
					$descuentos += $discount;
					$isv_neto += $isv_valor;
				}
			}//FIN DEL CICLO

			$total_despues_isv = ($total_valor + $isv_neto) - $descuentos;
			
			//ACTUALIZAMOS EL IMPORTE DE LA FACTURA
			$update = "UPDATE facturas
				SET
					importe = '$total_despues_isv'
				WHERE facturas_id = '$facturas_id'";
			$mysqli->query($update);
					
			$datos = array(
				0 => "Almacenado", 
				1 => "Registro Almacenado Correctamente", 
				2 => "success",
				3 => "btn-primary",
				4 => "formulario_facturacion",
				5 => "Registro",
				6 => $tipo,//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
				7 => "", //Modals Para Cierre Automatico
				8 => $facturas_id, //Modals Para Cierre Automatico		
			);
		}else{//NO SE PUEDO ALMACENAR ESTE REGISTRO
			$datos = array(
				0 => "Error", 
				1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);
		}	
	}else{
			$datos = array(
				0 => "Error", 
				1 => "Lo sentimos, debe agregar por lo menos una línea en los detalles de la factura", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",			
			);	
	}
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos, el Paciente, Profesional o Servicio no pueden quedar en blanco, por favor corregir", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);		
}

echo json_encode($datos);
?>