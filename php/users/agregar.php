<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$proceso = $_POST['pro'];
$id = $_POST['id-registro'];
$colaborador_id = $_POST['colaborador'];
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$tipo = $_POST['tipo'];
$estatus = $_POST['estatus'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
$empresa_id = $_POST['empresa'];

//OBTENER EL TIPO DE Usuario
$query_tipo = "SELECT nombre
   FROM tipo_user
   WHERE tipo_user_id = '$tipo'";
$result = $mysqli->query($query_tipo);   
$consultar_tipo = $result->fetch_assoc();
$tipo_nombre = $consultar_tipo['nombre'];

$contraseña_generada = generar_password_complejo();

$consultar_nombre = "SELECT CONCAT(nombre, ' ', apellido) AS 'colaborador' 
   FROM colaboradores 
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_nombre);   
$consultar_nombre1 = $result->fetch_assoc();
$colaborador = $consultar_nombre1['colaborador'];

//OBTENER CORRELATIVO
$correlativo= "SELECT MAX(id) AS max, COUNT(id) AS count 
   FROM users";
$result = $mysqli->query($correlativo);
$correlativo2 = $result->fetch_assoc();

$numero = $correlativo2['max'];
$cantidad = $correlativo2['count'];

if ( $cantidad == 0 )
	$numero = 1;
else
    $numero = $numero + 1;	
	
//VALIDAMOS SI EXISTE EL USUARIOS
$query_valid_user = "SELECT id 
FROM users 
WHERE colaborador_id = '$colaborador_id'";
$result_valid_user  = $mysqli->query($query_valid_user); 

//VALIDAMOS QUE EL CORREO NO SE ESTE DUPLICANDO
$query_correo = "SELECT id 
   FROM users 
   WHERE email = '$email'";
$result_valid_correo = $mysqli->query($query_correo);   

//CONTAMOS CUANTOS USUARIOS EXISTEN REGISTRADOS
$query_total_usuarios = "SELECT COUNT(*) AS 'total_usuarios'
   FROM users
   WHERE estatus = 1 AND type NOT IN(1)";
$result_total_usuarios = $mysqli->query($query_total_usuarios); 
$cantidad_usuario_sistema = $result_total_usuarios->fetch_assoc();
$total_usuarios_sistema = $cantidad_usuario_sistema['total_usuarios'];

//CONSULTAMOS EL TOTAL DE USUARIOS DISPONIBLES EN EL PLAN
$query_usuarios_plan = "SELECT * 
   FROM plan";
$result_usuario_plan = $mysqli->query($query_usuarios_plan); 
$cantidad_usuario_plan = $result_usuario_plan->fetch_assoc();
$total_usuarios_plan = $cantidad_usuario_plan['users'] + $cantidad_usuario_plan['user_extra'];

//SI EL LIMITE DEL PLAN SE ESTABLECE EN CERO, ESTE PERMITIRA AGREGAR MAS USUARIOS SIN NINGUN LIMITE
if($cantidad_usuario_plan['users'] == 0){
   $total_usuarios_plan = $total_usuarios_sistema + 1;
}    

if($total_usuarios_sistema < $total_usuarios_plan){
	if($result_valid_correo->num_rows == 0){
		if($result_valid_user->num_rows==0){
			 $insert = "INSERT INTO users 
			 VALUES('$numero', '$colaborador_id', '$username', MD5('$contraseña_generada'), '$email', '$tipo','$estatus','$fecha_registro', '$empresa_id')";
			 $query = $mysqli->query($insert);

			 if($query){
				//INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
				$historial_numero = historial();
				$estado_historial = "Agregar";
				$observacion_historial = "Se ha agregado el colaborador $colaborador con username $username bajo el perfil $tipo_nombre, para uso en el sistema";
				$modulo = "Usuarios";
				$insert = "INSERT INTO historial 
				   VALUES('$historial_numero','0','0','$modulo','$numero','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
				$mysqli->query($insert);	   
				/********************************************/ 	
				
				$datos = array(
				   0 => "Almacenado", 
				   1 => "Registro Almacenado Correctamente", 
				   2 => "success",
				   3 => "btn-primary",
				   4 => "formulario",
				   5 => "Registro",
				   6 => "Usuarios",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
				   7 => "registrar", //Modals Para Cierre Automatico
				   8 => "", //Modals Para Cierre Automatico		
				);

				//OBTENEMOS LOS DATOS DEL USUARIO Y DE LA EMPRESA
				$query_usuario = "SELECT e.telefono AS 'telefono', e.celular AS 'celular', e.correo AS 'correo', e.horario AS 'horario', e.eslogan AS 'eslogan', e.facebook AS 'facebook', e.sitioweb AS 'sitioweb'
					FROM users AS u
					INNER JOIN empresa AS e
					ON u.empresa_id = e.empresa_id
					WHERE u.colaborador_id = '$usuario'";
				$result_usuario = $mysqli->query($query_usuario); 			
				
				$telefono = '';
				$celular = '';
				$telefono = '';
				$horario = '';
				$eslogan = '';
				$facebook = '';
				$sitioweb = '';	
				$correo = '';

				if($result_usuario->num_rows >= 0){
					$consulta_empresa = $result_usuario->fetch_assoc();
					$telefono = $consulta_empresa['telefono'];
					$celular = $consulta_empresa['celular'];
					$correo = $consulta_empresa['correo'];   
					$horario = $consulta_empresa['horario'];
					$eslogan = $consulta_empresa['eslogan'];
					$facebook = $consulta_empresa['facebook'];
					$sitioweb = $consulta_empresa['sitioweb'];					
				}

				//OBTENER EL CORREO
				$tipo_correo = "Notificaciones";
				$query_correo = "SELECT c.correo_id AS 'correo_id', c.correo_tipo_id AS 'correo_tipo_id', ct.nombre AS 'tipo_correo', c.server AS 'server', c.correo AS 'correo', c.port AS 'port', c.smtp_secure AS 'smtp_secure', c.estado AS 'estado', c.password AS 'password'
					FROM correo AS c
					INNER JOIN correo_tipo AS ct
					ON c.correo_tipo_id = ct.correo_tipo_id
					WHERE ct.nombre = '$tipo_correo'";
				$result_correo = $mysqli->query($query_correo); 									

				$de = "";
				$contraseña = "";
				$server = "";
				$port = "";
				$smtp_secure = "";

				if($result_correo->num_rows >= 0){
					$consulta_correo = $result_correo->fetch_assoc();
					$de = $consulta_correo['correo'];
					$contraseña = decryption($consulta_correo['password']);
					$server = $consulta_correo['server'];   
					$port = $consulta_correo['port'];
					$smtp_secure = $consulta_correo['smtp_secure'];	
				}
   
				$from = "Creación de Usuario";
				$asunto = "Creación de Usuario\n";
				$mensaje = "";
				$CharSet = "UTF-8";
				$url_logo = SERVERURL."img/logo.png";
				$url_sistema = SERVERURL;
				$url_footer = SERVERURL."vistas/plantilla/img/logo.png";
				$url_facebook = $facebook;
				$url_sitio_web = $sitioweb;

				$mensaje="
						 <table class='table table-striped table-responsive-md btn-table'>
						 <tr>
						 <td colspan='2'><center><img width='25%' heigh='20%' src='".$url_logo."'></center></td>
						 </tr>
						 <tr>
							<td colspan='2'><center><b><h4>Notificación Cambio de Contraseña</h4></b></center></td>
						 </tr>
						 <tr>
						 <td>
							<p style='text-align: justify'>Estimado(a) <b>".$colaborador."</b>, Le damos la mas cordial bienvenida al sistema, Le notificamos lo siguiente.
						 <br/>Se ha creado su nuevo usuario de acceso el cual es <b>".$username."</b> y la contraseña asignada es: <b>".$contraseña_generada."</b> se requiere que la cambie a la brevedad posible.
						 <a href='".$url_sistema."'>Presione este enlace para acceder al Sistema Hospitalario</a>
						 </p>	
						 </td>
						 </tr>
						 <tr>
						 <td>
						 <p style='text-align: justify; font-size:12px;'>
							<b>
							   Este correo fue enviado desde una dirección solamente de notificaciones que no puede aceptar correo electrónico entrante. Por favor no respondas a este mensaje..
							</b>
						 </p>
						 </td>
					  </tr>
					  <tr>
						 <td>
						 <td colspan='2'><center><img width='25%' heigh='20%' src='".$url_footer."'></center></td>
						 </td>			  
					  </tr>   
					  </table>
					  ";		

				$cabeceras = "MIME-Version: 1.0\r\n";
				$cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
				$cabeceras .= "From: $de \r\n";

				//$archivo = $_FILES["archivo_fls"]["tmp_name"];
				//$destino = $_FILES["archivo_fls"]["name"];

				//incluyo la clase phpmailer	
				include_once("../phpmailer/class.phpmailer.php");
				include_once("../phpmailer/class.smtp.php");
				
				$mail = new PHPMailer(); //creo un objeto de tipo PHPMailer
				$mail->SMTPDebug = 1;
				$mail->IsSMTP(); //protocolo SMTP
				$mail->IsHTML(true);
				$mail->CharSet = $CharSet;
				$mail->SMTPAuth = true;//autenticación en el SMTP
				$mail->SMTPSecure = $smtp_secure;
				$mail->SMTPOptions = array(
				   'ssl' => array(
					  'verify_peer' => false,
					  'verify_peer_name' => false,
					  'allow_self_signed' => true
				   )
				);	
				$mail->Host = $server;//servidor de SMTP de gmail
				$mail->Port = $port;//puerto seguro del servidor SMTP de gmail
				$mail->From = $de; //Remitente del correo
				$mail->FromName = $from; //Remitente del correo
				$mail->AddAddress($email);// Destinatario
				$mail->Username = $de;//Aqui pon tu correo de gmail
				$mail->Password = $contraseña;//Aqui pon tu contraseña de gmail
				$mail->Subject = $asunto; //Asunto del correo
				$mail->Body = $mensaje; //Contenido del correo
				$mail->WordWrap = 50; //No. de columnas
				$mail->MsgHTML($mensaje);//Se indica que el cuerpo del correo tendrá formato html

				if($email != ""){		
				   if($mail->Send()){ //enviamos el correo por PHPMailer
					  $respuesta = "El mensaje ha sido enviado con la clase PHPMailer =)";
					  
					  //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
					  $historial_numero = historial();
					  $estado_historial = "Envio";
					  $observacion_historial = "Se ha enviado un correo Electrónico al colaborador $colaborador con username $username al correo $email";
					  $modulo = "Usuarios";
					  $insert = "INSERT INTO historial 
							VALUES('$historial_numero','0','0','$modulo','$numero','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
					  $mysqli->query($insert);	   
					  /********************************************/ 
				
				   }else{
					  $respuesta = "El mensaje no se pudo enviar con la clase PHPMailer =(";
						 $respuesta .= " Error: ".$mail->ErrorInfo;
				   }			   
				}	
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
				1 => "Lo sentimos este usuario ya ha sido registrado, por favor corregir", 
				2 => "error",
				3 => "btn-danger",
				4 => "",
				5 => "",
			 );			
		}
	}else{
		$datos = array(
			 0 => "Correo Duplicado", 
			 1 => "Lo sentimos este correo ya ha sido registrado, por favor corregir", 
			 2 => "error",
			 3 => "btn-danger",
			 4 => "",
			 5 => "",
		 );
	}
}else{
	$datos = array(
		0 => "Límite de usuarios excedido", 
		1 => "Lo sentimos, ha excedido el límite de usuarios según su plan", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "", 
   );  	
}


echo json_encode($datos);
?>