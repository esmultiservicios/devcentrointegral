<?php
include('../funtions.php');
session_start();
	
//CONEXION A DB
$mysqli = connect_mysqli();

date_default_timezone_set('America/Tegucigalpa');
$facturas_id = $_POST['facturas_id'];

//CONSULTAR DATOS DE FACTURA
$query = "SELECT CONCAT(p.nombre,' ',p.apellido) AS 'paciente', p.email AS 'correo', f.number AS 'numero', sf.prefijo AS 'prefijo', sf.relleno AS 'relleno'
	FROM facturas AS f
	INNER JOIN pacientes AS p
	ON f.pacientes_id = p.pacientes_id
	INNER JOIN secuencia_facturacion AS sf
	ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
	WHERE f.facturas_id = '$facturas_id'";
$result = $mysqli->query($query);

$nombre = "";
$para = "";
$no_factura = "";
$prefijo = "";
	 
if($result->num_rows>=0){
	$factura = $result->fetch_assoc();
	$nombre = $factura['paciente'];
	$para = $factura['correo'];
	$no_factura = str_pad($factura['numero'], $factura['relleno'], "0", STR_PAD_LEFT);
	$prefijo = $factura['prefijo'];
}

//OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];	

$query_empresa = "SELECT e.telefono AS 'telefono', e.celular AS 'celular', e.correo AS 'correo', e.horario AS 'horario', e.eslogan AS 'eslogan'
FROM users AS u
INNER JOIN empresa AS e
ON u.empresa_id = e.empresa_id
WHERE u.colaborador_id = '$usuario'";
$result_empresa = $mysqli->query($query_empresa) or die($mysqli->error);;
$consulta_empresa = $result_empresa->fetch_assoc();

$telefono = '';
$celular = '';
$telefono = '';
$horario = '';
$eslogan = '';
$correo_empresa = '';

if($result_empresa->num_rows>0){
   $telefono = $consulta_empresa['telefono'];
   $celular = $consulta_empresa['celular'];
   $correo = $consulta_empresa['correo'];   
   $horario = $consulta_empresa['horario'];
   $eslogan = $consulta_empresa['eslogan'];   
}  

$de = "facturacion@centrointegral.clinicarehn.com";
$contraseña = 'C3nt0lnt3grl2021';   
$servidor = "centrointegral.clinicarehn.com";
$puerto = "465";
$SMTPSecure = "ssl"; 
$from = "Factura";		   
$asunto = "Factura N° ".$prefijo." ".$no_factura;
$CharSet = "UTF-8";
$factura_documento = "factura_".$no_factura;
$URL = dirname('__FILE__').'/Facturas/'.$factura_documento.'.pdf';
$url_logo = SERVERURL."img/logo.png";
$url_sistema = SERVERURL;
$url_footer = "";
$url_facebook = "#";
$url_sitio_web = "#";

$mensaje="
   <table class='table table-striped table-responsive-md btn-table'>
       <tr>
          <td colspan='2'><center><img width='25%' heigh='20%' src='".$url_logo."'></center></td>
       </tr>
       <tr>
          <td colspan='2'><center><b><h4>Factura</h4></b></center></td>
       </tr>
	   <tr>
	       <td colspan='2'><b>Estimado(a) $nombre, se le notifica que se le esta haciendo llegar su factura # ".$prefijo."$no_factura.</b></td>
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
				<li><b style='text-align: justify; font-size:12px;'>Tambien puede llamarnos a nuestra WhatsApp: ".$celular."</b></li>
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
$mail->SMTPSecure = $SMTPSecure;
$mail->Host = $servidor;//servidor de SMTP de gmail
$mail->Port = $puerto;//puerto seguro del servidor SMTP de gmail
$mail->From = $de; //Remitente del correo
$mail->FromName = $from; //Remitente del correo
$mail->AddAddress($para);// Destinatario
$mail->Username = $de;//Aqui pon tu correo de gmail
$mail->Password = $contraseña;//Aqui pon tu contraseña de gmail
$mail->Subject = $asunto; //Asunto del correo
$mail->Body = $mensaje; //Contenido del correo
$mail->AddAttachment($URL, $prefijo.''.$no_factura.'.pdf');
$mail->WordWrap = 50; //No. de columnas
$mail->MsgHTML($mensaje);//Se indica que el cuerpo del correo tendrá formato html

if($para != ""){		
   if($mail->Send()){ //enviamos el correo por PHPMailer
	  echo 1;//CORREO ENVIDO SATISFACTORIAMENTE
   }else{
	  echo 2;//CORREO NO ENVIDO
   }			   
}else{
	echo 3;//NO HAY UN DESTINATARIO, NO SE PUEDE ENVIAR EL CORREO
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>