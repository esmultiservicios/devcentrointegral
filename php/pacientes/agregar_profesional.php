 <?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$profesional = cleanStringStrtolower($_POST['profesionales_buscar']);
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];
$fecha = date('Y-m-d');

//OBTENER CORRELATIVO PACIENTES
$correlativo= "SELECT DISTINCT MAX(profesion_id) AS max, COUNT(profesion_id) AS count 
    FROM profesion";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
    $numero = $numero + 1;	

$consultar = "SELECT profesion_id 
     FROM profesion 
	 WHERE nombre = '$profesional'";
$result = $mysqli->query($consultar);
$consultar2 = $result->fetch_assoc();
$profesional_id = $consultar2['profesion_id'];

if($profesional != ""){
  if($profesional_id == ""){
	 $insert = "INSERT INTO profesion VALUES('$numero', '$profesional', '$fecha','$usuario')";
	 $query = $mysqli->query($insert);
	 if($query){
          //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		  $historial_numero = historial();
		  $estado = "Agregar";
		  $observacion_historial = "Se ha agregado una nueva profesión: $profesional";
		  $modulo_historial = "Profesional";
		  $insert = "INSERT INTO historial 
		      VALUES('$historial_numero','$numero','0','$modulo_historial','0','0','0','$fecha','$estado','$observacion_historial','$usuario','$fecha_registro')";
		  $mysqli->query($insert);
		  /*****************************************************/
		
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_profesiones",
			5 => "Registro",
			6 => "formProfesionales",
			7 => "modal_profesiones",
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
}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro,  hay registros en blanco, por favor corregir", 
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