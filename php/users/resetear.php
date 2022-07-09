<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];

$consultar_nombre = "SELECT CONCAT(c.nombre, ' ', c.apellido) AS 'colaborador', u.username AS 'username', tu.nombre AS 'tipo_nombre', u.email
   FROM users AS u
   INNER JOIN colaboradores AS c
   ON u.colaborador_id = c.colaborador_id   
   INNER JOIN tipo_user AS tu
   ON u.type = tu.tipo_user_id
   WHERE id = '$id'";
$result = $mysqli->query($consultar_nombre);   
$consultar_datos = $result->fetch_assoc();
$colaborador = $consultar_datos['colaborador'];
$username = $consultar_datos['username'];
$tipo_nombre = $consultar_datos['tipo_nombre'];
$para = $consultar_datos['email'];
$from = "Cambio de Contraseña";
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

$consultar_datos = "SELECT CONCAT(nombre, ' ', apellido) AS 'colaborador', u.email 
   FROM users AS u
   INNER JOIN colaboradores AS c
   ON u.colaborador_id = c.colaborador_id
   WHERE c.colaborador_id = '$id'";
$result = $mysqli->query($consultar_datos);   

$contraseña_generada = generar_password_complejo();

$query = "UPDATE users SET password = MD5('$contraseña_generada') 
   WHERE id = '$id'";
$query = $mysqli->query($query);

if($query){
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL 
   $historial_numero = historial();
   $estado_historial = "Actualizar";
   $observacion_historial = "Se ha reseteado la contraseña para el usuario $colaborador (username: $username) con perfil $tipo_nombre, para uso en el sistema";
   $modulo = "Usuarios";
   $insert = "INSERT INTO historial 
      VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	 
   /********************************************/ 	
	
   echo 1;
	
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

   $from = "Cambio de Contraseña";
   $asunto = "Cambio de Contraseña\n";
   $CharSet = "UTF-8";   
   $mensaje = "";
   $url_logo = SERVERURL."img/logo.png";
   $url_sistema = SERVERURL;
   $url_footer = "";
   $url_facebook = "#";
   $url_sitio_web = "#";
   $mensaje = "";

   $mensaje="
            <table class='table table-striped table-responsive-md btn-table'>
            <tr>
               <td colspan='2'><center><img width='45%' heigh='40%' src='".$url_logo."'></center></td>
            </tr>
            <tr>
               <td colspan='2'><center><b><h4>Notificación Cambio de Contraseña</h4></b></center></td>
            </tr>
            <tr>
              <td>
	            <p style='text-align: justify'>Estimado(a) <b>".$colaborador."</b>, se le notifica que se ha cambiado su contraseña.
		        <br/>Su nueva contraseña es: <b>".$contraseña_generada."</b> se requiere que la cambie a la brevedad posible.
		        <a href='".$url_sistema."'>Presione este enlace para acceder al Sistema Hospitalario</a>
				</p>
	          </td>
            </tr>
            <tr>
              <td>
                <p style='text-align: justify; font-size:12px;'><b>
				  Este mensaje ha sido enviado de forma automática, por favor no responder este correo. Cualquier duda o consulta envié un correo electrónico a: gestionsistemas@hsjddhn.com, o puede marcar  a nuestra PBX: 2512-0870 Ext. 116.<br/>Haciendo el bien a los demás nos hacemos el bien a nosotros mismos.
				</b></p>
	          </td>
			</tr>
			<tr>
              <td>
                <p><img width='100%' heigh='100%' src='".$url_footer."'></p>
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
    $mail->AddAddress($para);// Destinatario
    $mail->Username = $de;//Aqui pon tu correo de gmail
    $mail->Password = $contraseña;//Aqui pon tu contraseña de gmail
    $mail->Subject = $asunto; //Asunto del correo
    $mail->Body = $mensaje; //Contenido del correo
    $mail->WordWrap = 50; //No. de columnas
    $mail->MsgHTML($mensaje);//Se indica que el cuerpo del correo tendrá formato html

    if($para != ""){		
        if($mail->Send()){ //enviamos el correo por PHPMailer
	       $respuesta = "El mensaje ha sido enviado con la clase PHPMailer =)";
		   
           //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
           $historial_numero = historial();
           $estado_historial = "Envio";
           $observacion_historial = "Se ha enviado un correo Electrónico al colaborador $colaborador con username $username al correo $para, indicandole que se ha reseteado su contraseña";
           $modulo = "Usuarios";
           $insert = "INSERT INTO historial 
                VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
           $mysqli->query($insert);	   
           /********************************************/ 		   
        }else{
	       $respuesta = "El mensaje no se pudo enviar con la clase PHPMailer =(";
   	       $respuesta .= " Error: ".$mail->ErrorInfo;
        }			   
    }	
}else{
	echo 2;
}
?>