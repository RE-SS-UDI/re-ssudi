<?php $titulo="Modificar Encuesta"; ?>
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
        <form method="get" action="ModificarEncuesta.php">
            <input type="text" name="id" placeholder="id Encuesta">
            <input type="submit" value="Modificar Encuesta">
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
        <form action="ModificarEncuesta.php">
            <input type="submit" value="Modificar otra">
        </FORM>
<?php
        return; //Termina el codigo
    }
    Atributo::configurar($conn, "encuesta");
    $obj = (object) array('nombreEncuesta' => new Atributo("nombreEncuesta", TipoDeDato::ALFANUMERICO, 20, $null=false, $unique=true));
	$obj->nombreEncuesta->error = "";
    	
    $registroAgregado = false;    

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
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
			if( $obj->nombreEncuesta->valor == "" )
				$obj->nombreEncuesta->error = "*Campo obligatorio";
			else
				$esValido = true;
		}
                
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
			$sqlActualizar = "UPDATE encuesta SET nombreEncuesta='".$obj->nombreEncuesta->valor."' where idEncuesta='".$idModificar."'";
            $stmt = $conn->prepare($sqlActualizar);
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
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
    
        <label>Nombre</label> <span class="error"><?php echo $obj->nombreEncuesta->error;?></span>
        <input type="text" name="<?php echo $obj->nombreEncuesta->nombre;?>" value="<?php echo $obj->nombreEncuesta->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Modificar">  
    </form>

<?php } else { ?>

Se modificó la encuesta " <?php echo $obj->nombreEncuesta->valor;?> " con éxito 

<table>
	<tr>
	<td style='border: 0pt'>
		<form method="post" action="AgregarPreguntas.php?id=<?php echo $idModificar; ?>">
			<input type="submit" name="agregar" value="Agregar Preguntas">
		</FORM>
	</td>
	<td style='border: 0pt'>
		<form method="post" action="QuitarPreguntas.php?id=<?php echo $idModificar; ?>">
			<input type="submit" name="quitar" value="Quitar Preguntas">
		</FORM>
	</td>
	</tr>
	<tr >
	<td  colspan="3" style='border: 0pt' class="noFondo">
		<form method="get" action="ModificarEncuesta.php">
			<input type="submit" name="otro" value="Modificar otra encuesta">
		</FORM>
	</td>
	</tr>
</table>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>