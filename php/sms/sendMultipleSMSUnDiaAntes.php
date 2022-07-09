<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$fecha = $_POST['fecha'];
$servicio = $_POST['servicio'];
$send_at = date("Y-m-d H:i:s");
$fecha_sistema = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

$dia_nombre = dia_nombre_corto($fecha);
$mes=nombre_mes_corto(nombremes(date("m", strtotime($fecha))));
$dia = date("d", strtotime($fecha));
$año=date("Y", strtotime($fecha));

$nombre_dia_fecha_sistema = dia_nombre($fecha_sistema);
$nombre_dia_fecha = dia_nombre($fecha);
$dia_sms = '';
$flag = false;
$saldo = 0;

if($nombre_dia_fecha_sistema == 'viernes' && $nombre_dia_fecha == 'lunes'){
  /*LA DIA DEL SISTEMA ES VIERNES Y LA FECHA SELECCIONADA ESD LUNES, SOLO HA PASADO UN DÍA YA QUE SABADO Y DOMNINGO NO SE LABORA, 
    ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES*/
     $dia_sms = 1;
}else{
	if(dias_pasados($fecha_sistema,$fecha) == 1){
	    $dia_sms = 1;//SOLO HA PASADO UN DIA DE LA FECHA DEL SISTEMA A LA FECHA ACTUAL, ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES
    }else{
		if($nombre_dia_fecha_sistema == 'viernes'){
	        /*LA DIA DEL SISTEMA ES VIERNES Y LA FECHA SELECCIONADA ESD LUNES, SOLO HA PASADO UN DÍA YA QUE SABADO Y DOMNINGO NO SE LABORA, 
	         ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES*/
	        $dia_sms = (dias_pasados($fecha_sistema,$fecha))-2;//HAN PASADO MAS DE UN DÍA DE LA FECHA ACTUAL, ESO SIGNIFICA QUE SE ENVIAN LOS MENSAJES DE MAS DE UN DÍA
        }else{
			$dia_sms = dias_pasados($fecha_sistema,$fecha)-2;
	    }
    }
}

/*PERMITE ENVIAR DOS SMS	
$where = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo = 2 AND LENGTH(p.telefono) = 8 AND p.telefono NOT LIKE '2%'";
$where1 = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo = 2 AND LENGTH(p.telefono1) = 8 AND p.telefono1 NOT LIKE '2%'";

$registro = "SELECT p.pacientes_id AS 'pacientes_id', p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora', a.colaborador_id AS 'colaborador_id', a.paciente AS 'paciente'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id
    ".$where."
    GROUP BY p.telefono
	UNION
    SELECT p.pacientes_id AS 'pacientes_id', p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono1 AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora', a.colaborador_id AS 'colaborador_id', a.paciente AS 'paciente'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id	
    ".$where1."
    GROUP BY p.telefono1
    ORDER BY usuario";
*/	

/*PERMITE ENVIAR UN SMS*/
$where = "WHERE a.servicio_id = '$servicio' AND CAST(a.fecha_cita AS DATE) = '$fecha' AND a.status = 0 AND confagenda.confirmo = 2 AND LENGTH(p.telefono) = 8 AND p.telefono NOT LIKE '2%'";

$registro = "SELECT p.pacientes_id AS 'pacientes_id', p.nombre AS 'usuario_nombre', p.apellido AS 'usuario_apellido', p.expediente AS 'expediente', CONCAT(p.nombre,' ',p.apellido) AS 'usuario', p.telefono AS 'telefono', CAST(a.fecha_cita AS DATE) AS 'fecha_cita', a.servicio_id AS 'servicio_id', a.hora AS 'hora', a.colaborador_id AS 'colaborador_id', a.paciente AS 'paciente'
    FROM agenda AS a
    INNER JOIN pacientes AS p
    ON a.pacientes_id = p.pacientes_id
	LEFT JOIN confirmacion_agenda AS confagenda
	ON a.agenda_id = confagenda.agenda_id
    ".$where."
    GROUP BY p.telefono";

$result_agenda_cosnsulta = $mysqli->query($registro);

//VERIFICAMOS SI HAY MENSAJES ENVIADOS EN ESTA FECHA
$query_consulta = "SELECT sms_id
   FROM sms
   WHERE fecha = '$fecha' AND servicio_id = '$servicio' AND dias = 1";
$result_consulta_registros = $mysqli->query($query_consulta);   

//CONSULTAR BALANCE
$query_balance_ = "SELECT balance
      FROM sms_up";
$result_balance_ = $mysqli->query($query_balance_);
$consulta_balance_ = $result_balance_->fetch_assoc();
$balance_ = $consulta_balance_['balance'];
$balance_total = 0; 

if($balance_ > 300){
if($result_consulta_registros->num_rows == 0 ){//CONSULTAMOS QUE NO HAY REGISTROS ALMACENADOS DE LOS SMS    
  if($result_agenda_cosnsulta->num_rows > 0 ){
    if(!$sock = @fsockopen('www.google.com', 80)){ 
        echo 2;//NO HAY CONEXION A INTERNET
    }else{ 
       while($registro2 = $result_agenda_cosnsulta->fetch_assoc()){ 
	     $telefono = substr($registro2['telefono'],0,1);
	     $mensaje = "";
	
	     //DATOS DEL USUARIO
	     $pacientes_id = $registro2['pacientes_id'];
	     $expediente = $registro2['expediente'];
	     $servicio_id = $registro2['servicio_id'];
	     $colaborador_id = $registro2['colaborador_id'];
	     $fecha = $registro2['fecha_cita'];
		 
	     /*EXTRAEMOS LOS DATOS DEL USUARIO*/
	     $nombre_ = explode(" ", $registro2['usuario_nombre']);
         $nombre_usuario = $nombre_[0];
	     $apellido_ = explode(" ", $registro2['usuario_apellido']);	
	     $nombre_apellido = $apellido_[0];
         $from = consultarFrom();
	     $area = "504";
	     $valor = SUBSTR($registro2['telefono'],0,1);//EXTRAEMOS EL PRIMIER DIGITO DE LOS NUMEROS PARA LUEGO EVALUAR SI ES UN NUMERO CELULAR
	     $tipo_paciente = $registro2['paciente'];
	     /********************************************************/
	   
	    if($valor == 9 || $valor == 8 || $valor == 7 || $valor == 3){//VERIFICAMOS QUE LOS NUMEROS SEAN CELULARES
		    $telefono = $registro2['telefono'];
	        $to = $area."".$telefono;  
	        $mensaje = "Estimado(a) $nombre_usuario $nombre_apellido, le recordamos, su cita es el $dia_nombre $dia de $mes, $año. Favor estar 15 minutos antes. Para mas detalles PBX: +504 2512-0870";
	
	        //VERIFICAR SI EL SMS SE HA ENVIADO AL USUARIO PARA NO DUPLICAR REGISTROS
			$query_envio_sms = "SELECT sms_id
			    FROM sms
				WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha' AND para = '$telefono' AND dias = 1";
            $result_agenda_envio_sms = $mysqli->query($query_envio_sms); 

	        $campo_id = 'sms_id';
	        $tabla = 'sms';
	        $sms_id = correlativo($campo_id, $tabla);
				 
            if($result_agenda_envio_sms->num_rows == 0){
	             $resultado = sendSMS($to, $mensaje);
	        
	             $status = json_decode($resultado);
                 $estado = $status->{'status'};
	
	             //GUARDAR VALORES DE LOS MENSAJES EN PHP
	             $insert = "INSERT INTO sms 
	                VALUES('$sms_id','$pacientes_id','$expediente','$servicio_id','$colaborador_id','$from','$area','$telefono','$mensaje','$estado','$fecha','$send_at','$usuario','$dia_sms','$tipo_paciente')";
						
	             $mysqli->query($insert);
				 				 
				 //CONSULTAR BALANCE
                 $query_balance = "SELECT balance
                    FROM sms_up";
                 $result_balance = $mysqli->query($query_balance);
                 $consulta_balance = $result_balance->fetch_assoc();
                 $balance = $consulta_balance['balance'];
				 
				 $balance_total = $balance - 1;
				 
				 //ACTUALIZAMOS BALANCE
				 $update = "UPDATE sms_up SET balance = '$balance_total'";
				 $mysqli->query($update);			 
			  }
			}			
      }

		echo 1;//MENSAJE ENVIADO CON EXITO  
    }	
  }else{
	  echo 3; //NO HAY MENSAJES PARA ENVIAR;
  }
}else{
	echo 4;//LO SENTIMOS YA HABIA ENVIADO LOS SMS PARA ESTA FECHA
}
  $saldo = $balance_;
}

if($saldo == 0){
  echo 5;//SALDO INSUFICIENTE
}

$mysqli->close();//CERRAR CONEXIÓN   
?>