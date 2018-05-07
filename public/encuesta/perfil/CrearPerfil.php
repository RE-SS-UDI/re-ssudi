<?php $titulo="Crear Perfil";?>
<?php include('../assets/InicioDocumento.php');?>
<?php include('../assets/Atributo.php');?>

<?php
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombrePerfil' => new Atributo("nombrePerfil", TipoDeDato::PALABRAS, 20, $null=false, $unique=true),
                          'descripcionPerfil'  => new Atributo("descripcionPerfil",  TipoDeDato::CADENA , 300, $null=true));

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
        
        //Si todo los datos son validos y no hay repetidos
        if($esValido){
            $stmt = $conn->prepare("INSERT INTO perfil (nombrePerfil,descripcionPerfil) VALUES (?,?)");
            if($stmt->bind_param("ss", $obj->nombrePerfil->valor, $obj->descripcionPerfil->valor))
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
        
        <label>Nombre Perfil</label> <span class="error"><?php echo $obj->nombrePerfil->error;?></span>
        <input type="text" name="<?php echo $obj->nombrePerfil->nombre;?>" value="<?php echo $obj->nombrePerfil->valor;?>">
        
        <label>Descripción</label> <span class="error"><?php echo $obj->descripcionPerfil->error;?></span><br>
        <textarea name="<?php echo $obj->descripcionPerfil->nombre;?>" rows="5" cols="50"><?php echo $obj->descripcionPerfil->valor;?></textarea>
        
        <br><br>
        <input type="submit" name="submit" value="Crear">  
    </form>

<?php } else { ?>

Se agrego el perfil " <?php echo $obj->nombrePerfil->valor;?> " con exito 

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input type="submit" name="reload" value="Agregar otro perfil">
</FORM>

<?php } ?>

<?php include('../assets/FinDocumento.php'); ?>