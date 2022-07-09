<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id-registro'];
$nombre = cleanStringStrtolower($_POST['puestosn']);
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER CORRELATIVO
$correlativo= "SELECT MAX(puesto_id) AS max, COUNT(puesto_id) AS count 
   FROM puesto_colaboradores";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
    $numero = $numero + 1;

//CONSULTAMOS QUE EL REGISTRO EXISTA
$consulta = "SELECT puesto_id 
      FROM puesto_colaboradores 
	  WHERE nombre = '$nombre'";
$result = $mysqli->query($consulta);	  
$consulta2 = $result->fetch_assoc();
$consulta_nombre = $consulta2['puesto_id'];
	
//VERIFICAMOS EL PROCESO

if($consulta_nombre == ""){
	$insert = "INSERT INTO puesto_colaboradores 
	   VALUES('$numero', '$nombre')";
	$query = $mysqli->query($insert);
	
	//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado_historial = "Agregar";
   $observacion_historial = "Se ha agregado un nuevo puesto: $nombre";
   $modulo = "Puestos";
   $insert = "INSERT INTO historial 
		VALUES('$historial_numero','0','0','$modulo','$numero','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	   
   /********************************************/			
	
	if($query){
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_puestos",
			5 => "Registro",
			6 => "Puestos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "registrar_puestos", //Modals Para Cierre Automatico
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
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos este registro ya existe no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);
}

echo json_encode($datos);
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>