<?php
session_start();
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$receta_id = $_POST['receta_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$estado = 0; //0. Anulada 1. Confirmada
$fecha = date("Y-m-d");
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];

$consultar_colaborador = "SELECT CONCAT(nombre, ' ', apellido) AS 'colaborador'
FROM colaboradores
WHERE colaborador_id = '$usuario'";
$resultColaborador = $mysqli->query($consultar_colaborador);

$NombreColaborador = "";

if($resultColaborador->num_rows > 0) {
	$consultaColaborador = $resultColaborador->fetch_assoc();
	$NombreColaborador = $consultaColaborador['colaborador'];
}

//OBTENER DATOS DE LA RECETA
$query_recetas = "SELECT r.receta_id, r.pacientes_id, p.expediente, r.colaborador_id, r.servicio_id
FROM recetas AS r
INNER JOIN pacientes AS p ON r.pacientes_id = p.pacientes_id
WHERE r.receta_id = '$receta_id' AND r.estado = 1";
$result_recetas = $mysqli->query($query_recetas) or die($mysqli->error);

$pacientes_id = 0;
$expediente = 0;

if($result_recetas->num_rows > 0) {
	$consultaDatosReceta = $result_recetas->fetch_assoc();
	$pacientes_id = $consultaDatosReceta["pacientes_id"];
	$expediente = $consultaDatosReceta["expediente"];
	$colaborador_id = $consultaDatosReceta["colaborador_id"];
	$servicio_id = $consultaDatosReceta["servicio_id"];

	//ACTUALIZAMOS EL EL ESTADO DE LA RECETA
	$update_estado_receta = "UPDATE recetas SET estado = '$estado' WHERE receta_id = '$receta_id'";
	$query = $mysqli->query($update_estado_receta) or die($mysqli->error);	

	if($query){
		echo 1;//RECETA CANCELADA CORRECTAMENTE

		// INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
		$historial_numero = historial();
		$estado_historial = "Aanular";
		$observacion_historial = "El número de receta $receta_id ha sido anulada correctamente, por el usuario: $NombreColaborador, según comentario: $comentario";
		$modulo = "Recetas";

		$insert = "
			INSERT INTO historial
			(
				historial_id, 
				pacientes_id, 
				expediente, 
				modulo, 
				codigo, 
				colaborador_id, 
				servicio_id, 
				fecha, 
				status, 
				observacion, 
				usuario, 
				fecha_registro
				)
			VALUES
			(
				'$historial_numero', 
				'$pacientes_id', 
				'$expediente', 
				'$modulo', 
				'$receta_id', 
				'$colaborador_id', 
				'$servicio_id', 
				'$fecha', 
				'$estado_historial', 
				'$observacion_historial', 
				'$usuario', 
				'$fecha_registro'
			)
		";

		$mysqli->query($insert) or die($mysqli->error);
	}else{
		echo 2;//ERROR AL CANCELAR LA RECETA
	}
}else{
	echo 3;//LA RECETA YA ESTA ANULADA, NO SE PUEDE PROCEDER
}

$resultColaborador->free();//LIMPIAR RESULTADO
$result_recetas->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN