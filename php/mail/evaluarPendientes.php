<?php  
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

/*EVALUAR PENDIENTES EN EL AGENDA*/
$colaborador_id = $_SESSION['colaborador_id'];
$fecha = date("Y-m-d");

//FECHA
$año = date("Y", strtotime($fecha));
$mes = date("m", strtotime($fecha));
$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));

$dia1 = date('d', mktime(0,0,0, $mes, 1, $año)); //PRIMER DIA DEL MES
$dia2 = date('d', mktime(0,0,0, $mes, $dia, $año)); // ULTIMO DIA DEL MES

$fecha_inicial = date("Y-m-d", strtotime($año."-".$mes."-".$dia1));
$nuevafecha = date("Y-m-d", strtotime ( '-1 day' , strtotime ( $fecha )));

$mes_actual=nombremes(date("m", strtotime($fecha)));

$consultar_puesto_colaborador = "SELECT puesto_id 
    FROM colaboradores 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consultar_puesto_colaborador);
$consultar_puesto_colaborador2 = $result->fetch_assoc();
$puesto_colaboradores = $consultar_puesto_colaborador2['puesto_id'];

$consultar_registros = "SELECT COUNT(pacientes_id) AS 'total' 
   FROM agenda 
   WHERE CAST(fecha_cita AS DATE) BETWEEN '$fecha_inicial' AND '$nuevafecha' AND status = 0 and preclinica = 1 AND colaborador_id = '$colaborador_id'";		 
$result = $mysqli->query($consultar_registros);
$consultar_registros2 = $result->fetch_assoc();
$total_agenda = $consultar_registros2['total'];

if($fecha == $fecha_inicial){
	$total_agenda = 0;
}
/*FIN DE EVALUACION DE PENDIENTES AGENDA*/

/*EVALUAR PENDIENTES HOSPITALIZACION*/
//OBTENER PUESTO_ID
$consulta_puesto = "SELECT puesto_id, CONCAT(c.nombre,' ',c.apellido) AS 'nombre' 
     FROM colaboradores AS c 
     WHERE c.colaborador_id = '$colaborador_id'
     GROUP BY 1";
$result = $mysqli->query($consulta_puesto);	 
$consulta_puesto2 = $result->fetch_assoc();
$consulta_puesto_id = $consulta_puesto2['puesto_id'];
$consulta_colaborador = $consulta_puesto2['nombre'];

if($consulta_colaborador  == ""){
   $consulta_puesto = "SELECT puesto_id, CONCAT(nombre,' ',apellido) AS 'nombre' 
        FROM colaboradores 
		WHERE colaborador_id = '$colaborador_id'";
   $result = $mysqli->query($consulta_puesto);
   $consulta_puesto2 = $result->fetch_assoc();
   $consulta_puesto_id = $consulta_puesto2['puesto_id'];
   $consulta_colaborador = $consulta_puesto2['nombre'];
}

if($total_agenda == 1){
	$string_agenda = "registro pendiente";
}else{
	$string_agenda = "registros pendientes";
}

//CONSULTAR CORREO
$consulta_correo = "SELECT email 
    FROM users 
	WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_correo);
$consulta_correo2 = $result->fetch_assoc();
$correo_consulta = $consulta_correo2['email'];
   
//OBTENEMOS LOS DATOS DEL USUARIO Y DE LA EMPRESA
$query_usuario = "SELECT e.telefono AS 'telefono', e.celular AS 'celular', e.correo AS 'correo', e.horario AS 'horario', e.eslogan AS 'eslogan', e.facebook AS 'facebook', e.sitioweb AS 'sitioweb'
	FROM users AS u
	INNER JOIN empresa AS e
	ON u.empresa_id = e.empresa_id
	WHERE u.colaborador_id = '$colaborador_id'";
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

$from = "Pendientes";
$asunto = "Registros Pendientes\n";
$mensaje = "";
$para = $correo_consulta;
$CharSet = "UTF-8";
$url_logo = SERVERURL."img/logo.png";
$url_sistema = SERVERURL;
$url_footer = SERVERURL."img/logo.png";
$url_facebook = $facebook;
$url_sitio_web = $sitioweb;

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
$correo_empresa = 'admision@mentesanahn.com';

if($result_empresa->num_rows>0){
   $telefono = $consulta_empresa['telefono'];
   $celular = $consulta_empresa['celular'];
   $correo = $consulta_empresa['correo'];   
   $horario = $consulta_empresa['horario'];
   $eslogan = $consulta_empresa['eslogan'];   
}   

if($total_agenda > 0){
    $mensaje="
       <table class='table table-striped table-responsive-md btn-table'>
         <tr>
            <td colspan='2'><center><img width='25%' heigh='20%' src='".$url_logo."'></center></td>
         </tr>
         <tr>
            <td colspan='2'><center><b><h4>Reporte de Registros Pendientes</h4></b></center></td>
         </tr>
         <tr>
            <td colspan='2'><center><b><h3><center><b>Agenda</b></center></h3></b></center></td>
         </tr>		 
         <tr>
            <td>
	           <p style='text-align: justify'>Estimado(a) <b>$consulta_colaborador</b>, se le recuerda que tiene un total de <b>$total_agenda</b> $string_agenda en el mes de <b>$mes_actual, $año.
			   </b><br/>Por favor ingresar la atencion de los usuarios. No debe dejar ningun registro pendiente dentro de este mes.
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
               <p><img width='25%' heigh='20%' src='".$url_footer."'></p>
	        </td>			  
         </tr>   
       </table>
    ";	
		 
}

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
   }else{
	  $respuesta = "El mensaje no se pudo enviar con la clase PHPMailer =(";
   	  $respuesta .= " Error: ".$mail->ErrorInfo;
   }			   
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>