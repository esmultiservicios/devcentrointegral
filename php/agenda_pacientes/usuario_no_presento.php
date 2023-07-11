<?php
session_start();   
include "../funtions.php";;
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$agenda_id = $_POST['agenda_id'];
$comentario = cleanStringStrtolower($_POST['comentario']);
$usuario_sistema = $_SESSION['colaborador_id'];

//CONSULTAR PACIENTES_ID DE LA ENTIDAD PACIENTES
$consultar = "SELECT CAST(fecha_cita AS DATE) AS 'fecha' , expediente, pacientes_id, colaborador_id, servicio_id 
    FROM agenda 
	WHERE agenda_id = '$agenda_id'";
$result = $mysqli->query($consultar);
$consultar2 = $result->fetch_assoc();

$pacientes_id = "";
$colaborador_id = "";
$servicio_id = "";
$expediente = "";
$fecha = "";

if($result->num_rows>0){
	$pacientes_id = $consultar2['pacientes_id'];
	$colaborador_id = $consultar2['colaborador_id'];
	$servicio_id = $consultar2['servicio_id'];
	$expediente = $consultar2['expediente'];
	$fecha = $consultar2['fecha'];	
}

$fecha_registro = date("Y-m-d H:i:s");
$fecha_sistema = date("Y-m-d");
$usuario_sistema = $_SESSION['colaborador_id'];

//CONSULTAR EL NOMBRE DEL SERVICIO
$consulta_servicio = "SELECT nombre 
    FROM servicios 
	WHERE servicio_id = '$servicio_id'";
$result = $mysqli->query($consulta_servicio);
$consulta_servicio2 = $result->fetch_assoc();

$servicio_nombre = "";

if($result->num_rows>0){
	$servicio_nombre = $consulta_servicio2['nombre'];
}
//CONSULTAMOS DATOS DEL PACIENTES
$consulta_paciente = "SELECT *
	FROM pacientes 
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_paciente);	
$consulta_paciente2 = $result->fetch_assoc();

$expediente = "";
$identidad = "";
$usuario = "";
$nombre = "";
$apellido = "";
$fecha_nacimiento = "";
$telefono1 = "";
$telefono2 = "";
$sexo = "";
$localidades = "";
$departamento = "";
$municipio = "";
$correo = "";
$consulta_paciente_status = "";

if($result->num_rows>0){
	$expediente = $consulta_paciente2['expediente'];
	$identidad = $consulta_paciente2['identidad'];
	$usuario = $consulta_paciente2['usuario'];
	$nombre = $consulta_paciente2['nombre'];
	$apellido = $consulta_paciente2['apellido'];
	$fecha_nacimiento = $consulta_paciente2['fecha_nacimiento'];
	$telefono1 = $consulta_paciente2['telefono1'];
	$telefono2 = $consulta_paciente2['telefono2'];
	$sexo = $consulta_paciente2['genero'];
	$localidades = $consulta_paciente2['localidad'];
	$departamento = $consulta_paciente2['departamento_id'];
	$municipio = $consulta_paciente2['municipio_id'];
	$correo = $consulta_paciente2['email'];
	$consulta_paciente_status = $consulta_paciente2['estado'];
}

if($consulta_paciente_status == 1){
   $estado_paciente_ = "Activo";
}else if($consulta_paciente_status == 2 || $consulta_paciente_status == 4){
   $estado_paciente_ = "Pasivo";
}else if($consulta_paciente_status == 3){
   $estado_paciente_ = "Fallecido";
}   

//CONSULTAR PUESTO COLABORADOR
$consulta_puesto = "SELECT puesto_id 
     FROM colaboradores 
	 WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta_puesto);
$consulta_puesto1 = $result->fetch_assoc(); 

$puesto_colaborador = "";

if($result->num_rows>0){
	$puesto_colaborador = $consulta_puesto1['puesto_id'];
}

//CONSULTAMOS LA EXISTENCIA DE LA AUSENCIA
$consulta = "SELECT ausencia_id 
       FROM ausencias 
	   WHERE pacientes_id = '$pacientes_id' AND colaborador_id = '$colaborador_id' AND servicio_id = '$servicio_id' AND fecha = '$fecha'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();

$ausencia_id = "";	

if($result->num_rows>0){
	$ausencia_id = $consulta2['ausencia_id'];
}
/*INCIO CORRELATIVOS DE LAS ENTIDADES*/

//OBTENER CORRELATIVO DE LA ENTIDAD AUSENCIA
$numero = correlativo("ausencia_id", "ausencias");

//OBTENER CORRELATIVO HISTORIAL PACIENTES
$numero_historial = correlativo("historial_id", "historial_pacientes");

$consulta = "SELECT preclinica_id 
       FROM preclinica 
       WHERE fecha = '$fecha' AND pacientes_id = '$pacientes_id' AND expediente = '$expediente' AND servicio_id = '$servicio_id' AND colaborador_id = '$colaborador_id'"; 
$result = $mysqli->query($consulta);	   
$consulta2 = $result->fetch_assoc();

$preclinica_id = "";

if($result->num_rows>0){
	$preclinica_id = $consulta2['preclinica_id'];
}
/*********************************************************************************/
if($preclinica_id == ""){//EVALUAMOS QUE EL USUARIO NO TIENE PRECLINICA ALMACENADA
    if($ausencia_id == ""){//EVALUAMOS SI AL USUARIO NO SE LE HA ALMACENADO UNA ASENCIA EN LA ENTIDAD AUSENCIAS
	    //CAMBIAMOS EL ESTATUS DEL USAURIO EN LA ENTIDAD AGENDA, COLOCONADOLE 2 (AUSENCIA)
	    $update = "UPDATE agenda SET status = 2, preclinica = 2 
		   WHERE agenda_id = '$agenda_id'";
		$query = $mysqli->query($update);
		
        //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
        $historial_numero = historial();
        $estado = "Actualizar";
        $observacion = "Se actualiza el campo preclinica en la entidad agenda para el servicio: $servicio_nombre";
        $modulo = "Agenda";
        $insert = "INSERT INTO historial 
              VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$agenda_id','$colaborador_id','$servicio_id','$fecha','$estado','$observacion','$usuario_sistema','$fecha_registro')";	 
        $mysqli->query($insert);
        /*****************************************************/			
	
	    //SE HACE LA CONSULTA DE EL TIPO DE PACIENTE QUE SE ESTA ATENDIENDO
	    $consultar_expediente = "SELECT a.agenda_id 
             FROM agenda AS a 
             INNER JOIN colaboradores AS c
             ON a.colaborador_id = c.colaborador_id
             WHERE a.pacientes_id = '$pacientes_id' AND c.puesto_id = '$puesto_colaborador' AND a.servicio_id = '$servicio_id' AND a.status = 1";
		$result = $mysqli->query($consultar_expediente);
        $consultar_expediente1 = $result->fetch_assoc(); 
	
	    //SE EVALUA EL TIPO DE PACUENTE/USUARIO SI ES NUEVO O SUBSIGUIENTE
        if ($consultar_expediente1['agenda_id']== ""){
	       $paciente = 'N';
	    }else{
	       $paciente = 'S';
	    }

        if($query){
			//INSERTAMOS LOS VALORES EN LA ENTIDAD AUSENCIA
            $insert = "INSERT INTO ausencias 
	                  VALUES('$numero','$pacientes_id', '$expediente', '$agenda_id','$fecha','$comentario','$usuario_sistema','$colaborador_id','$servicio_id','$paciente','$fecha_registro')";
			$mysqli->query($insert);
						
            //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
            $historial_numero = historial();
            $estado = "Agregar";
            $observacion = "Se agrega la ausencia del usuario en el servicio: $servicio_nombre";
            $modulo = "Ausencias";
            $insert = "INSERT INTO historial 
                 VALUES('$historial_numero','$pacientes_id','$expediente','$modulo','$numero','$colaborador_id','$servicio_id','$fecha','$estado','$observacion','$usuario_sistema','$fecha_registro')";	 
            $mysqli->query($insert);
            /*****************************************************/
			echo 1;//REGISTRO PROCESADO CORRECTAMENTE
		}else{
			echo 2;//ERROR AL PROCESAR LA SOLICITUD
		}		
	}else{
       echo 3;//NO SE PUEDE ALMACENAR LA AUSENCIA DE ESTE USUARIO, DEBIDO A QUE YA HA SIDO ALMACENADA.
    }
}else{
   echo 4;//ESTE USUARIO HA SIDO PRECLINEADO NO SE PUEDE MARCAR LA AUSENCIA	
}
/*********************************************************************************/

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>