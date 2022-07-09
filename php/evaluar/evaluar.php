<?php  
  //EVALUA SI HAY UN INDICIO DE SESION SI NO LO HAY ENVIA AL USUARIO A LA PAGINA DE INCIO (LOGIN)
  if ( $_SESSION['colaborador_id'] == "" )
     header('Location: ../index.php');    
?>