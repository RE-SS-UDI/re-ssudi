<?php $titulo="Crear Usuario Empresa"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>
<?php include('ValidarRFC.php');?>
<?php
	if( $_SESSION["tipoUsuario"] == "Empresa" || $_SESSION["tipoUsuario"] == "Encuestador")
		header ("Location: ../miPerfil/miPerfil.php");
?>

<?php
    Atributo::configurar($conn, "empresa");
    $obj = (object) array('rfc'             => new Atributo("rfc", TipoDeDato::CADENA, 14, $null=true, $unique=true),
                          'razonSocial'     => new Atributo("razonSocial",  TipoDeDato::CADENA , 50, $null=false),
                          'telefono'        => new Atributo("telefono",  TipoDeDato::CADENA , 10, $null=true),
                          'calle'           => new Atributo("calle",  TipoDeDato::CADENA , 20, $null=true),
                          'numInterior'     => new Atributo("numInterior",  TipoDeDato::CADENA , 5, $null=true),
                          'numExterior'     => new Atributo("numExterior",  TipoDeDato::CADENA , 5, $null=true),
                          'colonia'         => new Atributo("colonia",  TipoDeDato::CADENA , 20, $null=true),
                          'municipio'       => new Atributo("municipio",  TipoDeDato::CADENA , 20, $null=true),
                          'email'           => new Atributo("email",  TipoDeDato::EMAIL , 30, $null=true),
                          'codigoPostal'    => new Atributo("codigoPostal",  TipoDeDato::NUMERICO , 11, $null=true),
                          'idUsuario'       => new Atributo("idUsuario",  TipoDeDato::NUMERICO , 11, $null=false),
						  'nombreEmpresa'	=> new Atributo("nombreEmpresa", TipoDeDato::CADENA, 40, $null));
    
    $registroAgregado = false;

    //Si se recibio una respuesta por parte del formulario realiza lo siguiente:
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Vualve a cargar la pagina para agregar otro registro
        if (!empty($_POST["reload"])){
            unset($_POST["reload"]);
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        //VALIDAR RFC
        if (!empty($obj->rfc->valor) && !rfcValido($obj->rfc->valor)){
            $obj->rfc->error = " *Ingrese un RFC valido";
        }
        
        //VALIDAR USUARIO
        if (!empty($obj->idUsuario->valor) && is_numeric($obj->idUsuario->valor)){
            $sql = "SELECT idUsuario FROM usuario WHERE idUsuario = '".$obj->idUsuario->valor."'";
            $resultadoidUsuario = $conn->query($sql);
            if ($resultadoidUsuario->num_rows <= 0){
                $obj->idUsuario->error = " *Lo sentimos, ese usuario no existe";
            } else {
                $sql = "SELECT Usuario_idUsuario FROM empresa WHERE Usuario_idUsuario = '".$obj->idUsuario->valor."'";
                $resultadoEmpresa = $conn->query($sql);
                if ($resultadoEmpresa->num_rows > 0){
                    $obj->idUsuario->error = " *Este usuario ya esta vinculado a una empresa";
                }
            }
                
        }else if(is_numeric($obj->idUsuario->valor)){
            $obj->idUsuario->error = " *Ingrese un usuario";
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
            $stmt = $conn->prepare("INSERT INTO empresa (rfc, razonSocial, telefono, calle, numInterior, numExterior, colonia, municipio, email, codigoPostal, Usuario_idUsuario, nombreEmpresa) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            if($stmt->bind_param("sssssssssiis", $obj->rfc->valor, $obj->razonSocial->valor, $obj->telefono->valor,
                                 $obj->calle->valor, $obj->numInterior->valor, $obj->numExterior->valor,
                                 $obj->colonia->valor, $obj->municipio->valor, $obj->email->valor,
                                 $obj->codigoPostal->valor,$obj->idUsuario->valor, $obj->nombreEmpresa->valor))
            
            if($stmt->execute())
                $registroAgregado = true;
            
        }
    }

    if(!$registroAgregado){
?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        
        <label>Usuario (id usuario)</label> <span class="error"><?php echo $obj->idUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->idUsuario->nombre;?>" value="<?php echo $obj->idUsuario->valor;?>">
        
        <a href="../usuario/CrearUsuario.php" target="_blank">+ Ir a crear usuario</a>
        <br><br>
        
        <label>RFC</label> <span class="error"><?php echo $obj->rfc->error;?></span>
        <input type="text" name="<?php echo $obj->rfc->nombre;?>" value="<?php echo $obj->rfc->valor;?>">
        
        <label>Razon Social</label> <span class="error"><?php echo $obj->razonSocial->error;?></span>
        <input type="text" name="<?php echo $obj->razonSocial->nombre;?>" value="<?php echo $obj->razonSocial->valor;?>">
		
        <label>Nombre de la Empresa</label> <span class="error"><?php echo $obj->nombreEmpresa->error;?></span>
        <input type="text" name="<?php echo $obj->nombreEmpresa->nombre;?>" value="<?php echo $obj->nombreEmpresa->valor;?>">
        
        <label>Telefono</label> <span class="error"><?php echo $obj->telefono->error;?></span>
        <input type="text" name="<?php echo $obj->telefono->nombre;?>" value="<?php echo $obj->telefono->valor;?>">
        
        <label>Numero Interior</label> <span class="error"><?php echo $obj->numInterior->error;?></span>
        <input type="text" name="<?php echo $obj->numInterior->nombre;?>" value="<?php echo $obj->numInterior->valor;?>">
        
        <label>Numero Exterior</label> <span class="error"><?php echo $obj->numExterior->error;?></span>
        <input type="text" name="<?php echo $obj->numExterior->nombre;?>" value="<?php echo $obj->numExterior->valor;?>">
        
        <label>Colonia</label> <span class="error"><?php echo $obj->colonia->error;?></span>
        <input type="text" name="<?php echo $obj->colonia->nombre;?>" value="<?php echo $obj->colonia->valor;?>">
        
        <label>Municipio</label> <span class="error"><?php echo $obj->municipio->error;?></span>
        <input type="text" name="<?php echo $obj->municipio->nombre;?>" value="<?php echo $obj->municipio->valor;?>">
        
        <label>e-mail</label> <span class="error"><?php echo $obj->email->error;?></span>
        <input type="text" name="<?php echo $obj->email->nombre;?>" value="<?php echo $obj->email->valor;?>">
        
        <label>Codigo Postal</label> <span class="error"><?php echo $obj->codigoPostal->error;?></span>
        <input type="text" name="<?php echo $obj->codigoPostal->nombre;?>" value="<?php echo $obj->codigoPostal->valor;?>">
        
        <br><br>
        <input type="submit" name="submit" value="Vincular Empresa">  
    </form>

<?php } else { 
        $sql = "SELECT nombreUsuario FROM usuario WHERE idUsuario = '".$obj->idUsuario->valor."'";
        $rasultadoUsuario = $conn->query($sql);
        $usuario = $rasultadoUsuario->fetch_assoc();
?>

Se vinculo la empresa " <?php echo $obj->razonSocial->valor;?> " al usuario " <?php echo $usuario["nombreUsuario"] ?> " con éxito 

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input type="submit" name="reload" value="Agregar otra empresa">
</FORM>

<?php } ?>
<?php include('../assets/FinDocumento.php'); ?>