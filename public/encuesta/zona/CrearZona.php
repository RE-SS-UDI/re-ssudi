<?php $titulo="Crear Zona"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>

<?php
    Atributo::configurar($conn, "zona");
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
			
            $stmt = $conn->prepare("INSERT INTO zona (nombreZona) VALUES (?)");
            if($stmt->bind_param("s", $obj->nombreZona->valor))
            //Parametros del primer argumento de bind_param:           
            //i - integer
            //d - double
            //s - string
            //b - BLOB
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
    }
    
    if(!$registroAgregado){
?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        
        <label>Nombre</label> <span class="error"><?php echo $obj->nombreZona->error;?></span>
        <input type="text" name="<?php echo $obj->nombreZona->nombre;?>" value="<?php echo $obj->nombreZona->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Crear">  
    </form>

<?php } else { ?>

Se agregó la zona " <?php echo $obj->nombreZona->valor;?> " con éxito 

<form method="get" action="CrearZona.php">
    <input type="submit" name="otro" value="Agregar otra zona">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>