<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

/*
$fecha = $_GET['fecha'];
$servicio = $_GET['servicio'];
*/

$fecha = '2019-12-12';
$servicio = 1;
$fecha_sistema = date("Y-m-d H:i:s");

switch (date('w', strtotime($fecha))){ 
    case 0: $dia_nombre = "Domingo"; break; 
    case 1: $dia_nombre = "Lunes"; break; 
    case 2: $dia_nombre = "Martes"; break; 
    case 3: $dia_nombre = "Miercoles"; break; 
    case 4: $dia_nombre = "Jueves"; break; 
    case 5: $dia_nombre = "Viernes"; break; 
    case 6: $dia_nombre = "Sabado"; break; 
} 

$mes=nombremes(date("m", strtotime($fecha)));
$dia = date("d", strtotime($fecha));
$año=date("Y", strtotime($fecha));

$where = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo <> 1";

$registro = "SELECT p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id
    ".$where."
    UNION
    SELECT p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono1 AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id	
    ".$where."
    ORDER BY usuario";	

$result = $mysqli->query($registro);

$messages_arreglo = "";//EN ESTA VARIABLE SE ALMACENARÁ EL ARREGLO QUE UTILIZA LA API PARA EL ENVIO DE SMS

//CICLO QUE RECORRE LA BASE DE DATOS CON LOS REGISTROS Y PREPARA EL ARRAY QUE UTILIZA LA API DE SMS
while($registro2 = $result->fetch_assoc()){
	$telefono = substr($registro2['telefono'],0,1);
	$mensaje = "";
	
	/*EXTRAEMOS LOS DATOS DEL USUARIO*/
	$nombre_ = explode(" ", $registro2['usuario_nombre']);
    $nombre_usuario = $nombre_[0];
	$apellido_ = explode(" ", $registro2['usuario_apellido']);	
	$nombre_apellido = $apellido_[0];
	/********************************************************/
		  
	if($telefono == 3 || $telefono == 8 || $telefono == 7){
		$mensaje .= "Hospital San Juan de Dios: Estimado (a) $nombre_usuario $nombre_apellido, le recordamos que su cita es el dia $dia_nombre $dia de $mes, $año. Favor estar 15 minutos antes."; 
	}else{
		$mensaje = "Hospital San Juan de Dios: Estimado (a) $nombre_usuario $nombre_apellido, le recordamos que su cita es el dia $dia_nombre $dia de $mes, $año. Favor estar 15 minutos antes.";
	}
	
	$messages_arreglo .='{
		"from":"HOSPSJDD",
        "to":"504'.$registro2['telefono'].'",
        "text":"'.$mensaje.'",
        "send_at":"'.$fecha_sistema.'"
	},';
}

$Message_myString = trim($messages_arreglo, ',');//QUITAMOS EL ULTIMO CARACTER DE LA CONSULTA, EN ESTE CASO UNA COMA (,)
/*$Message_myString ALMACENA EL ARRAY QUE SE USARA EN LA API PARA EL ENVIO DE SMS DE FORMA AUTOMATICA*/

echo $Message_myString;

/*		   
$apy_key = "9d206673c6682ff228b26cdb6a694dcf";

$request = '{
    "api_key":"'.$apy_key.'",
    "concat":1,
    "messages":[
        '.$Message_myString.'
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
}*/
?>