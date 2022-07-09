<?php 
session_start(); 
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 
$id = $_POST['id-registro1'];
$email = trim($_POST['email1']);
$tipo = $_POST['tipo1'];
$estatus = $_POST['estatus1'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//CONSULTA REGISTRO
$consultar_nombre = "SELECT CONCAT(c.nombre, ' ', c.apellido) AS 'colaborador', u.username AS 'username', tu.nombre AS 'tipo_nombre' 
   FROM users AS u
   INNER JOIN colaboradores AS c
   ON u.colaborador_id = c.colaborador_id   
   INNER JOIN tipo_user AS tu
   ON u.type = tu.tipo_user_id
   WHERE id = '$id'";
$result = $mysqli->query($consultar_nombre);   
$consultar_nombre1 = $result->fetch_assoc();
$colaborador = $consultar_nombre1['colaborador'];
$username = $consultar_nombre1['username'];
$tipo_nombre = $consultar_nombre1['tipo_nombre'];

$update = "UPDATE users SET email = '$email', type = '$tipo', estatus = '$estatus' 
		   WHERE id = '$id'";
$query = $mysqli->query($update);

if($query){
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
   $historial_numero = historial();
   $estado_historial = "Actualizar";
   $observacion_historial = "Se han actualizado los datos al usuario $colaborador (username: $username) con perfil $tipo_nombre, para uso en el sistema";
   $modulo = "Usuarios";
   $insert = "INSERT INTO historial 
      VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	 
   /********************************************/ 		
	
	$datos = array(
		0 => "Modificado", 
		1 => "Registro Modificado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "",
		5 => "Registro",
		6 => "Usuarios",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "registrar_editar", //Modals Para Cierre Automatico
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

echo json_encode($datos);
?>