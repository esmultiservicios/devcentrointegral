<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$proceso = $_POST['pro'];
$id = $_POST['id-registro'];
$expediente = $_POST['expediente'];
$pa = $_POST['pa'];
$fr = $_POST['fr'];
$fc = $_POST['fc'];
$temperatura = $_POST['temperatura'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];
$observaciones = cleanStringStrtolower($_POST['observaciones']);
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];

//OBTENER PACIENTE_ID
$query_paciente = "SELECT pacientes_id, CONCAT(apellido,' ',nombre) AS 'paciente'
   FROM pacientes
   WHERE expediente = '$expediente'";
$result = $mysqli->query($query_paciente);
$consulta_paciente = $result->fetch_assoc();
$pacientes_id = $consulta_paciente['pacientes_id'];  
$nombre_paciente = $consulta_paciente['paciente']; 

//OBTENER DATOS DE LA PRECLINICA
$consulta_post = "SELECT colaborador_id, servicio_id, fecha
   FROM preclinica
   WHERE preclinica_id = '$id'";
$result = $mysqli->query($consulta_post);
$consulta_paciente = $result->fetch_assoc();
$colaborador_id = $consulta_paciente['colaborador_id'];  
$servicio_id = $consulta_paciente['servicio_id'];  
$fecha = $consulta_paciente['fecha'];

//VERIFICAMOS EL PROCESO
if ($proceso = 'Edicion'){
	$update = "UPDATE preclinica 
		SET 
			pa = '$pa', 
			fr = '$fr', 
			fc = '$fc', 
			t = '$temperatura', 
			peso = '$peso', 
			talla = '$talla', 
			observacion = 'observaciones' 
	WHERE preclinica_id = '$id'";
	$data = $mysqli->query($update);
	
    //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
    $historial_numero = historial();
    $estado_historial = "Actualizar";
    $observacion_historial = "Se ha actualizado la preclínica para este usuario: $nombre_paciente con expediente n° $expediente";
    $modulo = "Preclinica";
    $insert = "INSERT INTO historial 
         VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$id','$colaborador_id','$servicio_id','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	 
    $mysqli->query($insert);
    /*****************************************************/		
	
	if($data){
		$datos = array(
			0 => "Modificado", 
			1 => "Registro Modificado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_agregar_preclinica",
			5 => "Registro",
			6 => "ReporteEnfermeria",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "agregar_preclinica", //Modals Para Cierre Automatico
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
}

echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>