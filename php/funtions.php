<?php
require_once "conf/configAPP.php";
date_default_timezone_set('America/Tegucigalpa');   

function dia_nombre($fecha){
   $dia_nombre = '';
   switch (date('w', strtotime($fecha))){ 
	 case 0: $dia_nombre = "domingo"; break; 
	 case 1: $dia_nombre = "lunes"; break; 
	 case 2: $dia_nombre = "martes"; break; 
	 case 3: $dia_nombre = "miercoles"; break; 
	 case 4: $dia_nombre = "jueves"; break; 
	 case 5: $dia_nombre = "viernes"; break; 
	 case 6: $dia_nombre = "sabado"; break; 
  }	 

  return $dia_nombre;	  
}

function dia_nombre_corto($fecha){
   $dia_nombre = '';
   switch (date('w', strtotime($fecha))){ 
	 case 0: $dia_nombre = "dom"; break; 
	 case 1: $dia_nombre = "lun"; break; 
	 case 2: $dia_nombre = "mar"; break; 
	 case 3: $dia_nombre = "mie"; break; 
	 case 4: $dia_nombre = "jue"; break; 
	 case 5: $dia_nombre = "vier"; break; 
	 case 6: $dia_nombre = "sab"; break; 
  }	 

  return $dia_nombre;	  
}	

function encryption($string){
	$ouput = FALSE;
	$key=hash('sha256', SECRET_KEY);
	$iv = substr(hash('sha256', SECRET_IV), 0, 16);
	$output = openssl_encrypt($string, METHOD, $key, 0, $iv);
	$output = base64_encode($output);

	return $output;
}

/*Funcion que permite desencriptar string*/
function decryption($string){
	$key = hash('sha256', SECRET_KEY);
	$iv = substr(hash('sha256', SECRET_IV), 0, 16);
	$output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);

	return $output;
}

/*Funcion que permite generar codigos aleatorios*/
function getRandom($word, $length, $number){
	for($i=1; $i<$length; $i++){
		$number = rand(0,9);
		$word .= $number;
	}

	return $word.$number; 
}

function testingMail($servidor, $correo, $contraseña, $puerto, $SMTPSecure, $CharSet){
	$cabeceras = "MIME-Version: 1.0\r\n";
	$cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$cabeceras .= "From: $correo \r\n";
			
	//incluyo la clase phpmailer	
	include_once("phpmailer/class.phpmailer.php");
	include_once("phpmailer/class.smtp.php");
		
	$mail = new PHPMailer(); //creo un objeto de tipo PHPMailer
	$mail->SMTPDebug = 1;
	$mail->IsSMTP(); //protocolo SMTP
	$mail->IsHTML(true);
	$mail->CharSet = $CharSet;
	$mail->SMTPAuth = true;//autenticación en el SMTP
	$mail->SMTPSecure = $SMTPSecure;
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);		
	$mail->Host = $servidor;//servidor de SMTP de gmail
	$mail->Port = $puerto;//puerto seguro del servidor SMTP de gmail
	$mail->From = $correo; //Remitente del correo
	$mail->FromName = $correo; //Remitente del correo
	$mail->AddAddress($correo);// Destinatario
	$mail->Username = $correo;//Aqui pon tu correo
	$mail->Password = $contraseña;//Aqui pon tu contraseña de gmail
	

	$mail->WordWrap = 50; //No. de columnas
			
	if($mail->SmtpConnect()){ //enviamos el correo por PHPMailer
		echo 1;//MENSAJE ENVIADO	   
	}else{
		echo 2;//MENSAJE NO ENVIADO	   
	}			   

}

function getRealIP(){
	if (isset($_SERVER["HTTP_CLIENT_IP"])){
		return $_SERVER["HTTP_CLIENT_IP"];
	}elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
		return $_SERVER["HTTP_X_FORWARDED"];
	}elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
		return $_SERVER["HTTP_FORWARDED_FOR"];
	}elseif (isset($_SERVER["HTTP_FORWARDED"])){
		return $_SERVER["HTTP_FORWARDED"];
	}else{
		return $_SERVER["REMOTE_ADDR"];
	}
}

function getUltimoDiaMes($año,$mes){
	$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));
	$dia2 = date('d', mktime(0,0,0, $mes, $dia, $año)); // ULTIMO DIA DEL MES
	
	return $dia2;
}

function getPrimerDiaMes($año,$mes){
	$dia = date("d", mktime(0,0,0, $mes+1, 0, $año));
	$dia2 = date('d', mktime(0,0,0, $mes, 1, $año)); //PRIMER DIA DEL MES
	
	return $dia2;
}	

function encrypt($string, $key) {
	$result = ''; $key=$key.'2015';
	for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
		  $result.=$char;
	}
	return base64_encode($result);
}

function decrypt($string, $key) {
	$result = ''; $key=$key.'2015';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
		  $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)-ord($keychar));
		  $result.=$char;
	}
	return $result;
}

function estado($estado){
	if($estado=='s'){
		return '<span class="label label-success">Activo</span>';
	}else{
		return '<span class="label label-important">No Activo</span>';
	}
}

function dias_pasados($fecha_inicial,$fecha_final){
  $dias = (strtotime($fecha_inicial)-strtotime($fecha_final))/86400;
  $dias = abs($dias); $dias = floor($dias);
  return $dias;
}

function mensajes($mensaje,$tipo){
	if($tipo=='verde'){
		$tipo='alert alert-success';
	}elseif($tipo=='rojo'){
		$tipo='alert alert-danger';
	}elseif($tipo=='azul'){
		$tipo='alert alert-info';
	}
	return '<div class="'.$tipo.'" align="center">
		  <button type="button" class="close" data-dismiss="alert">×</button>
		  <strong>'.$mensaje.'</strong>
		</div>';
}
	
function formato($valor){
	return number_format($valor,2,",",".");
}

function cambiarfecha_mysql($fecha){
  list($dia,$mes,$ano)=explode(" de ",$fecha);
  $fecha="$ano-$mes-$dia";
  return $fecha;
}	

function dias_transcurridos($fecha_i,$fecha_f){//Obtiene los dias transcurridos entre dos fechas
   $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
   $dias 	=  abs($dias); $dias = floor($dias);		
   return $dias;
}

function Bisiesto($anyo){
   if(!checkdate(02,29,$anyo)){
	  return false;
   }else{
	 return true;
   }
}  

function rellenarDigitos($valor, $long){
	$numero = str_pad($valor, $long, '0', STR_PAD_LEFT);
	
	return $numero;
}

function nombremes($mes){
  setlocale(LC_TIME, 'spanish');  
  $nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000)); 
  return $nombre;
}  

function nombre_mes_corto($mes){
   $dia_nombre = '';
   switch ($mes){ 
	 case 'enero': $dia_nombre = "ene"; break; 
	 case 'febrero': $dia_nombre = "feb"; break; 
	 case 'marzo': $dia_nombre = "mar"; break; 
	 case 'abril': $dia_nombre = "abr"; break; 
	 case 'mayo': $dia_nombre = "may"; break; 
	 case 'junio': $dia_nombre = "jun"; break; 
	 case 'julio': $dia_nombre = "jul"; break;
	 case 'agosto': $dia_nombre = "ago"; break; 
	 case 'septiembre': $dia_nombre = "sep"; break; 
	 case 'octubre': $dia_nombre = "oct"; break; 
	 case 'noviembre': $dia_nombre = "nov"; break; 
	 case 'diciembre': $dia_nombre = "dic"; break;		 
  }	 

  return $dia_nombre;	  
}


function connect(){
	$conexion = mysql_connect(SERVER, USER, PASS);
	mysql_select_db(DB, $conexion);
	mysql_query("SET NAMES 'utf8'");			
}

function connect_mysqli(){
	$mysqli=mysqli_connect(SERVER,USER,PASS,DB);
	
	$mysqli->set_charset("utf8");
	
	if ($mysqli->connect_errno) {
	   echo "Fallo al conectar a MySQL: " . $mysqli->connect_error;
	   exit;
	}
	
	return $mysqli;
}
	
//FUNCION QUE PERMITE GENERAR LA CONTRASEÑA DE FORMA AUTOMATICA
function generar_password_complejo(){
   $largo = 12;
   $cadena_base =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
   $cadena_base .= '0123456789' ;
   $cadena_base .= '!@#%^&()_,./<>?;:[]{}\|=+|*-';

   $password = '';
   $limite = strlen($cadena_base) - 1;

   for ($i=0; $i < $largo; $i++)
	   $password .= $cadena_base[rand(0, $limite)];

   return $password;
}


function historial(){
	$mysqli = connect_mysqli(); 
	$correlativo = "SELECT MAX(historial_id)+ 1 AS 'total' FROM historial;";
	$result = $mysqli->query($correlativo);
	
	$correlativo2 = $result->fetch_assoc();

	$numero = $correlativo2['total'];
   
	return $numero;
}

function correlativo($campo_id, $tabla){
	$mysqli = connect_mysqli(); 
	$correlativo = "SELECT MAX(".$campo_id.") AS max, COUNT(".$campo_id.") AS count FROM ".$tabla;

	$result = $mysqli->query($correlativo);	
	$correlativo2 = $result->fetch_assoc();
	$numero = $correlativo2['max'];
	$cantidad = $correlativo2['count'];

	if ( $cantidad == 0 )
	   $numero = 1;
	else
	   $numero = $numero + 1;

	return $numero;
}

function ejecutar($url){
  trim($url);
  return $url;
}
   
function getAgendatime($consultarJornadaJornada_id, $servicio, $consultar_colaborador_puesto_id, $consulta_agenda_id, $hora_h, $consulta_nuevos_devuelto, $consultarJornadaNuevos, $consultaJornadaTotal, $consulta_subsiguientes_devuelto){

   //USUARIO NUEVO
   if($consulta_agenda_id == ""){
	   $colores = "#008000"; //VERDE USUARIOS NUEVOS
   }else{
	   $colores = "#0071c5"; //AZUL OSCURO USUARIOS SUBSIGUIENTES		   
   }
   
   //INICIO EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL	
   $limite = $consultaJornadaTotal - $consulta_nuevos_devuelto;
   $hora = '';
   $hora_ = '';
   
	if($consultarJornadaJornada_id != ""){//INICIO PARA EVALUAR QUE EXISTA REGISTRO DEL COLABORADOR EN LA ENTIDAD jornada_colaborador
		if($consultarJornadaJornada_id == 1){//INICIO JORNADA MATUTINA
			//EVALUAMOS LA CANTIDAD DE USUARIOS DISPONIBLES PARA AGENDAR;	
			if($consulta_nuevos_devuelto > $consultarJornadaNuevos){//EXCEDER EL LIMITE DE NUEVOS
				$hora = "NuevosExcede";
			}else if($consulta_subsiguientes_devuelto > $limite){//EXCEDER EL LIMITE DE SUBSIGUIENTES
				$hora = "SubsiguienteExcede"; 
			}else{//INICIO EVALUAR HORARIOS
				if ($hora_h >= date('H:i',strtotime('8:00')) && $hora_h < date('H:i',strtotime('9:00'))){
					 $hora = "08:00";
				}else if ($hora_h >= date('H:i',strtotime('9:00')) && $hora_h < date('H:i',strtotime('10:00'))){
					$hora = "09:00";
				}else if ($hora_h >= date('H:i',strtotime('10:00')) && $hora_h < date('H:i',strtotime('11:00'))){
					$hora = "10:00";
				}else if ($hora_h >= date('H:i',strtotime('11:00')) && $hora_h < date('H:i',strtotime('12:00'))){
					$hora = "11:00"; 
				}else if ($hora_h >= date('H:i',strtotime('12:00')) && $hora_h < date('H:i',strtotime('13:00'))){
					$hora = "12:00";
				}else if ($hora_h >= date('H:i',strtotime('13:00')) && $hora_h < date('H:i',strtotime('14:00'))){
					$hora = "13:00";
				}else if($hora_h >= date('H:i',strtotime('14:00'))){
					$hora = 'NulaSError';
				}
			}//FIN EVALUAR HORARIOS
		}
		
		if($consultarJornadaJornada_id == 2){//INICIO JORNADA TARDE
			if($consulta_nuevos_devuelto > $consultarJornadaNuevos){//EXCEDER EL LIMITE DE NUEVOS
				$hora = "NuevosExcede";
			}else if($consulta_subsiguientes_devuelto > $limite){//EXCEDER EL LIMITE DE SUBSIGUIENTES
				$hora = 'NulaSError'; 
			}else{//INICIO EVALUAR HORARIOS
				if ($hora_h >= date('H:i',strtotime('08:00')) && $hora_h < date('H:i',strtotime('14:00'))){
					 $hora = 'NulaSError';
				}else if ($hora_h >= date('H:i',strtotime('14:00')) && $hora_h < date('H:i',strtotime('15:00'))){
					 $hora = "14:00";
				}else if ($hora_h >= date('H:i',strtotime('15:00')) && $hora_h < date('H:i',strtotime('16:00'))){
					 $hora = "15:00";
				}else if ($hora_h >= date('H:i',strtotime('16:00')) && $hora_h < date('H:i',strtotime('17:00'))){
					$hora = "16:00";
				}else if ($hora_h >= date('H:i',strtotime('17:00')) && $hora_h < date('H:i',strtotime('18:00'))){
					$hora = "17:00";
				}else if ($hora_h >= date('H:i',strtotime('18:00')) && $hora_h < date('H:i',strtotime('19:00'))){
					$hora = "18:00"; 
				}else if ($hora_h >= date('H:i',strtotime('19:00')) && $hora_h < date('H:i',strtotime('20:00'))){
					$hora = "19:00";
				}else if ($hora_h >= date('H:i',strtotime('20:00')) && $hora_h < date('H:i',strtotime('21:00'))){
					$hora = "20:00";
				}else if($hora_h >= date('H:i',strtotime('21:00'))){
					$hora = 'NulaSError';
				}
			}//FIN EVALUAR HORARIOS			
		}
		
		if($consultarJornadaJornada_id == 3){//INICIO JORNADA DIURNA
			if($consulta_nuevos_devuelto > $consultarJornadaNuevos){//EXCEDER EL LIMITE DE NUEVOS
				$hora = "NuevosExcede";
			}else if($consulta_subsiguientes_devuelto > $limite){//EXCEDER EL LIMITE DE SUBSIGUIENTES
				$hora = "SubsiguienteExcede"; 
			}else{//INICIO EVALUAR HORARIOS
				if ($hora_h >= date('H:i',strtotime('08:00')) && $hora_h < date('H:i',strtotime('20:00'))){
					 $hora = 'NulaSError';
				}else if ($hora_h >= date('H:i',strtotime('20:00')) && $hora_h < date('H:i',strtotime('21:00'))){
					 $hora = "20:00";
				}else if ($hora_h >= date('H:i',strtotime('21:00')) && $hora_h < date('H:i',strtotime('22:00'))){
					$hora = "21:00";
				}else if ($hora_h >= date('H:i',strtotime('22:00')) && $hora_h < date('H:i',strtotime('23:00'))){
					$hora = "22:00";
				}else if ($hora_h >= date('H:i',strtotime('23:00')) && $hora_h < date('H:i',strtotime('00:00'))){
					$hora = "23:00"; 
				}else if ($hora_h >= date('H:i',strtotime('00:00')) && $hora_h < date('H:i',strtotime('01:00'))){
					$hora = "00:00";
				}else if ($hora_h >= date('H:i',strtotime('01:00')) && $hora_h < date('H:i',strtotime('02:00'))){
					$hora = "01:00";
				}else if($hora_h == date('H:i',strtotime('02:00')) ){//12VO USUARIO SUBSIGUIENTE
					$hora = 'NulaSError';
				}
			}//FIN EVALUAR HORARIOS			
		}	

		if($consultarJornadaJornada_id == 4){//INICIO JORNADA NOCTURNA
			if($consulta_nuevos_devuelto > $consultarJornadaNuevos){//EXCEDER EL LIMITE DE NUEVOS
				$hora = "NuevosExcede";
			}else if($consulta_subsiguientes_devuelto > $limite){//EXCEDER EL LIMITE DE SUBSIGUIENTES
				$hora = "SubsiguienteExcede"; 
			}else{//INICIO EVALUAR HORARIOS
				if ($hora_h >= date('H:i',strtotime('08:00')) && $hora_h < date('H:i',strtotime('02:00'))){
					 $hora = 'NulaSError';
				}else if ($hora_h >= date('H:i',strtotime('02:00')) && $hora_h < date('H:i',strtotime('03:00'))){
					 $hora = "02:00";
				}else if ($hora_h >= date('H:i',strtotime('03:00')) && $hora_h < date('H:i',strtotime('04:00'))){
					$hora = "03:00";
				}else if ($hora_h >= date('H:i',strtotime('04:00')) && $hora_h < date('H:i',strtotime('05:00'))){
					$hora = "04:00";
				}else if ($hora_h >= date('H:i',strtotime('05:00')) && $hora_h < date('H:i',strtotime('06:00'))){
					$hora = "05:00"; 
				}else if ($hora_h >= date('H:i',strtotime('06:00')) && $hora_h < date('H:i',strtotime('07:00'))){
					$hora = "06:00";
				}else if ($hora_h >= date('H:i',strtotime('07:00')) && $hora_h < date('H:i',strtotime('08:00'))){
					$hora = "07:00";
				}else if($hora_h == date('H:i',strtotime('08:00')) ){//12VO USUARIO SUBSIGUIENTE
					$hora = 'NulaSError';
				}
			}//FIN EVALUAR HORARIOS			
		}				
		
		if($consultarJornadaJornada_id == 5){//INICIO JORNADA TODO EL DIA
			if($consulta_nuevos_devuelto > $consultarJornadaNuevos){//EXCEDER EL LIMITE DE NUEVOS
				$hora = "NuevosExcede";
			}else if($consulta_subsiguientes_devuelto > $limite){//EXCEDER EL LIMITE DE SUBSIGUIENTES
				$hora = "SubsiguienteExcede"; 
			}else{//INICIO EVALUAR HORARIOS
				if ($hora_h >= date('H:i',strtotime('8:00')) && $hora_h < date('H:i',strtotime('9:00'))){
					 $hora = "08:00";
				}else if ($hora_h >= date('H:i',strtotime('9:00')) && $hora_h < date('H:i',strtotime('10:00'))){
					$hora = "09:00";
				}else if ($hora_h >= date('H:i',strtotime('10:00')) && $hora_h < date('H:i',strtotime('11:00'))){
					$hora = "10:00";
				}else if ($hora_h >= date('H:i',strtotime('11:00')) && $hora_h < date('H:i',strtotime('12:00'))){
					$hora = "11:00"; 
				}else if ($hora_h >= date('H:i',strtotime('12:00')) && $hora_h < date('H:i',strtotime('13:00'))){
					$hora = "12:00";
				}else if ($hora_h >= date('H:i',strtotime('13:00')) && $hora_h < date('H:i',strtotime('14:00'))){
					$hora = "13:00";
				}else if ($hora_h >= date('H:i',strtotime('14:00')) && $hora_h < date('H:i',strtotime('15:00'))){
					$hora = "14:00";
				}else if ($hora_h >= date('H:i',strtotime('15:00')) && $hora_h < date('H:i',strtotime('16:00'))){
					$hora = "15:00";
				}else if ($hora_h >= date('H:i',strtotime('16:00')) && $hora_h < date('H:i',strtotime('17:00'))){
					$hora = "16:00"; 
				}else if ($hora_h >= date('H:i',strtotime('17:00')) && $hora_h < date('H:i',strtotime('18:00'))){
					$hora = "17:00";
				}else if ($hora_h >= date('H:i',strtotime('18:00')) && $hora_h < date('H:i',strtotime('19:00'))){
					$hora = "18:00";
				}else if ($hora_h >= date('H:i',strtotime('19:00')) && $hora_h < date('H:i',strtotime('20:00'))){
					$hora = "19:00";
				}else if ($hora_h >= date('H:i',strtotime('20:00')) && $hora_h < date('H:i',strtotime('21:00'))){
					$hora = "20:00";
				}else if ($hora_h >= date('H:i',strtotime('21:00')) && $hora_h < date('H:i',strtotime('22:00'))){
					$hora = "21:00"; 
				}else if ($hora_h >= date('H:i',strtotime('22:00')) && $hora_h < date('H:i',strtotime('23:00'))){
					$hora = "22:00";
				}else if ($hora_h >= date('H:i',strtotime('23:00')) && $hora_h < date('H:i',strtotime('23:59'))){
					$hora = "23:00";
				}else if ($hora_h >= date('H:i',strtotime('00:00')) && $hora_h < date('H:i',strtotime('01:00'))){
					$hora = "00:00";
				}else if ($hora_h >= date('H:i',strtotime('01:00')) && $hora_h < date('H:i',strtotime('02:00'))){
					$hora = "01:00";
				}else if ($hora_h >= date('H:i',strtotime('02:00')) && $hora_h < date('H:i',strtotime('03:00'))){
					$hora = "02:00"; 
				}else if ($hora_h >= date('H:i',strtotime('03:00')) && $hora_h < date('H:i',strtotime('04:00'))){
					$hora = "03:00";
				}else if ($hora_h >= date('H:i',strtotime('04:00')) && $hora_h < date('H:i',strtotime('05:00'))){
					$hora = "04:00";
				}else if ($hora_h >= date('H:i',strtotime('05:00')) && $hora_h < date('H:i',strtotime('06:00'))){
					$hora = "05:00";
				}else if ($hora_h >= date('H:i',strtotime('06:00')) && $hora_h < date('H:i',strtotime('07:00'))){
					$hora = "06:00";
				}else if ($hora_h >= date('H:i',strtotime('07:00')) && $hora_h < date('H:i',strtotime('08:00'))){
					$hora = "07:00"; 
				}
			}//FIN EVALUAR HORARIOS				
		}
	}else{
	   $hora = "Vacio"; //EL PROFESIONAL NO TIENE ASIGNADA UNA JORNADA LABORAL, O SIMPLEMENTE NO TIENE UN SERVICIO ASIGNADO, NO SE LE PUEDEN AGENDAR USUARIOS
	   $colores = "";			
	}

$datos = array(
	"hora" => $hora, 
	"colores" => $colores
);
	
return $datos;
//FIN EVALUACIÓN HORARIOS PARA LOS SERVICIOS SEGUN PROFESIONAL	   
}
   
function getEdad($fecha_de_nacimiento){
  $fecha_actual = date ("Y-m-d"); 

  // separamos en partes las fechas 
  $array_nacimiento = explode ( "-", $fecha_de_nacimiento ); 
  $array_actual = explode ( "-", $fecha_actual ); 

  $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años 
  $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
  $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días 

  //ajuste de posible negativo en $días 
  if ($dias < 0) { 
	--$meses; 

	//ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual  
	switch ($array_actual[1]) { 
	   case 1:     $dias_mes_anterior=31; break; 
	   case 2:     $dias_mes_anterior=31; break; 
	   case 3:  
			if (bisiesto($array_actual[0])){ 
				$dias_mes_anterior=29; break; 
			} else { 
				$dias_mes_anterior=28; break; 
			} 
	   case 4:     $dias_mes_anterior=31; break; 
	   case 5:     $dias_mes_anterior=30; break; 
	   case 6:     $dias_mes_anterior=31; break; 
	   case 7:     $dias_mes_anterior=30; break; 
	   case 8:     $dias_mes_anterior=31; break; 
	   case 9:     $dias_mes_anterior=31; break; 
	   case 10:    $dias_mes_anterior=30; break; 
	   case 11:    $dias_mes_anterior=31; break; 
	   case 12:    $dias_mes_anterior=30; break; 
	} 

	$dias=$dias + $dias_mes_anterior; 
 } 

 //ajuste de posible negativo en $meses 
 if ($meses < 0){ 
   --$anos; 
   $meses=$meses + 12; 
 } 

 $datos = array(
	 "anos" => $anos, 
	 "meses" => $meses,
	 "dias" => $dias
 );
	
 return $datos;	
}

function historial_acceso($comentario, $nombre_host, $colaborador_id){
	$mysqli = connect_mysqli(); 
	$fecha = date("Y-m-d H:i:s");

   //OBTENER CORRELATIVO
   $correlativo= "SELECT MAX(acceso_id) AS max, COUNT(acceso_id) AS count 
	  FROM historial_acceso";
	 $result = $mysqli->query($correlativo);
	 $correlativo2 = $result->fetch_assoc();

	 $numero = $correlativo2['max'];
	 $cantidad = $correlativo2['count'];

	 if ( $cantidad == 0 )
		$numero = 1;
	 else
	   $numero = $numero + 1;		
   
   //CONSULTAR REGISTRO
	$consultar_registro = "SELECT acceso_id 
		 FROM historial_acceso 
		 WHERE acceso_id = '$numero'"; 
   $result_acceso = $mysqli->query($consultar_registro);
  
   if($result_acceso->num_rows==0){
	 $insert = "INSERT INTO historial_acceso 
		VALUES('$numero','$fecha','$colaborador_id','$nombre_host','$comentario')"; 
	 $mysqli->query($insert);		  
  }
  
  $result->free();//LIMPIAR RESULTADO
  $result_acceso->free();//LIMPIAR RESULTADO
}

function sendSMS($to, $mensaje){
   date_default_timezone_set('America/Tegucigalpa');
	   
   $from = consultarFrom();	   
   $apy_key = consultarApi_key();
   $send_at = date("Y-m-d H:i:s");

   $request = '{
	  "api_key":"'.$apy_key.'",
	  "concat":1,
	  "messages":[
		{
		   "from":"'.$from.'",
		   "to":"'.$to.'",
		   "text":"'.$mensaje.'",
		   "send_at":"'.$send_at.'"
		}
	]
   }';

   $headers = array('Content-Type: application/json');
   $ch = curl_init('https://api.gateway360.com/api/3.0/sms/send');
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

   $result = curl_exec($ch);
   
   if (curl_errno($ch) != 0 ){
	  die("curl error: ".curl_errno($ch));
   }
   
   return $result;
}

function consultarFrom(){
   $mysqli = connect_mysqli(); 
   
   //CONSULTAR CONEXION SMS UP
   $query_sms = "SELECT * FROM sms_up";
   $result = $mysqli->query($query_sms);
   $correlativo2 = $result->fetch_assoc();
	
   $from = $correlativo2['from_'];	   
   
   return $from;
}

function consultarApi_key(){
   $mysqli = connect_mysqli(); 
   
   //CONSULTAR CONEXION SMS UP
   $query_sms = "SELECT * FROM sms_up";
   $result = $mysqli->query($query_sms);
   $correlativo2 = $result->fetch_assoc();
   
   $apy_key = $correlativo2['api_key'];	     
   
   return $apy_key;
}   

/*INICIO CONVERTIR NUMEROS A LETRAS*/
function unidad($numuero){
	switch ($numuero){
		case 9:{
			$numu = "NUEVE";
			break;
		}
		case 8:{
			$numu = "OCHO";
			break;
		}
		case 7:{
			$numu = "SIETE";
			break;
		}
		case 6:{
			$numu = "SEIS";
			break;
		}
		case 5:{
			$numu = "CINCO";
			break;
		}
		case 4:{
			$numu = "CUATRO";
			break;
		}
		case 3:{
			$numu = "TRES";
			break;
		}
		case 2:{
			$numu = "DOS";
			break;
		}
		case 1:{
			$numu = "UNO";
			break;
		}
		case 0:{
			$numu = "";
			break;
		}
	}
	return $numu;
}

function decena($numdero){
	if ($numdero >= 90 && $numdero <= 99){
		$numd = "NOVENTA ";
		if ($numdero > 90)
			$numd = $numd."Y ".(unidad($numdero - 90));
	}
	else if ($numdero >= 80 && $numdero <= 89){
		$numd = "OCHENTA ";
		if ($numdero > 80)
			$numd = $numd."Y ".(unidad($numdero - 80));
	}
	else if ($numdero >= 70 && $numdero <= 79){
		$numd = "SETENTA ";
		if ($numdero > 70)
		$numd = $numd."Y ".(unidad($numdero - 70));
	}
	else if ($numdero >= 60 && $numdero <= 69){
		$numd = "SESENTA ";
		if ($numdero > 60)
		$numd = $numd."Y ".(unidad($numdero - 60));
	}
	else if ($numdero >= 50 && $numdero <= 59){
		$numd = "CINCUENTA ";
		if ($numdero > 50)
		$numd = $numd."Y ".(unidad($numdero - 50));
	}
	else if ($numdero >= 40 && $numdero <= 49){
		$numd = "CUARENTA ";
		if ($numdero > 40)
		$numd = $numd."Y ".(unidad($numdero - 40));
	}
	else if ($numdero >= 30 && $numdero <= 39){
		$numd = "TREINTA ";
		if ($numdero > 30)
		$numd = $numd."Y ".(unidad($numdero - 30));
	}
	else if ($numdero >= 20 && $numdero <= 29){
		if ($numdero == 20)
		$numd = "VEINTE ";
		else
		$numd = "VEINTI".(unidad($numdero - 20));
	}
	else if ($numdero >= 10 && $numdero <= 19)
	{
		switch ($numdero){
			case 10:{
				$numd = "DIEZ ";
				break;
			}
			case 11:{
				$numd = "ONCE ";
				break;
			}
			case 12:{
				$numd = "DOCE ";
				break;
			}
			case 13:{
				$numd = "TRECE ";
				break;
			}
			case 14:{
				$numd = "CATORCE ";
				break;
			}
			case 15:{
				$numd = "QUINCE ";
				break;
			}
			case 16:{
				$numd = "DIECISEIS ";
				break;
			}
			case 17:{
				$numd = "DIECISIETE ";
				break;
			}
			case 18:{
				$numd = "DIECIOCHO ";
				break;
			}
			case 19:{
				$numd = "DIECINUEVE ";
				break;
			}
		}
	}
	else
		$numd = unidad($numdero);
	return $numd;
}

function centena($numc){
	if ($numc >= 100){
		if ($numc >= 900 && $numc <= 999){
			$numce = "NOVECIENTOS ";
		if ($numc > 900)
			$numce = $numce.(decena($numc - 900));
		}
		else if ($numc >= 800 && $numc <= 899){
			$numce = "OCHOCIENTOS ";
			if ($numc > 800)
				$numce = $numce.(decena($numc - 800));
		}
		else if ($numc >= 700 && $numc <= 799){
			$numce = "SETECIENTOS ";
			if ($numc > 700)
				$numce = $numce.(decena($numc - 700));
		}
		else if ($numc >= 600 && $numc <= 699){
			$numce = "SEISCIENTOS ";
			if ($numc > 600)
				$numce = $numce.(decena($numc - 600));
		}
		else if ($numc >= 500 && $numc <= 599){
			$numce = "QUINIENTOS ";
			if ($numc > 500)
				$numce = $numce.(decena($numc - 500));
		}
		else if ($numc >= 400 && $numc <= 499){
			$numce = "CUATROCIENTOS ";
			if ($numc > 400)
				$numce = $numce.(decena($numc - 400));
		}
		else if ($numc >= 300 && $numc <= 399){
			$numce = "TRESCIENTOS ";
			if ($numc > 300)
				$numce = $numce.(decena($numc - 300));
		}
		else if ($numc >= 200 && $numc <= 299){
			$numce = "DOSCIENTOS ";
			if ($numc > 200)
				$numce = $numce.(decena($numc - 200));
		}
		else if ($numc >= 100 && $numc <= 199){
			if ($numc == 100)
			$numce = "CIEN ";
			else
				$numce = "CIENTO ".(decena($numc - 100));
		}
	}
	else
		$numce = decena($numc);
		return $numce;
}

function miles($nummero){
	if ($nummero >= 1000 && $nummero < 2000){
		$numm = "MIL ".(centena($nummero%1000));
	}
	if ($nummero >= 2000 && $nummero <10000){
		$numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
	}
	if ($nummero < 1000)
		$numm = centena($nummero);

	return $numm;
}

function decmiles($numdmero){
	if ($numdmero == 10000)
		$numde = "DIEZ MIL";
	if ($numdmero > 10000 && $numdmero <20000){
		$numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));
	}
	if ($numdmero >= 20000 && $numdmero <100000){
		$numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));
	}
	if ($numdmero < 10000)
		$numde = miles($numdmero);

	return $numde;
}

function cienmiles($numcmero){
	if ($numcmero == 100000)
		$num_letracm = "CIEN MIL";
	if ($numcmero >= 100000 && $numcmero <1000000){
		$num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));
	}
	if ($numcmero < 100000)
		$num_letracm = decmiles($numcmero);
	return $num_letracm;
}

function millon($nummiero){
	if ($nummiero >= 1000000 && $nummiero <2000000){
		$num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero >= 2000000 && $nummiero <10000000){
		$num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
	}
	if ($nummiero < 1000000)
		$num_letramm = cienmiles($nummiero);

	return $num_letramm;
}

function decmillon($numerodm){
	if ($numerodm == 10000000)
		$num_letradmm = "DIEZ MILLONES";
	if ($numerodm > 10000000 && $numerodm <20000000){
		$num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));
	}
	if ($numerodm >= 20000000 && $numerodm <100000000){
		$num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));
	}
	if ($numerodm < 10000000)
		$num_letradmm = millon($numerodm);

	return $num_letradmm;
}

function cienmillon($numcmeros){
	if ($numcmeros == 100000000)
		$num_letracms = "CIEN MILLONES";
	if ($numcmeros >= 100000000 && $numcmeros <1000000000){
		$num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
	}
	if ($numcmeros < 100000000)
		$num_letracms = decmillon($numcmeros);
	return $num_letracms;
}

function milmillon($nummierod){
	if ($nummierod >= 1000000000 && $nummierod <2000000000){
		$num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod >= 2000000000 && $nummierod <10000000000){
		$num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
	}
	if ($nummierod < 1000000000)
		$num_letrammd = cienmillon($nummierod);

	return $num_letrammd;
}

/*Funcion que permite limpiar valores de los string (Inyección SQL)*/
function cleanString($string){
	//Limpia espacios al inicio y al final
	$string =  trim($string);

	//Quita las barras de un string con comillas escapadas
	$string = stripslashes($string); 

	//Limpiar etiquetas de JavaScript o Instrucciones SQL entre otros
	$string = str_ireplace("<script>", "", $string);
	$string = str_ireplace("</script>", "", $string);
	$string = str_ireplace("<script src>", "", $string);
	$string = str_ireplace("<script type>", "", $string);
	$string = str_ireplace("SELECT * FROM", "", $string);
	$string = str_ireplace("DELETE FROM", "", $string);
	$string = str_ireplace("INSERT INTO", "", $string);
	$string = str_ireplace("UPDATE", "", $string);
	$string = str_ireplace("--", "", $string);
	$string = str_ireplace("^", "", $string);
	$string = str_ireplace("]", "", $string);
	$string = str_ireplace("[", "", $string);  
	$string = str_ireplace("{", "", $string);
	$string = str_ireplace("}", "", $string);               
	$string = str_ireplace("==", "", $string);
	$string = str_ireplace("'", "", $string);		
	
	return $string;
}

function cleanStringStrtolower($string){
	//Limpia espacios al inicio y al final
	$string =  strtolower(trim($string));

	//Quita las barras de un string con comillas escapadas
	$string = stripslashes($string); 

	//Limpiar etiquetas de JavaScript o Instrucciones SQL entre otros
	$string = str_ireplace("<script>", "", $string);
	$string = str_ireplace("</script>", "", $string);
	$string = str_ireplace("<script src>", "", $string);
	$string = str_ireplace("<script type>", "", $string);
	$string = str_ireplace("SELECT * FROM", "", $string);
	$string = str_ireplace("DELETE FROM", "", $string);
	$string = str_ireplace("INSERT INTO", "", $string);
	$string = str_ireplace("UPDATE", "", $string);
	$string = str_ireplace("--", "", $string);
	$string = str_ireplace("^", "", $string);
	$string = str_ireplace("]", "", $string);
	$string = str_ireplace("[", "", $string);  
	$string = str_ireplace("{", "", $string);
	$string = str_ireplace("}", "", $string);               
	$string = str_ireplace("==", "", $string);
	$string = str_ireplace("'", "", $string);
	$string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");		
	
	return $string;
}	

function cleanStringStrtoupper($string){
	//Limpia espacios al inicio y al final
	$string =  strtoupper(trim($string));

	//Quita las barras de un string con comillas escapadas
	$string = stripslashes($string); 

	//Limpiar etiquetas de JavaScript o Instrucciones SQL entre otros
	$string = str_ireplace("<script>", "", $string);
	$string = str_ireplace("</script>", "", $string);
	$string = str_ireplace("<script src>", "", $string);
	$string = str_ireplace("<script type>", "", $string);
	$string = str_ireplace("SELECT * FROM", "", $string);
	$string = str_ireplace("DELETE FROM", "", $string);
	$string = str_ireplace("INSERT INTO", "", $string);
	$string = str_ireplace("UPDATE", "", $string);
	$string = str_ireplace("--", "", $string);
	$string = str_ireplace("^", "", $string);
	$string = str_ireplace("]", "", $string);
	$string = str_ireplace("[", "", $string);  
	$string = str_ireplace("{", "", $string);
	$string = str_ireplace("}", "", $string);               
	$string = str_ireplace("==", "", $string);
	$string = str_ireplace("'", "", $string);		
	
	return $string;
}			

function cleanStringConverterCase($string){
	//Limpia espacios al inicio y al final
	$string =  mb_convert_case(trim($string), MB_CASE_TITLE, "UTF-8");

	//Quita las barras de un string con comillas escapadas
	$string = stripslashes($string); 

	//Limpiar etiquetas de JavaScript o Instrucciones SQL entre otros
	$string = str_ireplace("<script>", "", $string);
	$string = str_ireplace("</script>", "", $string);
	$string = str_ireplace("<script src>", "", $string);
	$string = str_ireplace("<script type>", "", $string);
	$string = str_ireplace("SELECT * FROM", "", $string);
	$string = str_ireplace("DELETE FROM", "", $string);
	$string = str_ireplace("INSERT INTO", "", $string);
	$string = str_ireplace("UPDATE", "", $string);
	$string = str_ireplace("--", "", $string);
	$string = str_ireplace("^", "", $string);
	$string = str_ireplace("]", "", $string);
	$string = str_ireplace("[", "", $string);  
	$string = str_ireplace("{", "", $string);
	$string = str_ireplace("}", "", $string);               
	$string = str_ireplace("==", "", $string);
	$string = str_ireplace("'", "", $string);  
	
	return $string;
}	

function eliminar_acentos($cadena){
	//Reemplazamos la A y a
	$cadena = str_replace(
	array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
	array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
	$cadena
	);

	//Reemplazamos la E y e
	$cadena = str_replace(
	array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
	array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
	$cadena );

	//Reemplazamos la I y i
	$cadena = str_replace(
	array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
	array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
	$cadena );

	//Reemplazamos la O y o
	$cadena = str_replace(
	array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
	array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
	$cadena );

	//Reemplazamos la U y u
	$cadena = str_replace(
	array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
	array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
	$cadena );

	//Reemplazamos la N, n, C y c
	$cadena = str_replace(
	array('Ñ', 'ñ', 'Ç', 'ç'),
	array('N', 'n', 'C', 'c'),
	$cadena
	);
	
	return $cadena;
}

function convertir($numero){
	$num = str_replace(",","",$numero);
	$num = number_format($num,2,'.','');
	$cents = substr($num,strlen($num)-2,strlen($num)-1);
	$num = (int)$num;

	$numf = milmillon($num);

	return $numf." CON ".$cents."/100";
}

//CONSULTA EN EL SERVIDOR DE KIREDS PARA VALIDAR QUE EL CLIENTE EXISTA
function connect_mysqli_main_server(){
	$mysqli_main =mysqli_connect(SERVER_MAIN,USER_MAIN,PASS_MAIN,DB_MAIN);

	$mysqli_main->set_charset("utf8");

	if ($mysqli_main->connect_errno) {
		echo "Fallo al conectar a MySQL: " . $mysqli->connect_error;
		exit;
	}

	return $mysqli_main;
}	
/*INICIO CONVERTIR NUMEROS A LETRAS*/   
?>