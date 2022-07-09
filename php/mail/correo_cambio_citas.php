<?php
include('../funtions.php');
session_start();  
	
//CONEXION A DB
$mysqli = connect_mysqli();

date_default_timezone_set('America/Tegucigalpa');
$agenda_id = $_POST['agenda_id'];

//CONSULTAR DATOS DE LA AGENDA
$consultar_paciente_agenda = "SELECT pacientes_id, colaborador_id, 	hora, CAST(fecha_cita AS DATE) AS 'fecha_cita', servicio_id, DATE_FORMAT(fecha_registro, '%d/%m/%Y %h:%i:%s %p') AS 'fecha_registro' 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consultar_paciente_agenda);	
$consultar_paciente_agenda2 = $result->fetch_assoc();	
$pacientes_id = $consultar_paciente_agenda2['pacientes_id'];
$medico = $consultar_paciente_agenda2['colaborador_id'];	
$fecha_cita = $consultar_paciente_agenda2['fecha_cita'];
$hora = date('g:i a',strtotime($consultar_paciente_agenda2['hora']));		
$servicio = $consultar_paciente_agenda2['servicio_id'];	
$fecha_registro = $consultar_paciente_agenda2['fecha_registro'];	

//CONSULTAR NOMBRE DE PROFESIONAL
$consulta_nombre_profesional = "SELECT CONCAT(nombre,' ',apellido) AS nombre, puesto_id
     FROM colaboradores 
	 WHERE colaborador_id = '$medico'";
$result = $mysqli->query($consulta_nombre_profesional);	 
$consulta_nombre_profesional2 = $result->fetch_assoc();	
$nombre_colaborador = $consulta_nombre_profesional2['nombre'];	
$puesto_id = $consulta_nombre_profesional2['puesto_id'];

//INICIO CONSULTAR CORREO DEL MEDICO
$consulta_correo_profesional = "SELECT email
	FROM users
	WHERE colaborador_id = '$medico'";
$result_correo_profesional = $mysqli->query($consulta_correo_profesional);	 
$correo_profesional = $result_correo_profesional->fetch_assoc();	

$email_profesional = "";

if($result_correo_profesional->num_rows>0){
	$email_profesional = $correo_profesional['email'];
}
//FIN CONSULTAR CORREO DEL MEDICO
	
//OBTENER CORREO ELECTRONICO DEL USUARIO
$consulta_correo = "SELECT email, CONCAT(nombre,' ',apellido) AS nombre, expediente, identidad 
     FROM pacientes 
	 WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_correo);	 
$consulta_correo2 = $result->fetch_assoc();	
$para = $consulta_correo2['email'];
$nombre = $consulta_correo2['nombre'];	

if($consulta_correo2['expediente'] == 0 ){
   $expediente = "TEMP";	
}else{
   $expediente = $consulta_correo2['expediente'];	
}
	
$identidad = $consulta_correo2['identidad'];	

//CONSULTRA NOMBRE DE SERVICIO
$consulta_nombre_servicio = "SELECT nombre 
    FROM servicios 
	WHERE servicio_id = '$servicio'";
$result = $mysqli->query($consulta_nombre_servicio);
$consulta_nombre_servicio2 = $result->fetch_assoc();	
$nombre_servicio = $consulta_nombre_servicio2['nombre'];

//CONOCER EL TIPO DE USUARIO
$consultar_expediente = "SELECT a.agenda_id AS 'agenda_id'
    FROM agenda AS a
    INNER JOIN colaboradores AS c
	ON a.colaborador_id = c.colaborador_id
    WHERE pacientes_id = '$pacientes_id' AND a.servicio_id = '$servicio' AND c.puesto_id = '$puesto_id' AND a.status = 1";
$result = $mysqli->query($consultar_expediente);	

$consultar_expediente1 = $result->fetch_assoc();   

if($consultar_expediente1['agenda_id'] == ""){
   $usuario = 'Nuevo'; 
}else{
   $usuario = 'Subsiguiente';
} 

//OBTENER NOMBRE DE EMPRESA
$usuario_sistema = $_SESSION['colaborador_id'];	

//OBTENEMOS LOS DATOS DEL USUARIO Y DE LA EMPRESA
$query_usuario = "SELECT e.telefono AS 'telefono', e.celular AS 'celular', e.correo AS 'correo', e.horario AS 'horario', e.eslogan AS 'eslogan', e.facebook AS 'facebook', e.sitioweb AS 'sitioweb'
	FROM users AS u
	INNER JOIN empresa AS e
	ON u.empresa_id = e.empresa_id
	WHERE u.colaborador_id = '$usuario_sistema'";
$result_usuario = $mysqli->query($query_usuario); 			

$telefono = '';
$celular = '';
$telefono = '';
$horario = '';
$eslogan = '';
$facebook = '';
$sitioweb = '';	
$correo_empresa = '';

if($result_usuario->num_rows >= 0){
	$consulta_empresa = $result_usuario->fetch_assoc();
	$telefono = $consulta_empresa['telefono'];
	$celular = $consulta_empresa['celular'];
	$correo_empresa = $consulta_empresa['correo'];   
	$horario = $consulta_empresa['horario'];
	$eslogan = $consulta_empresa['eslogan'];
	$facebook = $consulta_empresa['facebook'];
	$sitioweb = $consulta_empresa['sitioweb'];					
} 

//OBTENER EL CORREO
$tipo_correo = "Citas";
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

$asunto = "Cambio de Cita";
$from = "Cambio de Cita";
$CharSet = "UTF-8";
$url_logo = SERVERURL."img/logo.png";
$url_sistema = SERVERURL;
$url_footer = SERVERURL."img/logo.png";
$url_facebook = $facebook;
$url_sitio_web = $sitioweb;
$mensaje = "";

$mensaje="
   <table class='table table-striped table-responsive-md btn-table'>
       <tr>
          <td colspan='2'><center><img width='45%' heigh='40%' src='".$url_logo."'></center></td>
       </tr>
       <tr>
          <td colspan='2'><center><b><h4>Cita Modificada</h4></b></center></td>
       </tr>
	   <tr>
	       <td colspan='2'><b>Estimado(a) $nombre, se le informa que se ha cambiado su cita, los detalles de esta a continuación.</b></td>
	   </tr>
	   <tr>
	       <td><b>Identidad:</b></td>
		   <td>$identidad</td>		
	   </tr>
	   <tr>
	       <td><b>Expediente:</b></td>
		   <td>$expediente</td>		
	   </tr>	   
	   <tr>
	       <td><b>Fecha Cita:</b></td>
		   <td>$fecha_cita</td>
	   </tr>
	   <tr>
	       <td><b>Hora:</b></td>
		   <td>$hora</td>
	   </tr>
	   <tr>
	       <td><b>Tipo de Cita:</b></td>
		   <td>$usuario </td>
	   </tr>	   
	   <tr>
	       <td><b>Profesional:</b></td>
		   <td>$nombre_colaborador</td>
	   </tr>	   
	   <tr>
	       <td><b>Servicio:</b></td>
		   <td>$nombre_servicio</td>
	   </tr>
	   <tr>
	       <td><b>Fecha de Registro:</b></td>
		   <td>$fecha_registro</td>
	   </tr>	   
       <tr>
          <td colspan='2'>
		  <p style='text-align: justify'>Se le recuerda que debe estar 15 minutos antes de su cita, y debe venir a compañado de un familiar.</p>
		  </td>
       </tr>
       <tr>
          <td colspan='2'>
             <p style='text-align: justify; font-size:12px;'><b>
		     Este mensaje ha sido enviado de forma automática, por favor no responder este correo. Cualquier duda o consulta puede contactarnos a las siguientes direcciones de correo: 
             <u>
                <li style='text-align: justify; font-size:12px;'>".$correo_empresa."</li>
             </u>
			 <ul>
				<li><b style='text-align: justify; font-size:12px;'>Tambien puede llamarnos a nuestra Teléfono: ".$telefono."</b></li>
				<li><b style='text-align: justify; font-size:12px;'>Tambien puede escribirnos a nuestra WhatsApp: ".$celular."</b></li>
				<li><b style='text-align: justify; font-size:12px;'>En los siguientes horarios: ".$horario."</b></li>
			 </ul>
			 <ul>
				<li> <a style='text-align: justify; font-size:12px;' href='".$url_sitio_web."'>Presione este enlace, para acceder a Nuestro Sitio WEB</a></li>
				<li><a style='text-align: justify; font-size:12px;' href='".$url_sitio_web."'>Presione este enlace, para acceder a Nuestro Facebook</a></li>
			 </ul>
             <p style='text-align: justify; font-size:12px;'><b>".$eslogan."</b></p>
			 <br/>
			 <br/>
             <p style='text-align: justify; font-size:12px;'>
				<b>
					Este correo fue enviado desde una dirección solamente de notificaciones que no puede aceptar correo electrónico entrante. Por favor no respondas a este mensaje..
				</b>
			 </p>
		 </td>
       </tr>		
       <tr>
          <td colspan='2'>
		     <p><img width='25%' heigh='20%' src='".$url_footer."'></p>
		  </td>
       </tr>	   
   </table>	   
";
	   
$cabeceras = "MIME-Version: 1.0\r\n";
$cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
$cabeceras .= "From: $de \r\n";
		
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
$mail->AddCC($email_profesional);// Copia Destinatario
$mail->Username = $de;//Aqui pon tu correo de gmail
$mail->Password = $contraseña;//Aqui pon tu contraseña de gmail
$mail->Subject = $asunto; //Asunto del correo
$mail->Body = $mensaje; //Contenido del correo
$mail->WordWrap = 50; //No. de columnas
$mail->MsgHTML($mensaje);//Se indica que el cuerpo del correo tendrá formato html

if($para != ""){		
   if($mail->Send()){ //enviamos el correo por PHPMailer
	  $respuesta = "El mensaje ha sido enviado con la clase PHPMailer y tu cuenta de gmail =)";
   }else{
	  $respuesta = "El mensaje no se pudo enviar con la clase PHPMailer y tu cuenta de gmail =(";
   	  $respuesta .= " Error: ".$mail->ErrorInfo;
   }			   
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>