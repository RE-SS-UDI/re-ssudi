<?php $titulo="Ver Usuario"; ?>
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
        <form method="get" action="VerUsuario.php">
            <input type="text" name="id" placeholder="id Usuario">
            <input type="submit" value="Ver Usuario">
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
        <form action="VerUsuario.php">
            <input type="submit" value="Ver otro">
        </FORM>
<?php
        return; //Termina el codigo
    }
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombreUsuario' => new Atributo("nombreUsuario", TipoDeDato::PALABRAS, 15, $null=false, $unique=true));
	$perfilSeleccionado = $zonaSeleccionada = -1;
	$registroAgregado = false;
	
				$sql = "SELECT nombreZona FROM zona";
				$resultadoZona = $conn->query($sql);
				$sql = "SELECT nombrePerfil FROM perfil ";
				$resultadoPerfil = $conn->query($sql);
	    

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        //Conseguir los valores seleccionados de las listas
        if (!empty($_POST["Zona_idZona"]))
            $zonaSeleccionada = $_POST["Zona_idZona"];
        if (!empty($_POST["Perfil_idPerfil"]))
            $perfilSeleccionado = $_POST["Perfil_idPerfil"];
        
	/*	$sqlActualizar = "UPDATE usuario SET elimina=1 where idUsuario='".$idModificar."'";
		$stmt = $conn->prepare($sqlActualizar);				
		if($stmt->execute())
			$registroAgregado = true;				*/
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
				$zonaSeleccionada = $row["Zona_idZona"];
				$perfilSeleccionado = $row["Perfil_idPerfil"];
				 //Revisar que existan perfiles y zonas en la BD
				$sql = "SELECT nombreZona FROM zona where idZona ='".$zonaSeleccionada."'";
				$resultadoZona = $conn->query($sql);
				$sql = "SELECT nombrePerfil FROM perfil where idPerfil ='".$perfilSeleccionado."'";
				$resultadoPerfil = $conn->query($sql);
				
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
    <form method="post" action="MostrarUsuarios.php">
        
		
        <label>Nombre Perfil</label><br>
        <label><?php echo $obj->nombreUsuario->valor;?></label> <br><br>
		
        
        <label>Zona</label> <br>
        <?php
            if ($resultadoZona->num_rows > 0 ) {
                $row = $resultadoZona->fetch_assoc();
        ?>
				<label><?php echo $row["nombreZona"] ?></label><br><br>
        <?php } ?>
        
        
        <label>Perfil</label> <br>
        <?php
            if( $resultadoPerfil->num_rows > 0 ) {
                $row = $resultadoPerfil->fetch_assoc();
        ?>
				<label><?php echo $row["nombrePerfil"] ?></label><br><br>
        <?php } ?>
        
        <br><br>
        <input type="submit" name="submit" value="Regresar" >  
    </form>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>