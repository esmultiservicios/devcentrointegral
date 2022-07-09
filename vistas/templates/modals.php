<!--INICIO MODAL CAMBIAR CONTRASEÑA -->
 <div class="modal fade" id="ModalContraseña">
	<div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Modificar Contraseña</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
        </div><div class="container"></div>
        <div class="modal-body">
			<form id="form-cambiarcontra" action="" method="POST" data-form="" autocomplete="off" enctype="multipart/form-data">
				<div class="form-row">
				    <div class="col-md-12 mb-3">
						<div class="input-group mb-3">
						  <input type="text" required="required" readonly id="id-registro" name="id-registro" readonly="readonly" style="display: none;" class="form-control"/>
						  <input type="password" name="contranaterior" class="form-control" id="contranaterior" placeholder="Contraseña Anterior" required="required">
						  <div class="input-group-append">
							<span class="btn btn-outline-success" id="show_password1" style="cursor:pointer;"><i id="icon1" class="fa-solid fa-eye-slash fa-lg"></i></span>
						  </div>
						</div>
					</div>
				</div>
				<div class="form-row">
				    <div class="col-md-12 mb-3">
						<div class="input-group mb-3">
						  <input type="password" name="nuevacontra" class="form-control" id="nuevacontra" placeholder="Nueva Contraseña" required="required">
						  <div class="input-group-append">
							<span class="btn btn-outline-success" id="show_password2" style="cursor:pointer;"><i id="icon2" class="fa-solid fa-eye-slash fa-lg"></i></span>
						  </div>
						</div>
					</div>
				</div>
				<div class="form-row">
				    <div class="col-md-12 mb-3">
						<div class="input-group mb-3">
						  <input type="password" name="repcontra" class="form-control" id="repcontra" placeholder="Repetir Contraseña" required="required">
						  <div class="input-group-append">
							<span class="btn btn-outline-success" id="show_password3" style="cursor:pointer;"><i id="icon3" class="fa-solid fa-eye-slash fa-lg"></i></span>
						  </div>
						</div>
					</div>
				</div>
				<div class="form-row">
				    <div class="col-md-12 mb-3">
						<div id="mensaje_cmabiar_contra"></div>
					</div>
				</div>
				<div class="form-row">
				    <div class="col-md-12 mb-3">
					   <ul title="La contraseña debe cumplir con todas estas características">
					     <li id="mayus"> 1 Mayúscula</li>
					     <li id="special">1 Caracter Especial (Símbolo)</li>
					     <li id="numbers">Números</li>
					     <li id="lower">Minúsculas</li>
					     <li id="len">Mínimo 8 Caracteres</li>
					  </ul>
					</div>
				</div>
				<input type="hidden" name="id" class="form-control" id="id" value = "<?php echo $_SESSION['colaborador_id'];?>">
				<div class="modal-footer">
				<button class="btn btn-success ml-2" type="submit" id="Modalcambiarcontra_Edit"><div class="sb-nav-link-icon"></div><i class="far fa-save fa-lg"></i> Modificar</button>
				</div>
			</form>
        </div>
      </div>
    </div>
</div>
 <!--FIN MODAL CAMBIAR CONTRASEÑA -->  
 
<!--INICIO MODAL PARA SALIR-->
   <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="salir" data-keyboard="false">
     <div class="modal-dialog modal-sm">
       <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel"><center>¿Realmente quiere salir?</center></h4>
         </div>
         <div class="modal-body">
           <center>
            <button type="button" class="btn btn-primary" onClick="salir();" id="Si"><span class="glyphicon glyphicon-ok"></span> Si</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" id="No"><span class="glyphicon glyphicon-remove-circle"></span> No</button>
            <p>
            <div id ="salida" style="display: none;">
            </div>
            </p>
           </center>
         </div>
      </div>
      </div>
   </div>	
<!--FIN MODAL PARA SALIR-->

<!-- Modal Start here-->
<div class="modal fade bs-example-modal-sm" id="myPleaseWait" tabindex="-1"
    role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="glyphicon glyphicon-time">
                    </span> Por favor espere.
                 </h4>
            </div>
            <div class="modal-body">
                <div class="mensaje">
                    <center><img src="../img/gif-load.gif" width="35%" heigh="35%"></center></center>
                </div>
            </div>
        </div>
    </div>
</div>   
<!--Fin Ventanas Modales-->   