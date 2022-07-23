<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$facturas_id = $_POST['facturas_id'];
$pacientes_id = $_POST['pacientes_id'];
$fecha = $_POST['fecha'];
$colaborador_id = $_POST['colaborador_id'];
$servicio_id = $_POST['servicio_id'];
$notes = cleanStringStrtolower($_POST['notes']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$activo = 1;
$numero = 0;
$empresa_id = $_SESSION['empresa_id'];

if(isset($_POST['aseguradora_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['aseguradora_id'] == ""){
		$aseguradora_id = 0;
	}else{
		$aseguradora_id = $_POST['aseguradora_id'];
	}
}else{
	$aseguradora_id = 0;
}

if(isset($_POST['fact_empresas_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['fact_empresas_id'] == ""){
		$fact_empresas_id = 0;
	}else{
		$fact_empresas_id = $_POST['fact_empresas_id'];
	}
}else{
	$fact_empresas_id = 0;
}

//CONSULTAR DATOS DE LA SECUENCIA DE FACTURACION
$query_secuencia = "SELECT secuencia_facturacion_id, prefijo, siguiente AS 'numero', rango_final, fecha_limite, incremento, relleno
   FROM secuencia_facturacion
   WHERE activo = '$activo' AND empresa_id = '$empresa_id'";
$result = $mysqli->query($query_secuencia) or die($mysqli->error);
$consulta2 = $result->fetch_assoc();

$secuencia_facturacion_id = "";

if($result->num_rows>0){
	$secuencia_facturacion_id = $consulta2['secuencia_facturacion_id'];	
}

$cierre = 2;
$importe = 0;

if(isset($_POST['facturas_activo'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['facturas_activo'] == ""){
		$tipo_factura = 2;
		$tipo = "FacturacionCredito";
		$estado = 4; 
	}else{
		$tipo_factura = $_POST['facturas_activo'];
		$tipo = "Facturacion1";

		if($tipo_factura == 1){
			$estado = 1;
		}else{
			$estado = 4;
		}
	}
}else{
	$tipo_factura = 2;
	$tipo = "FacturacionCredito";
	$estado = 4;
}

$empresa_id = $_SESSION['empresa_id'];

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
		//INSERTAMOS LOS DATOS EN LA ENTIDAD FACTURA
		$facturas_id = correlativo("facturas_id","facturas");
		$insert = "INSERT INTO facturas 
			VALUES('$facturas_id','$secuencia_facturacion_id','$numero','$tipo_factura','$pacientes_id','$colaborador_id','$servicio_id','$importe','$notes','$fecha','$estado','$cierre','$fact_empresas_id','$usuario','$empresa_id','$fecha_registro','$aseguradora_id')";

		$query = $mysqli->query($insert);

		if($query){	
			//ALMACENAMOS EL DETALLE DE LA FACTURA EN LA ENTIDAD FACTURAS DETALLE
			
			$total_valor = 0;
			$descuentos = 0;
			$isv_neto = 0;
			$total_despues_isv = 0;
			$isv_valor = 0;
			
			for ($i = 0; $i < count( $_POST['productName']); $i++){//INICIO DEL CICLO
				$discount = 0;
				$isv_valor = 0;					
				$facturas_detalle_id = correlativo("facturas_detalle_id","facturas_detalle");
				$productoID = $_POST['productoID'][$i];
				$productName = $_POST['productName'][$i];
				$quantity = $_POST['quantity'][$i];
				$price = $_POST['price'][$i];

				if($_POST['discount'][$i] != "" || $_POST['discount'][$i] != null){
					$discount = $_POST['discount'][$i];
				}	
									
				$total = $_POST['total'][$i];

				if($_POST['valor_isv'][$i] != "" || $_POST['valor_isv'][$i] != null){
					$isv_valor = $_POST['valor_isv'][$i];
				}					
					
				if($productoID != "" && $productName != "" && $quantity != "" && $price != "" && $discount != "" && $total != ""){
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
						
					$insert_detalle = "INSERT INTO facturas_detalle 
						VALUES('$facturas_detalle_id','$facturas_id','$productoID','$quantity','$price','$isv_valor','$discount')";
					$mysqli->query($insert_detalle);	
					
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
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir, verifique si hay registros en blanco antes de enviar los datos de la factura, se le recuerda que el detalle de la factura no puede quedar vacío", 
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