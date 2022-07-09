<?php
session_start();   
include "../funtions.php";

$fecha_sistema = date("Y-m-d");
$fecha = $_POST['fecha'];
$nombre_dia_fecha_sistema = dia_nombre($fecha_sistema);
$nombre_dia_fecha = dia_nombre($fecha);

if($fecha_sistema == $fecha){
	echo 4;//NO SE PUEDEN ENVIAR MENSAJES A USUARIOS EL DIA ACTUAL DE LA CONSULTA
}else{
   if($nombre_dia_fecha_sistema == 'sabado' || $nombre_dia_fecha_sistema == 'domingo'){
	   echo 3;//ES UN FIN DE SEMANA NO SE PUEDE REALIZAR ESTA CONSULTA;
   }else{
       if($nombre_dia_fecha_sistema == 'viernes' && $nombre_dia_fecha == 'lunes'){
	      /*LA DIA DEL SISTEMA ES VIERNES Y LA FECHA SELECCIONADA ESD LUNES, SOLO HA PASADO UN DÍA YA QUE SABADO Y DOMNINGO NO SE LABORA, 
	      ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES*/
	      echo 1;
       }else if($nombre_dia_fecha_sistema == 'lunes' && $nombre_dia_fecha == 'lunes'){
	      /*LA DIA DEL SISTEMA ES VIERNES Y LA FECHA SELECCIONADA ESD LUNES, SOLO HA PASADO UN DÍA YA QUE SABADO Y DOMNINGO NO SE LABORA, 
	      ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES*/
	      echo 5;
       }else{
	       if(dias_pasados($fecha_sistema,$fecha) == 1){
	           echo 1;//SOLO HA PASADO UN DIA DE LA FECHA DEL SISTEMA A LA FECHA ACTUAL, ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES
           }else{
		       if($nombre_dia_fecha_sistema == 'viernes'){
	                 /*LA DIA DEL SISTEMA ES VIERNES Y LA FECHA SELECCIONADA ESD LUNES, SOLO HA PASADO UN DÍA YA QUE SABADO Y DOMNINGO NO SE LABORA, 
	                 ESO SIGNIFICA QUE SE ENVIA LOS MENSAJES DE UN DÍA ANTES*/
	                 echo (dias_pasados($fecha_sistema,$fecha))-2;//HAN PASADO MAS DE UN DÍA DE LA FECHA ACTUAL, ESO SIGNIFICA QUE SE ENVIAN LOS MENSAJES DE MAS DE UN DÍA
               }else{
				    echo dias_pasados($fecha_sistema,$fecha)-2;
			   }
	       }
	   }	
   }
}
?>