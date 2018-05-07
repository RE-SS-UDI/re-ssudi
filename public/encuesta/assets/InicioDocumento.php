<?php include('conexionBD.php'); ?>
<?php	
    ob_start();
// Start the session
    session_start();
    if( empty($_SESSION["tipoUsuario"]) && empty($_SESSION["Usuario"]) && empty($_SESSION["idUsuario"]) )
		header ("Location: ../index.php");
    if(!isset($titulo) || $titulo == "")
        $titulo = "Sistema de encuestas";
    

	function limpiarCadena($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
	
?>

<!DOCTYPE html>
<html>

<?php include('head.php'); ?>
<style>
.error {color: #FF0000;}
</style>
<body>
    <div id="wrapper">
        <div class="page-container">
            <?php include('header.php'); ?>
        </div>

        <div id="content">
            <div class="container container-content">
                <?php include('nav.php'); ?>

                <!-- end content -->
                <div class="col-md-9 col-xs-12" class="centro">
                    <div class="titulo">
                        <legend ><?php echo $titulo; ?></legend>
                    </div>
                    <div class="row">                        
                    