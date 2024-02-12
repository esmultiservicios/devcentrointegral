<?php
session_start();
include "../php/funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

if( isset($_SESSION['colaborador_id']) == false ){
   header('Location: login.php');
}

$_SESSION['menu'] = "Secuencia de Facturación";

if(isset($_SESSION['colaborador_id'])){
 $colaborador_id = $_SESSION['colaborador_id'];
}else{
   $colaborador_id = "";
}

$type = $_SESSION['type'];

$nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);//HOSTNAME
$fecha = date("Y-m-d H:i:s");
$comentario = mb_convert_case("Ingreso al Modulo Secuencia de Facturación", MB_CASE_TITLE, "UTF-8");

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
    <title>Secuencia de Facturación :: <?php echo $empresa; ?></title>
	<?php include("script_css.php"); ?>
</head>
<body>
   <!--Ventanas Modales-->
   <!-- Small modal -->
  <?php include("templates/modals.php"); ?>

<!--INICIO MODAL-->
<div class="modal fade" id="secuenciaFacturacion">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Secuencia de Facturación</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div>
        <div class="modal-body">
			<form class="FormularioAjax" id="formularioSecuenciaFacturacion" data-async data-target="#rating-modal" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-md-12 mb-3">
					    <input type="hidden" id="secuencia_facturacion_id" name="secuencia_facturacion_id" class="form-control">
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
					    <label for="empresa">Empresa <span class="priority">*<span/></label>
						<div class="input-group mb-3">
							<select class="selectpicker" id="empresa" name="empresa" required data-live-search="true" title="Empresa">
							</select>
						</div>
					</div>
					<div class="col-md-9 mb-3">
					  <label for="cai">CAI</label>
					  <div class="input-group mb-3">
						  <input type="text" name="cai" id="cai" class="form-control" placeholder="CAI" data-toggle="tooltip" data-placement="top" title="Este es el número entregado en la documentación solicitada el cual tendrá el siguiente formato: CAI: 57606A-B57ED1-224B98-7DA363-38B33B-B1">
						  	<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="far fa-id-card fa-lg"></i></span>
							</div>
					   </div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="prefijo">Prefijo</label>
					  <div class="input-group mb-3">
						  <input type="text" name="prefijo" id="prefijo" class="form-control" placeholder="Prefijo" data-toggle="tooltip" data-placement="top" title="Este es el número incial de la facturación según el documento entregado el cual inicia por ejemplo: 000-001-01-">
							<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fab fa-autoprefixer fa-lg"></i></span>
							</div>
					   </div>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="relleno">Relleno <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="number" name="relleno" id="relleno" class="form-control" placeholder="Relleno" required data-toggle="tooltip" data-placement="top" title="Esta es la cantidad de ceros que se agregaran antes del número para completar el valor total necesario">
						  	<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-fill fa-lg"></i></span>
							</div>
					   </div>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="nombre">Incremento <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="incremento" name="incremento" id="incremento" class="form-control" placeholder="Incremento" required data-toggle="tooltip" data-placement="top" title="Esta opción se refiere a la cantidad de números en lo cual el numero en la casilla siguiente se va a incrementar, por ejemplo: Sí agregar el número 1 se incrementará de uno en uno, si agrega 2, de dos en dos y así |sucesivamente el número que agregué">
						  	<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-arrow-right fa-lg"></i></span>
							</div>
					   </div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="nombre">Siguiente <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="siguiente" name="siguiente" id="siguiente" class="form-control" data-toggle="tooltip" data-placement="top" title="Número Siguiente" placeholder="Siguiente" required data-toggle="tooltip" data-placement="top" title="Este campo hace referencia al siguiente número que continua en el correlativo">
						  	<div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-caret-right fa-lg"></i></span>
							</div>
					   </div>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rango_inicial">Rango Inicial <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="text" name="rango_inicial" id="rango_inicial" class="form-control" placeholder="Rango Inicial" required="required">
						  <div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-list-ol fa-lg"></i></span>
							</div>
					   </div>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="rango_final">Rango Final <span class="priority">*<span/></label>
					  <div class="input-group mb-3">
						  <input type="number" name="rango_final" id="rango_final" class="form-control" placeholder="Rango Final" required="required">
						  <div class="input-group-append">
								<span class="input-group-text"><div class="sb-nav-link-icon"></div><i class="fas fa-list-ol fa-lg"></i></span>
							</div>
					   </div>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-4 mb-3">
					  <label for="fecha_activacion">Fecha de Activación <span class="priority">*<span/></label>
					  <input type="date" required="required" id="fecha_activacion" name="fecha_activacion" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
					</div>
					<div class="col-md-4 mb-3">
					  <label for="fecha_limite">Fecha Límite <span class="priority">*<span/></label>
					  <input type="date" required="required" id="fecha_limite" name="fecha_limite" value="<?php echo date ("Y-m-d");?>" class="form-control"/>
					</div>
         			<div class="col-md-3 mb-3">
					    <label for="estado">Estado <span class="priority">*<span/></label>
						<div class="input-group mb-3">
							<select class="selectpicker" id="estado" name="estado" required data-live-search="true" title="Estado">
							</select>
						</div>
					</div>
				</div>
			</form>
        </div>
		<div class="modal-footer">
			<button class="btn btn-primary ml-2" form="formularioSecuenciaFacturacion" type="submit" id="reg"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Registrar</button>
			<button class="btn btn-warning ml-2" form="formularioSecuenciaFacturacion" type="submit" id="edi"><div class="sb-nav-link-icon"></div><i class="fas fa-edit fa-lg"></i> Modificar</button>
			<button class="btn btn-danger ml-2" form="formularioSecuenciaFacturacion" type="submit" id="delete"><div class="sb-nav-link-icon"></div><i class="fas fa-trash fa-lg"></i> Eliminar</button>
		</div>
      </div>
    </div>
</div>
   <?php include("modals/modals.php");?>

   <!--Fin Ventanas Modales-->
	<!--MENU-->
       <?php include("templates/menu.php"); ?>
    <!--FIN MENU-->

<br><br><br>
<div class="container-fluid">
	<ol class="breadcrumb mt-2 mb-4">
		<li class="breadcrumb-item"><a class="breadcrumb-link" href="inicio.php">Dashboard</a></li>
		<li class="breadcrumb-item active" id="acciones_factura"><span id="label_acciones_factura"></span>Secuencia de Facturación</li>
	</ol>

    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-search  mr-1"></i>
        Búsqueda
      </div>
      <div class="card-body">
        <form id="form_main" class="form-inline">
          <div class="form-group mr-1">
            <div class="input-group">
              <div class="input-group-append">
                <span class="input-group-text"><div class="sb-nav-link-icon"></div>Empresa</span>
              </div>
              <select id="empresa" name="empresa" class="selectpicker" title="Empresa" data-live-search="true">
  						</select>
            </div>
          </div>
          <div class="form-group mr-1">
            <div class="input-group">
              <div class="input-group-append">
                <span class="input-group-text"><div class="sb-nav-link-icon"></div>Estado</span>
              </div>
              <select id="estado" name="estado" class="selectpicker" title="Estado" data-live-search="true">
  						</select>
            </div>
          </div>
          <div class="form-group mr-1">
            <div class="input-group">
              <input type="date" required="required" id="fechaf" name="fechaf" style="width: 159px;" value="<?php echo date ("Y-m-d");?>" data-toggle="tooltip" data-placement="top" title="Fecha Inicial" class="form-control"/>
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

	</div>
	<?php include("templates/factura.php"); ?>
	<?php include("templates/footer.php"); ?>
</div>

    <!-- add javascripts -->
	<?php
		include "script.php";

		include "../js/main.php";
		include "../js/myjava_secuencia_facturacion.php";
		include "../js/select.php";
		include "../js/functions.php";
		include "../js/myjava_cambiar_pass.php";
	?>

</body>
</html>
