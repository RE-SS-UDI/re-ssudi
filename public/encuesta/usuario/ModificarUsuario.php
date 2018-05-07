<?php $titulo="Modificar Usuario"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>


<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="ModificarUsuario.php">
            <input type="text" name="id" placeholder="id Usuario">
            <input type="submit" value="Modificar Usuario">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idUsuario FROM usuario WHERE idUsuario='".$idModificar."' and elimina_usuario = 0";
	if( $_SESSION["tipoUsuario"] == "Coordinador" ) {
		$sql = $sql." AND ( Perfil_idPerfil = 1  OR Perfil_idPerfil = 3 )";
	}
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        El usuario ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="ModificarUsuario.php">
            <input type="submit" value="Modificar otro">
        </FORM>
<?php
        return; //Termina el codigo
    }
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombreUsuario' => new Atributo("nombreUsuario", TipoDeDato::PALABRAS, 15, $null=false, $unique=true),
                          'contrasenia'  => new Atributo("contrasenia",  TipoDeDato::CADENA , 20, $null=false),
                          'repetirContrasenia'  => new Atributo("repetirContrasenia",  TipoDeDato::CADENA , 20, $null=false));
    	
    $perfilErr = $zonaErr = "";
    $perfilSeleccionado = $zonaSeleccionada = -1;
    $registroAgregado = false;

    //Revisar que existan perfiles y zonas en la BD
    $sql = "SELECT idZona, nombreZona FROM zona";
    $resultadoZona = $conn->query($sql);
    if ($resultadoZona->num_rows <= 0)
        $perfilErr = " *Lo sentimos, no existen zonas en la BD";

    $sql = "SELECT idPerfil, nombrePerfil FROM perfil";
    $resultadoPerfil = $conn->query($sql);
    if ($resultadoPerfil->num_rows <= 0)
        $perfilErr = " *Lo sentimos, no existen perfiles en la BD";
    

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        //Conseguir los valores seleccionados de las listas
        if (!empty($_POST["Zona_idZona"])){
            $zonaSeleccionada = $_POST["Zona_idZona"];
        }
        if (!empty($_POST["Perfil_idPerfil"])){
            $perfilSeleccionado = $_POST["Perfil_idPerfil"];

        }
        
        $esValido = true;
        foreach($obj as $key=>$value) {
            if(!$value->esValido()){
                $esValido = false;
                break;
            }
        }
		
		if( $esValido ){
			$esValido = false;
			if( $obj->nombreUsuario->valor == "" )
				$obj->nombreUsuario->error = "*Campo obligatorio";
			else
				$esValido = true;
		}
        
        //Se valida la contraseña
        if($esValido){
            $esValido = false;
			if( $obj->contrasenia->valor == "" )
				$obj->contrasenia->error = "*Campo obligatorio";
            else if(strlen($obj->contrasenia->valor) < 5)
                $obj->contrasenia->error = " *Contraseña muy corta";
            else if($obj->nombreUsuario->valor == $obj->contrasenia->valor)
                $obj->contrasenia->error = " *La contraseña no debe ser igual al nombre de usuario";
			else{
				$obj->contrasenia->error = "";
				$esValido = true;
			}
			
			if( $esValido ){
				$esValido = false;
				if( $obj->repetirContrasenia->valor == "" )
					$obj->repetirContrasenia->error = "*Campo obligatorio";			
				else if($obj->contrasenia->valor != $obj->repetirContrasenia->valor)
					$obj->repetirContrasenia->error = " *La contraseña no coincide";			
				else{
					$obj->repetirContrasenia->error = "";
					$esValido = true;
				}
			}
            
        }
        
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
			$sqlActualizar = "UPDATE usuario SET nombreUsuario=?, contrasenia=?, Zona_idZona=?, Perfil_idPerfil=? where elimina_usuario=0 AND idUsuario='".$idModificar."'";
            $stmt = $conn->prepare($sqlActualizar);
            if( $stmt->bind_param( "ssii", $obj->nombreUsuario->valor, $obj->contrasenia->valor,$_POST["Zona_idZona"],$_POST["Perfil_idPerfil"]))
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
    }
	else{
		$sql = "SELECT nombreUsuario, contrasenia, Zona_idZona, Perfil_idPerfil FROM usuario where idUsuario = '".$idModificar."' and elimina_usuario = 0";
		if( $_SESSION["tipoUsuario"] == "Coordinador" ) {
			$sql = $sql." AND ( Perfil_idPerfil = 1  OR Perfil_idPerfil = 3 )";
		}
		$resultadoUsuario = $conn->query($sql);
		if( $resultadoUsuario->num_rows > 0 ){		
			if($row = $resultadoUsuario->fetch_assoc()) {
				$obj->nombreUsuario->valor = $row["nombreUsuario"];
				$obj->contrasenia->valor = $row["contrasenia"];
				$obj->repetirContrasenia->valor = $row["contrasenia"];
				$zonaSeleccionada = $row["Zona_idZona"];
				$perfilSeleccionado = $row["Perfil_idPerfil"];
				
				//Limpiar errores
                foreach($obj as $key=>$value) {
                    if(!$value->esValido()){
                        $value->error = "";
                    }
                }
			}
		}
	}

    if(!$registroAgregado){
?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$idModificar;?>">
        
		
        <label>Nombre Perfil</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        
        <label>Contraseña</label> <span class="error"><?php echo $obj->contrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->contrasenia->nombre;?>" value="<?php echo $obj->contrasenia->valor;?>">
        
        <label>Repetir Contraseña</label> <span class="error"><?php echo $obj->repetirContrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->repetirContrasenia->nombre;?>">
        
        <label>Zona</label> 
        <select name="Zona_idZona"> <span class="error"><?php echo $zonaErr;?></span><br>
        <?php
            if ($resultadoZona->num_rows > 0) {
                // output data of each row
                while($row = $resultadoZona->fetch_assoc()) {
                    $select = $zonaSeleccionada == $row["idZona"] ? "selected" : "";
        ?>
            <option value="<?php echo $row["idZona"] ?>" <?php echo $select ?>><?php echo $row["nombreZona"] ?></option>
        <?php }} ?>
        </select>
        
        <label>Perfil</label> <span class="error"><?php echo $perfilErr;?></span><br>
        <select name="Perfil_idPerfil">
        <?php
            if ($resultadoPerfil->num_rows > 0) {
                // output data of each row
                while($row = $resultadoPerfil->fetch_assoc()) {
                    $select = $perfilSeleccionado == $row["idPerfil"] ? "selected" : "";
					if( $_SESSION["tipoUsuario"] == "Coordinador" ){
						if( $row["nombrePerfil"] == "Empresa" || $row["nombrePerfil"] == "Encuestador" ){
							?><option value="<?php echo $row["idPerfil"] ?>" <?php echo $select ?>><?php echo $row["nombrePerfil"] ?></option><?php
						}
					}
					else{
						?><option value="<?php echo $row["idPerfil"] ?>" <?php echo $select ?>><?php echo $row["nombrePerfil"] ?></option><?php
					}
				}
			} 
		?>
        </select>
        
        <br><br>
        <input type="submit" name="submit" value="Modificar">  
    </form>

<?php } else { ?>

Se modificó el usuario " <?php echo $obj->nombreUsuario->valor;?> " con éxito 

<form method="post" action="MostrarUsuarios.php">
    <input type="submit" name="reload" value="Ver usuarios">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>