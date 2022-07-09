<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$proceso = $_POST['pro_ausencias'];
$fecha_ausencia = $_POST['fecha_ausencia'];
$fecha_ausencia = $_POST['fecha_ausenciaf'];
$colaborador_id = $_POST['medico_ausencia'];
$comentario = cleanStringStrtolower($_POST['comentario_ausencias']);
$usuario = $_SESSION['colaborador_id'];
$diai = date('d',strtotime($_POST['fecha_ausencia']));
$diaf = date('d',strtotime($_POST['fecha_ausenciaf']));
$fecha_registro = date("Y-m-d H:i:s");

//CONSULTAR EXISTENCIA DE AUSENCIA
$consulta = "SELECT ausencia_id 
    FROM ausencia_medicos 
	WHERE colaborador_id = '$colaborador_id' AND fecha_ausencia = '$fecha_ausencia'";
$result = $mysqli->query($consulta);
$consulta2 = $result->fetch_assoc();
$ausencia_id = $consulta2['ausencia_id'];
   
$valor = $diai - $diaf;

if ($valor < 0){
   $total = -1*$valor;
}

//VERIFICAMOS EL PROCESO
$i = 0;
while ($diai <= $diaf){
   $numero = correlativo("ausencia_id", "ausencia_medicos");	
   
   $fecha_ausencia_registro = date('Y-m-d', strtotime('+ '.$i.' day', strtotime($_POST['fecha_ausencia'])));   
   
   $insert = "INSERT INTO ausencia_medicos VALUES('$numero', '$colaborador_id', '$fecha_registro', '$fecha_ausencia_registro', '$usuario','$comentario')"; 
   $query = $mysqli->query($insert);
   
   $i++;
   $diai++; 
}

 if($query){
   echo 1;
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado = "Agregar";
   $observacion = "Se agrego un ausencia para este colaborador";
   $modulo = "Citas";
   $insert = "INSERT INTO historial 
        VALUES('$historial_numero','0','0','$modulo','0','$colaborador_id','0','$fecha_ausencia','$estado','$observacion','$usuario','$fecha_registro')";
   $mysqli->query($insert);
   /*****************************************************/		 
 }else{
	 echo 2;
 }
   
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>