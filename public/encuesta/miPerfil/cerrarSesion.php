<?php
	ob_start();
	// Start the session
    session_start();
	 unset($_SESSION["tipoUsuario"]); 
	 unset($_SESSION["Usuario"]);
	 unset($_SESSION["idUsuario"]);
	 session_destroy();
	header ("Location: ../index.php");
	
?>