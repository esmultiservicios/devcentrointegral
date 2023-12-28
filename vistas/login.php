<?php
require_once "../php/conf/configAPP.php";
//header("Location: ".SERVERURL."vistas/no_disponible.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="<?php echo SERVERURL; ?>img/logo_icono.png">
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>login/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>fontawesome/css/all.min.css">
    <!--//USO DE ICONOS font awesome-->
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>login/css/style.css">
    <link href="<?php echo SERVERURL; ?>sweetalert/sweetalert.css" rel="stylesheet">
    <title>Sign in :: <?php echo SERVEREMPRESA; ?></title>
</head>

<body>
    <div id="logreg-forms">
        <form class="form-signin" id="loginform">
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center"> Iniciar Sesión</h1>

            <p>
                <center><img src="<?php echo SERVERURL; ?>img/logo.png" width="45%" height="20%"></center>
            </p>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" id="inputEmail" class="form-control" placeholder="Usuario" required="" autofocus
                    name="usu" id="usu">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" class="form-control" placeholder="Contraseña" required="" name="con"
                    id="inputPassword">
                <div class="input-group-append">
                    <button id="show_password" class="btn btn-primary" type="button"> <span id="icon"
                            class="fa fa-eye-slash icon"></span> </button>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div id="acceso"></div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div id="mensaje"></div>
                </div>
            </div>

            <button class="btn btn-success btn-block" type="submit" id="enviar"><i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión</button>
            <a style="text-decoration:none;" href="#" id="forgot_pswd">¿Olvido su contraseña?</a>
            <hr>
            <!-- <p>Don't have an account!</p>  -->
            <button class="btn btn-primary btn-block" type="button" id="btn-signup" style="display: none;"><i
                    class="fas fa-user-plus"></i> Registrate</button>
        </form>

        <form class="form-reset" id="forgot_form">
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Restablecer Contraseña</h1>
            <p>
                <center><img src="<?php echo SERVERURL; ?>img/logo.png" width="50%" height="20%"></center>
            </p>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" id="resetEmail" class="form-control" placeholder="Usuario" required="" autofocus
                    name="usu_forgot" id="usu_forgot">
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div id="mensaje_forgot"></div>
                </div>
            </div>

            <button class="btn btn-primary btn-block" type="submit"> Restablecer</button>
            <a style="text-decoration:none;" href="#" id="cancel_reset"><i class="fas fa-angle-left"></i> Atrás</a>
        </form>

        <form class="form-signup" id="form_registro">
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center"> Formulario de Registro</h1>
            <p>
                <center><img src="<?php echo SERVERURL; ?>img/logo.png" width="50%" height="20%"></center>
            </p>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-database"></i></span>
                </div>
                <select class="form-control" name="base_datos1" id="base_datos1">
                    <option value="1">hospiasc</option>
                </select>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" id="user-name" class="form-control" placeholder="Nombre Completo" required=""
                    autofocus="">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                </div>
                <input type="text" class="form-control" placeholder="Correo Electrónico" id="mail" name="email">
                <div class="input-group-append">
                    <span class="input-group-text">@algo.com</span>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" id="user-pass" class="form-control" placeholder="Contraseña" required
                    autofocus="">
                <div class="input-group-append">
                    <button id="show_password1" class="btn btn-primary" type="button"> <span id="icon1"
                            class="fa fa-eye-slash icon"></span> </button>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" id="user-repeatpass" class="form-control" placeholder="Repetir Contraseña"
                    required autofocus="">
                <div class="input-group-append">
                    <button id="show_password2" class="btn btn-primary" type="button"> <span id="icon2"
                            class="fa fa-eye-slash icon"></span> </button>
                </div>
            </div>

            <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-user-plus"></i>
                Registrarse</button>
            <a style="text-decoration:none;" href="#" id="cancel_signup"><i class="fas fa-angle-left"></i> Atras</a>
        </form>
        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">
            <center><img src="<?php echo SERVERURL; ?>img/cami.svg" width="90%" height="90%"></center>©
            2017 - <?php echo date("Y");?> Copyright:
            <center>
                <p class="navbar-text"> Todos los derechos reservados
                </p>
            </center>
        </div>
        <!-- Copyright -->

        <?php
          if (SISTEMA_PRUEBA=="SI"){ //CAJA
        ?>
        <span class="prueba-sistema">SISTEMA DE PRUEBA</span>
        <?php
          }
        ?>
    </div>

    <p style="text-align:center">
        <a href="http://bit.ly/2RjWFMfunction toggleResetPswd(e){
           e.preventDefault();
           $('#logreg-forms .form-signin').toggle() // display:block or none
           $('#logreg-forms .form-reset').toggle() // display:block or none
        }

        function toggleSignUp(e){
           e.preventDefault();
           $('#logreg-forms .form-signin').toggle(); // display:block or none
           $('#logreg-forms .form-signup').toggle(); // display:block or none
        }

        $(()=>{
           // Login Register Form
           $('#logreg-forms #forgot_pswd').click(toggleResetPswd);
           $('#logreg-forms #cancel_reset').click(toggleResetPswd);
           $('#logreg-forms #btn-signup').click(toggleSignUp);
           $('#logreg-forms #cancel_signup').click(toggleSignUp);
        })g" </p>

            <script src="<?php echo SERVERURL; ?>login/js/jquery.min.js"></script>
            <script src="<?php echo SERVERURL; ?>login/bootstrap/js/bootstrap.min.js"></script>
            <script src="<?php echo SERVERURL; ?>login/js/script_login.js"></script>
            <script src="<?php echo SERVERURL; ?>sweetalert/sweetalert.min.js"></script>

            <a href="https://api.whatsapp.com/send?phone=50432273380&text=Hola%20CLINICARE,%20nos%20gustar%C3%ADa%20que%20nos%20puedan%20brindar%20asistencia%20t%C3%A9cnica,%20muchas%20gracias."
                class="float-ws" target="_blank" data-toggle="tooltip" data-placement="top" title="Soporte CLINICARE">
                <i class="fab fa-whatsapp my-float-ws"></i>

                <?php 		
		include "../js/login.php";		
	?>
</body>

</html>