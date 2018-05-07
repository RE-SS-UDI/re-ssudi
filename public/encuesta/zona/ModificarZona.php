<?php $titulo="Modificar Zona"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="ModificarZona.php">
            <input type="text" name="id" placeholder="id Zona">
            <input type="submit" value="Modificar Zona">
        </FORM>
<?php
        return; //Termina el codigo
    }
	$idModificar = $_GET["id"];	

    //Revisar que exista dicha empresa en la base de datos
    $sql = "SELECT idZona FROM zona WHERE idZona='".$idModificar."' and elimina_zona = 0";
    $resultado = $conn->query($sql);
    if ($resultado->num_rows <= 0){
?>
        La zona ' <?php echo $idModificar ?> ' no existe<br><br>
        <form action="ModificarZona.php">
            <input type="submit" value="Modificar otra">
        </FORM>
<?php
        return; //Termina el codigo
    }
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombreZona' => new Atributo("nombreZona", TipoDeDato::ALFANUMERICO, 50, $null=false, $unique=true));
	$obj->nombreZona->error = "";
    	
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
			if( $obj->nombreZona->valor == "" )
				$obj->nombreZona->error = "*Campo obligatorio";
			else
				$esValido = true;
		}
                
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
			$sqlActualizar = "UPDATE zona SET nombreZona='".$obj->nombreZona->valor."' where idZona='".$idModificar."'";
            $stmt = $conn->prepare($sqlActualizar);
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
    }
	else{
		$sql = "SELECT nombreZona FROM zona where idZona = '".$idModificar."' and elimina_zona = 0";
		$resultado = $conn->query($sql);
		if( $resultado->num_rows > 0 ){		
			if($row = $resultado->fetch_assoc()) {
				$obj->nombreZona->valor = $row["nombreZona"];
				
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
    
        <label>Nombre</label> <span class="error"><?php echo $obj->nombreZona->error;?></span>
        <input type="text" name="<?php echo $obj->nombreZona->nombre;?>" value="<?php echo $obj->nombreZona->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Modificar">  
    </form>

<?php } else { ?>

Se modificó la zona " <?php echo $obj->nombreZona->valor;?> " con éxito 

<form method="post" action="MostrarZonas.php">
    <input type="submit" name="reload" value="Ver Zonas">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>