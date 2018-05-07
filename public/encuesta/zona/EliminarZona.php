<?php $titulo="Eliminar Zona"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>

<?php
//Revisar que se envio el get id por URL
    if(empty($_GET["id"])){
?>
        <form method="get" action="EliminarZona.php">
            <input type="text" name="id" placeholder="id Zona">
            <input type="submit" value="Eliminar Zona">
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
        <form action="EliminarZona.php">
            <input type="submit" value="Eliminar otra">
        </FORM>
<?php
        return; //Termina el codigo
    } 
    Atributo::configurar($conn, "zona");
    $obj = (object) array('nombreZona' => new Atributo("nombreZona", TipoDeDato::ALFANUMERICO, 50, $null=false, $unique=true));   
	$registroAgregado = false;
	  

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
		
		$sqlActualizar = "UPDATE zona SET elimina_zona=1 WHERE idZona=".$idModificar;
        $sql = $conn->prepare($sqlActualizar);
            
        if($sql->execute())
            $registroAgregado = true;
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
        
		
        <label>Nombre Zona</label><br>
        <label><?php echo $obj->nombreZona->valor;?></label>
                
        <br><br>
        <input type="submit" name="submit" value="Eliminar" onclick="return confirm('¿Esta seguro que desea eliminarlo?')">  
    </form>

<?php } else { ?>

Se eliminó la zona " <?php echo $obj->nombreZona->valor;?> " con éxito 

<form method="post" action="MostrarZonas.php">
    <input type="submit" name="reload" value="Ver zonas">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>