<?php
session_start();
include "../php/funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php');
}

$_SESSION['menu'] = "Configuraciones Varios";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME
$fecha = date("Y-m-d H:i:s");
$comentario = mb_convert_case("Ingreso al Modulo de Configuración de Correos", MB_CASE_TITLE, "UTF-8");

if($colaborador_id != "" || $colaborador_id != null){
   historial_acceso($comentario, $nombre_host, $colaborador_id);
}

//OBTENER NOMBRE DE EMPRESA
$usuario = $_SESSION['colaborador_id'];

$query_empresa = "SELECT e.nombre AS 'nombre'
	FROM users AS u
	INNER JOIN empresa AS e
	ON u.empresa_id = e.empresa_id
	WHERE u.colaborador_id = '$usuario'";
$result = $mysqli->query($query_empresa) or die($mysqli->error);
$consulta_registro = $result->fetch_assoc();

$empresa = '';

if($result->num_rows>0){
  $empresa = $consulta_registro['nombre'];
}

$mysqli->close();//CERRAR CONEXIÓN
 ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="author" content="Script Tutorials" />
    <meta name="description" content="Responsive Websites Orden Hospitalaria de San Juan de Dios">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Configuración Correos :: <?php echo $empresa; ?></title>
	<?php include("script_css.php"); ?>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->
  <?php include("templates/modals.php"); ?>

<div class="modal fade" id="registrar">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Registro Correo Electrónico</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">
			<form class="FormularioAjax" id="formulario_registros" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="id_registro" name="id_registro" class="form-control"/>
						<div class="input-group mb-3">
							<input type="text" required readonly id="pro" name="pro" class="form-control"/>
							<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i></span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-row">
          <div class="col-md-3 mb-3">
              <label for="conf_tipo">Tipo Correo <span class="priority">*<span/></label>
          	<div class="input-group mb-3">
          		<select class="selectpicker" id="conf_tipo" name="conf_tipo" required data-live-search="true" title="Tipo Correo">
          		</select>
          	</div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="conf_smtp_secure">SMTP Secure <span class="priority">*<span/></label>
            <div class="input-group mb-3">
              <select class="selectpicker" id="conf_smtp_secure" name="conf_smtp_secure" required data-live-search="true" title="SMTP Secure">
              </select>
            </div>
          </div>
					<div class="col-md-6 mb-3">
					  <label for="expedoente">Servidor <span class="priority">*<span/></label>
					  <input type="text" required id="conf_servidor" name="conf_servidor" class="form-control"/>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="expedoente">Correo <span class="priority">*<span/></label>
					  <input type="mail" required id="conf_mail" name="conf_mail" class="form-control"/>
					</div>
					<div class="col-md-6 mb-3">
					  <label for="edad">Contraseña <span class="priority">*<span/></label>
					  <input type="password" required name="conf_pass" id="conf_pass" maxlength="100" class="form-control"/>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-6 mb-3">
					  <label for="expedoente">Puerto <span class="priority">*<span/></label>
					  <input type="number" required id="conf_puerto" name="conf_puerto" class="form-control" maxlength="3"/>
					</div>
				</div>
			</form>
        </div>
		<div class="modal-footer">
			<button class="btn btn-success ml-2" form="formulario_registros" type="submit" id="test_confEmails"><div class="sb-nav-link-icon"></div><i class="fas fa-mail-bulk fa-lg"></i> Probar Conexión</button>
			<button class="btn btn-primary ml-2" form="formulario_registros" type="submit" id="reg"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" form="formulario_registros" type="submit" id="edi"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Editar</button>
		</div>
      </div>
    </div>
</div>
<!--INICIO MODAL-->
   <?php include("modals/modals.php");?>
<!--FIN MODAL-->

<!--Fin Ventanas Modales-->
	<!--MENU-->
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU-->

<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="<?php echo SERVERURL; ?>vistas/inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Configuración Correo</li>
	</ol>

    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-search  mr-1"></i>
        Búsqueda
      </div>
      <div class="card-body">
        <form id="for_main" class="form-inline">
          <div class="form-group mr-1">
            <div class="input-group">
              <input type="text" placeholder="Buscar por: Empresa" title="Buscar por: Empresa" id="bs_regis" autofocus class="form-control" size="60"/>
            </div>
          </div>
          <div class="form-group mr-1">
            <button class="btn btn-primary ml-2" type="submit" id="nuevo_registro"><div class="sb-nav-link-icon"></div><i class="fas fa-plus-circle fa-lg"></i> Crear</button>
          </div>
        </form>
      </div>
      <div class="card-footer small text-muted">

      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <i class="fab fa-sellsy mr-1"></i>
        Resultado
      </div>
      <div class="card-body">
        <div class="form-group">
          <div class="col-sm-12">
            <div class="registros overflow-auto" id="agrega-registros"></div>
          </div>
        </div>
        <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
      </div>
      <div class="card-footer small text-muted">

      </div>
    </div>

	<?php include("templates/footer.php"); ?>
</div>

    <!-- add javascripts -->
	<?php
		include "script.php";

		include "../js/main.php";
		include "../js/myjava_config_mails.php";
		include "../js/select.php";
		include "../js/functions.php";
		include "../js/myjava_cambiar_pass.php";
	?>

</body>
</html>
