<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 

$agenda_id = $_POST['agenda_id'];
$to_ = $_POST['to'];
$mensaje = $_POST['text'];
$send_at = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];
$from = consultarFrom();
$area = '504';
$saldo = 0;

//CONSULTAR DATOS DEL USUARIO EN LA AGENDA 
$query_datos_agenda = "SELECT expediente, colaborador_id, CAST(fecha_cita AS DATE) AS 'fecha_cita', servicio_id, pacientes_id, paciente
     FROM agenda
	 WHERE agenda_id = '$agenda_id'";
	 	
$result = $mysqli->query($query_datos_agenda);	
$registro2 = $result->fetch_assoc();
 
$pacientes_id = $registro2['pacientes_id']; 
$expediente = $registro2['expediente'];
$colaborador_id = $registro2['colaborador_id'];
$servicio_id = $registro2['servicio_id'];
$fecha = $registro2['fecha_cita'];
$dias = 0;
$tipo_paciente = $registro2['paciente'];

$telefono_consultado = explode(",", $to_);

//CONSULTAR BALANCE
$query_balance_ = "SELECT balance
      FROM sms_up";
$result_balance_ = $mysqli->query($query_balance_);
$consulta_balance_ = $result_balance_->fetch_assoc();
$balance_ = $consulta_balance_['balance'];
$balance_total = 0; 

if($balance_ > 5){
   if(!$sock = @fsockopen('www.google.com', 80)){ 
      echo 2;//NO HAY CONEXIÓN A INTERNET
   }else{
       for($i = 0; $i<sizeof($telefono_consultado); $i++){
	      $valor = SUBSTR($telefono_consultado[$i],0,1);//EXTRAEMOS EL PRIMIER DIGITO DE LOS NUMEROS PARA LUEGO EVALUAR SI ES UN NUMERO CELULAR

	      if($valor == 9 || $valor == 8 || $valor == 7 || $valor == 3){//VERIFICAMOS QUE LOS NUMEROS SEAN CELULARES
		       $to = $area.''.$telefono_consultado[$i];
	           $telefono = $telefono_consultado[$i];
			   
			   //VERIFICAR SI EL SMS SE HA ENVIADO AL USUARIO PARA NO DUPLICAR REGISTROS
			   $query_envio_sms = "SELECT sms_id
			       FROM sms
				   WHERE pacientes_id = '$pacientes_id' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id' AND fecha = '$fecha' AND para = '$telefono'";
               $result_agenda_envio_sms = $mysqli->query($query_envio_sms);  
	           
			   if($result_agenda_envio_sms->num_rows == 0){
			      $resultado = sendSMS($to, $mensaje);
	           
			      $status = json_decode($resultado);
                  $estado = $status->{'status'};
	           
			      $campo_id = 'sms_id';
	              $tabla = 'sms';
	              $sms_id = correlativo($campo_id, $tabla);
	
                  //GUARDAR VALORES DE LOS MENSAJES EN PHP
                  $query = "INSERT INTO sms 
	                  VALUES('$sms_id','$pacientes_id','$expediente','$servicio_id','$colaborador_id','$from','$area','$telefono','$mensaje','$estado','$fecha','$send_at','$usuario','$dias','$tipo_paciente')";
                  $result_query = $mysqli->query($query); 

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
  $saldo = $balance_;
}

if($saldo == 0){
  echo 4;//SALDO INSUFICIENTE
}

$mysqli->close();//CERRAR CONEXIÓN 
?>