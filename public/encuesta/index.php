
<?php
	ob_start();
	// Start the session
	session_start();
    if( !empty($_SESSION["tipoUsuario"]) && !empty($_SESSION["Usuario"]) && !empty($_SESSION["idUsuario"]) )
		header ("Location: miPerfil/miPerfil.php");
	
	include ('assets/conexionBD.php');
	
    $registroAgregado = false;    
	$nombreError="";
	$contraseniaError="";
	$ingresoError="";
	$nombre="";
	$contrasenia="";

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
		$nombre=$_POST["usuario"];
		$contrasenia=$_POST["contrasenia"];
                
        $esValido = false;
		if( $nombre == "" ){
			$nombreError= "* Campo obligatorio";
			$esValido = false;
		}
		else
			$esValido = true;
        //Se valida la contraseña
		if( $contrasenia == "" ){
			$contraseniaError = "* Campo obligatorio";
			$esValido = false;
		}
		else
			$esValido = true;
        
        
        //Si todo los datos son validos 
        if($esValido){
/*            $sql = "SELECT	u.nombreUsuario,
							u.idUsuario,
							p.nombrePerfil
					From	usuario u
					JOIN	perfil  p ON p.idPerfil = u.Perfil_idPerfil
					WHERE	u.nombreUsuario = '".$nombre."' AND
							u.contrasenia = '".$contrasenia."' AND elimina_usuario = 0";
*/
            $sql = "SELECT	p.nombre,
					u.id,
					tu.descripcion as nombrePerfil
					From usuario u
					JOIN tipo_usuario  tu
					ON tu.id = u.tipo_usuario
					JOIN persona p
					ON p.id = u.persona_id
					WHERE	u.usuario = '".$nombre."' AND	u.contrasena = '".$contrasenia."' AND u.status = 1";
//			$result = $conn->query($sql);
		$stmt = sqlsrv_query( $conn, $sql);
//            if ($result->num_rows > 0 ){
			if($obj = sqlsrv_fetch_object($stmt)){
//				$row = $result->fetch_assoc();
				$_SESSION["tipoUsuario"] = $obj->nombrePerfil;
				$_SESSION["Usuario"] = $obj->nombre;
				$_SESSION["idUsuario"] = $obj->id;
				header ("Location: miPerfil/miPerfil.php");
			}
			else?>
				<script>alert('Usuario y/o contrasenia incorrectos');</script>
            <?php
        }
    }

?>


<!DOCTYPE html>
<html lang="en" class="no-js">

    <head>

        <meta charset="utf-8">
        <title>Login</title>
        <link rel="icon" href="assets/img/ett.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

		<link rel="stylesheet" type="text/css" href="/css/bootstrap.css" />
        <!-- CSS -->
        <link rel="stylesheet" href="assets/css/login.css">

    </head>

    <body  style= "background-image: url('/img/fondoUTJ1.jpg'); background-repeat: no-repeat; background-position: center;">
	<header id="header">
	    <div class="navbar navbar-inverse navbar-fixed-top">
	        <div class="container">
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                </button>
	                <a class="navbar-brand" href="/">CA02UTJ</a>
	            </div>
	            <div class="navbar-collapse collapse">
	                <ul class="nav navbar-nav">
	                    <li><a href="/index">Inicio</a></li>
	                    <li><a href="/index/about">Acerca de...</a></li>
	                    <li><a href="/index/contact">Contactanos</a></li>
	                </ul>
	                <ul class="nav navbar-nav navbar-right">
	              <li><a href="/index/pre-register" id="registerLink">Registro</a></li>
	          </ul>
	            </div>
	        </div>
	    </div>
	</header>

<!--		<form class="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">-->
			<div class="container">
				<form class="login" method="post" action="/public/encuesta/">
					<div class="row">
			          <div class="col-md-4 text-center">
			              <img src="/img/logo_ca.png" style="max-width: 200px; max-height: 100px;" />
			          </div>
					</div>
					<div class="row">
						<div class="col-xs-12 form-group">
							<?php echo $nombreError;?>
							<input type="text" name="usuario" class="col-xs-4 log in-input form-control" placeholder="nombre de usuario" autofocus value="<?php echo $nombre; ?>">
						</div>
					</div>				
					<div class="row">
						<div class="col-xs-12 form-group">
							<?php echo $contraseniaError;?>
							<input type="password" name="contrasenia" class="col-xs-4 logi n-input form-control" placeholder="contraseña">
						</div>
					</div>
					
					<input type="submit" value="Ingresar" class="login-button">
				</form>
			</div>

  <footer >
	<div class="container body-content">
	  <hr />
	      <div class="col-xs-4">
	          <b>Universidad Tecnológica de Jalisco</b> <br />
	          <address>                    
	              División de Desarrollo e Innovación Empresarial <br />
	              Director de la División<br />
	              Lic. Jorge Sandoval Rodríguez <br />
	              Luis J. Jiménez #577 <br />
	              <abbr title="Telefono">Teléfonos: </abbr><br />
	              Conmutador: 30300900 Ext 7361 <br />
	              Directo:    30300926
	          </address>
	          
	          </div>
	      <div class="col-xs-4">
	         
	          <p>Desarrolladores del sistema <br /><br /> Edgardo E. González del Castillo <br /> Esau Tolosa Carrillo </p>
	          </div>
	          <div class="col-xs-4">
	              <img src="/img/logo_ca.png" width="200" height="100" />
	          </div>
	</div>
  </footer>

    </body>

</html>

