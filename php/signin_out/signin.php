<?php   
session_start();   
include "../funtions.php";
	
header("Content-Type: text/html;charset=utf-8");

$username = $_POST['usu'];  
$password = $_POST['con'];

$mysqli = connect_mysqli(); 

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s");
 
$query = "SELECT c.colaborador_id AS 'colaborador_id', c.puesto_id AS 'puesto_id', u.*
	FROM users AS u
	INNER JOIN colaboradores AS c
	ON u.colaborador_id = c.colaborador_id
	WHERE BINARY username='$username' AND password = MD5('$password')";
$result = $mysqli->query($query); 

$num_row = $result->num_rows;  
$row = $result->fetch_assoc();
	 
//SE EVALUA SI SE DEVOLVIERON RESULTADOS DURANTE LA CONSULTA.
 if( $num_row >=1 ) {  
		if ( $row['estatus'] == 2 ){
			echo 2; //INDICA QUE EL USUARIO NO SE ENCUENTRA ACTIVO
		}else{
			echo 1; //EL USUARIO SE ENCUENTRA ACTIVO E INGRESO CORRECTAMENTE
			$_SESSION['id']=$row['id']; //ALMACENA EL ID DE USUARIO.				
			$_SESSION['colaborador_id']=$row['colaborador_id']; //ALMACENA CODIGO DEL COLABORADOR QUE SERVIRA PARA OBTENER SUS DATOS.												
			$_SESSION['user_name']=$row['username']; //ALMACENA EL NOMBRE DE USUARIO.
			$_SESSION['email']=$row['email']; //ALMACENA EL EMAIL DE USUARIO.		
			$_SESSION['empresa_id']=$row['empresa_id']; //ALMACENA LA EMPRESA.				
			$_SESSION['type']=$row['type']; //ALMACENA EL TIPO DE USUARIO QUE SE LOGONEO.
			$_SESSION['estatus']=$row['estatus']; //ALMACENA EL ESTATUS DEL USUARIO
			$_SESSION['puesto_id']=$row['puesto_id'];//ALMACENA EL PUESTO ID DEL COLABORADOR QUE HA INICIADO SESIÓN
			$colaborador_id = $_SESSION['colaborador_id'];//ALMACENA EL CODIGO DEL COLABORADOR QUE HA INICIADO LA SESIÓN
			
			//ACTUALIZAR HISTORIAL DE INGRESO
			$correlativo = "SELECT MAX(acceso_id) AS max, COUNT(acceso_id) AS count 
			   FROM historial_acceso";
			$result_correlativo = $mysqli->query($correlativo);
			$correlativo2 = $result_correlativo->fetch_assoc();

			$numero = $correlativo2['max'];
			$cantidad = $correlativo2['count'];

			if ( $cantidad == 0 )
			   $numero = 1;
			else
			   $numero = $numero + 1;				
			
			$comentario = mb_convert_case("Inicio de Sesion", MB_CASE_TITLE, "UTF-8");
			$insert = "INSERT INTO historial_acceso 
				VALUES('$numero','$fecha','$colaborador_id','$nombre_host','$comentario'";
			$mysqli->query($insert);						
		}
   }  
   else{  
	  echo 0; //EL USUARIO NO SE ENCUENTRA REGISTRADO  
  }  
?>  