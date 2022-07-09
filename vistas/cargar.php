<?php 
session_start(); 
include "../php/funtions.php";

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php'); 
}    

$_SESSION['menu'] = "Importar";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];  
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];
   
$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME	
$fecha = date("Y-m-d H:i:s");  
$comentario = mb_convert_case("Ingreso al Modulo de Importar", MB_CASE_TITLE, "UTF-8");   

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id); 
}     
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="Script Tutorials" />
    <meta name="description" content="Responsive Websites Orden Hospitalaria de San Juan de Dios">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>	
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cargar :: <?php echo SERVEREMPRESA;?></title>
    <!--Menu e Iconos-->   	
    <link rel="stylesheet" href="../css/estilo_import_file.css" type="text/css" media="screen">
	
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="../css/error_bien.css" type="text/css" media="screen">		
    <link rel="stylesheet" href="../login/css/all.css"><!--//USO DE ICONOS font awesome-->
    <link rel="shortcut icon" href="../img/logo_icono.png">		
    <link href="../css/fileinput.css" media="all" rel="stylesheet" type="text/css" />     
    <!--******************************************************************************-->
    <link href="../css/estilo-paginacion.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap-select.css">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
	<link href="../sweetalert/sweetalert.css" rel="stylesheet">
    <!---->   
    <style>
	.bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
       width: 110px;
    }	
    </style>	
</head>
<body onload="ini(), nologoneado(<?php echo $_SESSION['colaborador_id'];?>)" onkeypress="parar(), nologoneado(<?php echo $_SESSION['colaborador_id'];?>)" onclick="parar(), nologoneado(<?php echo $_SESSION['colaborador_id'];?>)">
   <!--Ventanas Modales--> 
   <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="mensaje" data-keyboard="false">
     <div class="modal-dialog modal-sm">
       <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="mensaje_mensaje"></h4>
         </div>
         <div class="modal-body">
           <center>
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="okay"><span class="glyphicon glyphicon-ok"></span> Okay</button>
            <button style="display: none;" type="button" class="btn btn-danger" data-dismiss="modal" id="bad"><span class="glyphicon glyphicon-ok"></span> Okay</button>			
            <p>
            <div id ="salida" style="display: none;">
            </div>
            </p>
           </center>
         </div>
      </div>
      </div>
   </div>     
   <!-- Small modal -->
   <?php include("templates/modals.php"); ?>
		
	<!--MENU-->	  
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU--> 

    <!-- main container -->
    <div class="container-fluid">
      <!--Cuerpo-->
	  <br/>
	  
      <div class="container-fluid">
           <center><header><h3>Cargar</h3></header></center>	  
			<form class="form-horizontal" id="subida" method="post" enctype="multipart/form-data">
			  <div class="modal-body">
				  <input type="text" required="required" readonly id="id-registro" name="id-registro" readonly="readonly" style="display: none;" class="form-control"/>
				  <div class="form-group">
				    <div class="col-sm-2">
                        <select id="first-disabled2" class="form-control" name="selector" data-toggle="tooltip" data-placement="top" title="Seleccione">               
                        </select>					
					</div> 
				  	<div class="col-sm-5">
                         <input id="file-0a" class="file" type="file" multiple data-min-file-count="1" name="csv" accept=".csv">					
					</div> 
				    <div class="col-sm-2">
                        <a target="_blank" class="btn btn-success" class="form-control" id="ejemplo" data-toggle="tooltip" data-placement="top" title="Permite descargar un ejemplo del archivo a subir"><span class="fas fa-download"></span></a>					
					</div>   					
				  </div> 				  
		
				  <div class="form-group">
				    <center>
				       <div class="col-sm-12">
                          <div id="respuesta"></div>
					   </div>
					</center>
				  </div>				  
			  </div>
			</form>
      </div>          
      <!--Fin del Cuerpo-->
    <br/><br/>	
<!--FOOTER-->	  
  <?php include("templates/footer.php"); ?>
<!--FIN FOOTER-->  
    </div><!--/.container-->

    <!-- add javascripts -->
    <script src="../js/jquery.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>
    <!--Función que permite hacer desplegable el menú-->
    <script src="../js/menu-despelgable.js"></script>
	<script src="../js/session.js"></script>
    <script src="../js/functions.js"></script>		
    <script src="../js/subir_archivo_csv.js"></script>
    <script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_fr.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js" type="text/javascript"></script>    
    <script src="../bootstrap/js/bootstrap-select.js"></script>
	<script src="../js/myjava_cambiar_pass.js"></script>
	<script src="../sweetalert/sweetalert.min.js"></script>		
    <!--Boton volver al principio-->
    <script src="../js/arriba.js"></script>       
    <span class="ir-arriba" data-toggle="tooltip" data-placement="top" title="Ir Arriba"><i class="fas fa-chevron-up fa-xs"></i></span>  
</body>
</html>

