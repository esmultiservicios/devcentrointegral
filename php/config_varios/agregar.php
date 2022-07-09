<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$entidad = $_POST['consulta_registro'];
$nombre = $_POST['nombre_registro'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER CORRELATIVO
$correlativo = "SELECT MAX(".$entidad."_id) AS max, COUNT(".$entidad."_id) AS count 
    FROM ".$entidad;
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
    $numero = $numero + 1;

//CONSULTAR EXISTENCIA
$consulta = "SELECT ".$entidad."_id AS 'id' 
    FROM ".$entidad." 
    WHERE nombre = '$nombre'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();
$registro_id = $consulta2['id'];

if($registro_id == ""){
   $insert = "INSERT INTO ".$entidad." 
      VALUES ('$numero','$nombre')";
   $query = $mysqli->query($insert);

   if($query){
       //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
       $historial_numero = historial();
       $estado_historial = "Actualizar";
       $observacion_historial = "Se ha agregado el registro $nombre en la entidad $entidad con código $numero";
       $modulo = cleanStringStrtolower($entidad);
       $insert = "INSERT INTO historial 
          VALUES('$historial_numero','0','0','$modulo','$numero','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
       $mysqli->query($insert);	 
       /********************************************/
	$datos = array(
		0 => "Almacenado", 
		1 => "Registro Almacenado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formulario_registros",
		5 => "Registro",
		6 => "configuracionVarios",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "registrar", //Modals Para Cierre Automatico
		8 => "", //Modals Para Cierre Automatico		
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
		1 => "Este Registro ya existe, no se puede almacenar", 
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