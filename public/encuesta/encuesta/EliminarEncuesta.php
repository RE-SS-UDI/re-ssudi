<?php $titulo="Eliminar Encuesta"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php
	if( $_SESSION["tipoUsuario"] != "Administrador" )
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="EliminarEncuesta.php">
            <input type="text" name="id" placeholder="id Encuesta">
            <input type="submit" value="Eliminar Encuesta">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idEncuesta FROM encuesta WHERE idEncuesta='".$idModificar."' and elimina_encuesta = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La encuesta ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="EliminarEncuesta.php">
            <input type="submit" value="Eliminar otra">
        </FORM>
<?php
        return; //Termina el codigo
    } 
    Atributo::configurar($conn, "encuesta");
    $obj = (object) array('nombreEncuesta' => new Atributo("nombreEncuesta", TipoDeDato::ALFANUMERICO, 50, $null=false, $unique=true));   
	$registroAgregado = false;
	  

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        $sqlActualizar = "UPDATE encuesta SET elimina=1 where idEncuesta='".$idModificar."'";
		$stmt = $conn->prepare($sqlActualizar);				
		if($stmt->execute())
			$registroAgregado = true;			
    }
	else{
		$sql = "SELECT nombreEncuesta FROM encuesta where idEncuesta = '".$idModificar."' and elimina_encuesta = 0";
		$resultado = $conn->query($sql);
		if( $resultado->num_rows > 0 ){		
			if($row = $resultado->fetch_assoc()) {
				$obj->nombreEncuesta->valor = $row["nombreEncuesta"];
				
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
        
		
        <label>Nombre Encuesta</label><br>
        <label><?php echo $obj->nombreEncuesta->valor;?></label>
                
        <br><br>
        <input type="submit" name="submit" value="Eliminar" onclick="return confirm('¿Esta seguro que desea eliminarlo?')">  
    </form>

<?php } else { ?>

Se eliminó la enucuesta " <?php echo $obj->nombreEncuesta->valor;?> " con éxito 

<form method="post" action="MostrarEncuestas.php">
    <input type="submit" name="reload" value="Ver encuestas">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>