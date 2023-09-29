<!-- Navigation -->
    <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
  <a class="navbar-brand" href="#"><a href="#"><img src="<?php echo SERVERURL; ?>img/logo_main.png" width="55px" height="45px" alt=""/></a></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">

		<?php
			 if ($_SESSION['type']==3){//MEDICOS  
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-hospital-user fa-lg"></i>&nbsp;Recepción</a>
			<div class="dropdown-menu" aria-labelledby="dropdown01">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/pacientes.php">Pacientes</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/citas.php">Citas</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/agenda.php">Agenda</a>	
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/facturacion.php">Facturación</a>  
			</div>
		  </li>			
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-doctor fa-lg"></i>&nbsp;Profesionales</a>
			<div class="dropdown-menu" aria-labelledby="dropdown03">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/atencion_medica.php">Atenciones</a>
			</div>
		  </li>	

		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-chart-bar fa-lg"></i>&nbsp;Reportes</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_atenciones_medicas.php">Reporte de Atenciones</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_ausencias.php">Reporte de Ausencias</a>			  
			</div>
		  </li>			  
		<?php
	     }
		?>			

		<?php
			 if ($_SESSION['type']==4){//CONTADOR  
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-chart-bar fa-lg"></i>&nbsp;Reportes</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_facturacion.php">Reporte de Facturación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_pagos.php">Reporte de Pagos</a>	  
			</div>
		  </li>			
		<?php
	     }
		?>	
		
		<?php
			 if ($_SESSION['type']==5){//CAJA-MEDICOS 
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-hospital-user fa-lg"></i>&nbsp;Recepción</a>
			<div class="dropdown-menu" aria-labelledby="dropdown01">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/pacientes.php">Pacientes</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/citas.php">Citas</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/agenda.php">Agenda</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/facturacion.php">Facturación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reg_empresas.php">Configurar Empresas</a>				  	  
			</div>
		  </li>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-nurse fa-lg"></i>&nbsp;Confirmación</a>
			<div class="dropdown-menu" aria-labelledby="dropdown02">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/preclinica.php">Confirmación</a>
			</div>
		  </li>	
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-doctor fa-lg"></i>&nbsp;Profesionales</a>
			<div class="dropdown-menu" aria-labelledby="dropdown03">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/atencion_medica.php">Atenciones</a>
			</div>
		  </li>	
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-chart-bar fa-lg"></i>&nbsp;Reportes</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_atenciones_medicas.php">Reporte de Atenciones</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_ausencias.php">Reporte de Ausencias</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_facturacion.php">Reporte de Facturación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_pagos.php">Reporte de Pagos</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_enfermeria.php">Reporte de Confirmación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_sms.php">Reporte SMS</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_transito.php">Reporte Tránsito</a>		  
			</div>
		  </li>			  
		<?php
	     }
		?>	

		<?php
			 if ($_SESSION['type']==6){//CAJA  
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-hospital-user fa-lg"></i>&nbsp;Recepción</a>
			<div class="dropdown-menu" aria-labelledby="dropdown01">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/pacientes.php">Pacientes</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/citas.php">Citas</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/agenda.php">Agenda</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/facturacion.php">Facturación</a>	
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reg_empresas.php">Configurar Empresas</a>				  		  
			</div>
		  </li>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-nurse fa-lg"></i>&nbsp;Confirmación</a>
			<div class="dropdown-menu" aria-labelledby="dropdown02">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/preclinica.php">Confirmación</a>
			</div>
		  </li>	
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-warehouse fa-lg"></i>&nbsp;Almacén</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/productos.php">Productos</a>		  
			</div>
		  </li>			  
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-chart-bar fa-lg"></i>&nbsp;Reportes</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_facturacion.php">Reporte de Facturación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_pagos.php">Reporte de Pagos</a>		  		  
			</div>
		  </li>			  
		<?php
	     }
		?>			
		
		<?php
		 if ($_SESSION['type']==1 || $_SESSION['type']==2){ //SUPER ADMINISTRADOR Y ADMINISTRADOR 
		?>
	
		  <li class="nav-item active active">
		  	<a class="nav-link" href="<?php echo SERVERURL; ?>vistas/inicio.php"><i class="fa-solid fa-gauge fa-lg"></i>&nbsp;Dashboard <span class="sr-only">(current)</span></a>
		  </li>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-hospital-user fa-lg"></i>&nbsp;Recepción</a>
			<div class="dropdown-menu" aria-labelledby="dropdown01">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/pacientes.php">Pacientes</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/citas.php">Citas</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/agenda.php">Agenda</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/facturacion.php">Facturación</a>		  
			</div>
		  </li>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-nurse fa-lg"></i>&nbsp;Confirmación</a>
			<div class="dropdown-menu" aria-labelledby="dropdown02">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/preclinica.php">Confirmación</a>
			</div>
		  </li>	 
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-user-doctor fa-lg"></i>&nbsp;Profesionales</a>
			<div class="dropdown-menu" aria-labelledby="dropdown03">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/atencion_medica.php">Atenciones</a>
			</div>
		  </li>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-warehouse fa-lg"></i>&nbsp;Almacén</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/productos.php">Productos</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/movimientos.php">Movimientos</a>		  
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/almacen.php">Almacén</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/ubicacion.php">Ubicación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/medidas.php">Medidas</a>		  
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/categoria.php">Categoria</a>
			</div>
		  </li>	
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-chart-bar fa-lg"></i>&nbsp;Reportes</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_atenciones_medicas.php">Reporte de Atenciones</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_ausencias.php">Reporte de Ausencias</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_facturacion.php">Reporte de Facturación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reporte_pagos.php">Reporte de Pagos</a>	  
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_enfermeria.php">Reporte de Confirmación</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_sms.php">Reporte SMS</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reportes_transito.php">Reporte Tránsito</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/historial_accesos.php">Historial de Accesos</a>		  
			</div>
		  </li>	
		  <li class="nav-item dropdown active">
				<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-tools fa-lg"></i>&nbsp;Clinicare</a>
				<div class="dropdown-menu" aria-labelledby="dropdown05">
					<a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/perfil.php">Perfil</a>
					<a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/misFacturas.php">Mis Facturas</a>
				</div>
		  </li>  		  
		<?php
	     }
		?>	
    </ul>
    <form class="form-inline my-2 my-lg-0">
	  <ul class="navbar-nav mr-auto">
		<?php
			 if ($_SESSION['type']==4){//CONTADOR GENERAL
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-gears fa-lg"></i>&nbsp;</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/secuencia_facturacion.php">Secuencia Facturación</a>		  
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/empresas.php">Empresa</a>		  
			</div>
		  </li>		
		<?php
	     }
		?>		  
	  
		<?php
			 if ($_SESSION['type']==1 || $_SESSION['type']==2){//SUPER ADMINISTRADOR Y ADMINISTRADOR 
		?>
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-gears fa-lg"></i>&nbsp;Configuración</a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/colaboradores.php">Colaboradores</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/users.php">Usuarios</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/aseguradora.php">Aseguradora</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/reg_empresas.php">Configurar Empresas</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/config_varios.php">Varios</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/config_mails.php">Correo</a>
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/secuencia_facturacion.php">Secuencia Facturación</a>			  
			  <a class="dropdown-item" href="<?php echo SERVERURL; ?>vistas/empresas.php">Empresa</a>		  
			</div>
		  </li>			  
		 <?php
	     }
		 ?>	
		  <li class="nav-item dropdown active">
			<a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="saludo_sistema">></span></a>
			<div class="dropdown-menu" aria-labelledby="dropdown05">
			  <a class="dropdown-item" href="#" id="mostrar_cambiar_contraseña">Modificar Contraseña</a>
			  <a class="dropdown-item" href="#" id="salir_sistema">Sign Out</a>	  
			</div>
		  </li>	
	  </ul>
      <!--<input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
      <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>-->
    </form>
  </div>
</nav>
<br/>

<?php
  if (SISTEMA_PRUEBA=="SI"){ //CAJA
?>
    <span class="container-fluid prueba-sistema">SISTEMA DE PRUEBA</span>
<?php
  }
?>