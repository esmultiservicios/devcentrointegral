<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$colaborador_id = $_POST['colaborador_id'];
$jornada_id = $_POST['jornada_id'];
$cantidad_nuevos = $_POST['cantidad_nuevos'];
$cantidad_subsiguientes = $_POST['cantidad_subsiguientes'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENER NOMBRE DE COLABORADOR
$query_colaborador = "SELECT CONCAT(nombre,' ',apellido) AS 'colaborador'
  FROM colaboradores
  WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($query_colaborador);
$cosulta_colaborador = $result->fetch_assoc();
$colaborador_nombre = $cosulta_colaborador['colaborador'];  

//OBTENER NOMBRE DE JORNADA
$query_jornada = "SELECT nombre
   FROM jornada
   WHERE jornada_id = '$jornada_id'";
$result = $mysqli->query($query_jornada) or die($mysqli->error);   
$consulta_jornada = $result->fetch_assoc();
$jornada_nombre = $consulta_jornada['nombre']; 
   
//OBTENER CORRELATIVO
$numero = correlativo('id ', 'jornada_colaboradores');

//CONSULTAMOS QUE EL REGISTRO EXISTA
$consulta = "SELECT id 
      FROM jornada_colaboradores 
	  WHERE j_colaborador_id = '$jornada_id' AND colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta);	  
$consulta2 = $result->fetch_assoc();
$servicios_puestos_id = $consulta2['id'];

//EVALUAMOS QUE EL COLABORADOR TENGA ALMACENADA LA JORNADA
$query_jornada = "SELECT id
	FROM jornada_colaboradores
	WHERE j_colaborador_id = '$jornada_id' AND colaborador_id = '$colaborador_id'";
$result_jornada = $mysqli->query($query_jornada);

if($result_jornada->num_rows==0){
	//VERIFICAMOS EL PROCESO 
	if($jornada_id != ""){	 
	   if($servicios_puestos_id == ""){
		   $insert = "INSERT INTO jornada_colaboradores VALUES('$numero', '$jornada_id', '$colaborador_id', '$cantidad_nuevos', '$cantidad_subsiguientes')";
		   $mysqli->query($insert);
		   
		   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		   $historial_numero = historial();
		   $estado_historial = "Agregar";
		   $observacion_historial = "Se ha agregado al colaborador $colaborador_nombre en la jornada de la $jornada_nombre, con un total de $cantidad_nuevos nuevos, y un total de $cantidad_subsiguientes subsiguientes";
		   $modulo = "Servicio Puesto Colaboradores";
		   $insert = "INSERT INTO historial 
			   VALUES('$historial_numero','0','0','$modulo','$numero','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
		   $mysqli->query($insert);	   
		   /********************************************/		   
			$datos = array(
				0 => "Almacenado", 
				1 => "Registro Almacenado Correctamente", 
				2 => "success",
				3 => "btn-primary",
				4 => "formulario_servicios_colaboradores",
				5 => "Registro",
				6 => "servicioColaboradores",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
				7 => "registrar_servicios_colaboradores", //Modals Para Cierre Automatico
			);
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
			1 => "Hay registros en blanco por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",		
		);
	}	
}else{
		$datos = array(
			0 => "Error", 
			1 => "Esta jornada ya ha sido asignada, por favor corregir", 
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