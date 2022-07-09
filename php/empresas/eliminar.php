<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$empresa_id = $_POST['empresa_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$usuario = $_SESSION['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");


//VERIFICAMOS QUE EL USUARIO NO PERTENEZCA A LA EMPRESA A ELIMINAR
$query = "SELECT id
    FROM users
	WHERE colaborador_id = '$usuario' AND empresa_id = '$empresa_id'";
$result_usuario = $mysqli->query($query) or die($mysqli->error); 

if($result_usuario->num_rows==0){
	//VERIFICAMOS QUE LA EMPRESA NO PERTENEZCA A NINGUN REGISTRO
	$query_registro = "SELECT id FROM users WHERE empresa_id = '$empresa_id'";
	$result_registro = $mysqli->query($query_registro) or die($mysqli->error);

    if($result_registro->num_rows==0){
		//ELIMINAMOS LA EMPRESA
		$delete = "DELETE FROM empresa WHERE empresa_id = '$empresa_id'";
		$query = $mysqli->query($delete) or die($mysqli->error);
		
		if($query){
			echo 1;//REGISTRO ELIMINADO CORRECTAMENTE
		}else{
			echo 2;//ERROR AL ELIMINAR EL REGISTRO
		}
	}else{
		echo 3;//ESTE REGISTRO CUENTA CON INFORMACIÓN ALMACENADA NO SE PUEDE ELIMINAR
	}	
	
}else{
	echo 4;//NO SE PUEDE ELIMINAR LA EMPRESA A LA QUE PERTENECE ESTE USUARIO
}

$mysqli->close();//CERRAR CONEXIÓN
?>