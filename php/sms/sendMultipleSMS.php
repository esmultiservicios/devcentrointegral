<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$fecha = $_POST['fecha'];
$servicio = $_POST['servicio'];
$send_at = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];

$dia_nombre = dia_nombre($fecha);
$mes=nombremes(date("m", strtotime($fecha)));
$dia = date("d", strtotime($fecha));
$año=date("Y", strtotime($fecha));

$where = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo <> 1 AND LENGTH(p.telefono) = 8";
$where1 = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo <> 1 AND LENGTH(p.telefono1) = 8";

$registro = "SELECT p.pacientes_id AS 'pacientes_id', p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora', a.colaborador_id AS 'colaborador_id'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id
    ".$where."
    UNION
    SELECT p.pacientes_id AS 'pacientes_id', p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono1 AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora', a.colaborador_id AS 'colaborador_id'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id	
    ".$where1."
    ORDER BY usuario";	

$result_agenda_cosnulta = $mysqli->query($registro);

if($result_agenda_cosnulta->num_rows > 0 ){
  if(!$sock = @fsockopen('www.google.com', 80)){ 
      echo 2;//NO HAY CONEXION A INTERNET
  }else{ 
     while($registro2 = $result_agenda_cosnulta->fetch_assoc()){
	   $telefono = substr($registro2['telefono'],0,1);
	   $mensaje = "";
	
	   /*EXTRAEMOS LOS DATOS DEL USUARIO*/
	   $nombre_ = explode(" ", $registro2['usuario_nombre']);
       $nombre_usuario = $nombre_[0];
	   $apellido_ = explode(" ", $registro2['usuario_apellido']);	
	   $nombre_apellido = $apellido_[0];
       $from = consultarFrom();
	   $area = "504";
	   $telefono = $registro2['telefono'];
	   $to = $area."".$telefono;
	   /********************************************************/
	   
	   $mensaje = "Estimado(a) $nombre_usuario $nombre_apellido, le recordamos que su cita es el $dia_nombre $dia de $mes, $año. Favor estar 15 minutos antes. Para mas detalles, PBX: +504 2512-0870";
	
	   $resultado = sendSMS($to, $mensaje);
	
	   $status = json_decode($resultado);
       $estado = $status->{'status'};
	
	   $campo_id = 'sms_id';
	   $tabla = 'sms';
	   $sms_id = correlativo($campo_id, $tabla);
	
	   //DATOS DEL USUARIO
	   $pacientes_id = $registro2['pacientes_id'];
	   $expediente = $registro2['expediente'];
	   $servicio_id = $registro2['servicio_id'];
	   $colaborador_id = $registro2['colaborador_id'];
	   $fecha = $registro2['fecha_cita'];
	
	   //GUARDAR VALORES DE LOS MENSAJES EN PHP
	   $query = "INSERT INTO sms 
	       VALUES('$sms_id','$pacientes_id','$expediente','$servicio_id','$colaborador_id','$from','$area','$telefono','$mensaje','$estado','$fecha','$send_at','$usuario')";
	   $result_query = $mysqli->query($query);
  }
  echo 1;//MENSAJE ENVIADO CON EXITO
}	
}else{
	echo 3; //NO HAY MENSAJES PARA ENVIAR;
}
?>