<?php $titulo="Modificar Mi Perfil"; ?>
<?php include('../assets/InicioDocumento.php'); ?>
<?php include('../assets/Atributo.php');?>


<?php
    Atributo::configurar($conn, "perfil");
    $obj = (object) array('nombreUsuario' => new Atributo("nombreUsuario", TipoDeDato::PALABRAS, 15, $null=false, $unique=true),
                          'contrasenia'  => new Atributo("contrasenia",  TipoDeDato::CADENA , 20, $null=false),
                          'repetirContrasenia'  => new Atributo("repetirContrasenia",  TipoDeDato::CADENA , 20, $null=false));
	if( $_SESSION["tipoUsuario"] == "Empresa" ){
			Atributo::configurar($conn, "empresa");
			$objE = (object) array('rfc'             => new Atributo("rfc", TipoDeDato::CADENA, 13, $null=true),
								  'razonSocial'     => new Atributo("razonSocial",  TipoDeDato::CADENA , 50, $null=false),
								  'telefono'        => new Atributo("telefono",  TipoDeDato::CADENA , 10, $null=true),
								  'calle'           => new Atributo("calle",  TipoDeDato::CADENA , 20, $null=true),
								  'numInterior'     => new Atributo("numInterior",  TipoDeDato::CADENA , 5, $null=true),
								  'numExterior'     => new Atributo("numExterior",  TipoDeDato::CADENA , 5, $null=true),
								  'colonia'         => new Atributo("colonia",  TipoDeDato::CADENA , 20, $null=true),
								  'municipio'       => new Atributo("municipio",  TipoDeDato::CADENA , 20, $null=true),
								  'email'           => new Atributo("email",  TipoDeDato::EMAIL , 30, $null=true),
								  'codigoPostal'    => new Atributo("codigoPostal",  TipoDeDato::NUMERICO , 11, $null=true),
								  'nombreEmpresa'	=> new Atributo("nombreEmpresa", TipoDeDato::CADENA, 40, $null));
	}
    	
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
			
			$sqlActualizar = "UPDATE usuario SET nombreUsuario='".$obj->nombreUsuario->valor."', contrasenia='".$obj->contrasenia->valor."' where idUsuario='".$_SESSION["idUsuario"]."'";
            $stmt = $conn->prepare($sqlActualizar);
            
            if($stmt->execute())
                header ("Location: ../miPerfil/miPerfil.php");
            
        }
		
		if( $_SESSION["tipoUsuario"] == "Empresa" ){
			//VALIDANDO QUE EL RFC SEA UNICO, IGNORANDO EL REGISTRO QUE SE ESTA EDITANDO
			$sql = "SELECT idEmpresa FROM empresa WHERE rfc='".$objE->rfc->valor."' AND idUsuario<>'".$_SESSION["idUsuario"]."' and elimina_empresa=0";
			$resultado = $conn->query($sql);
			if ($resultado->num_rows > 0 && !empty($objE->rfc->valor)){
				$objE->rfc->error = "*Este registro ya existe, ingrese otro";
			}
			//VALIDAR RFC
			if (!empty($objE->rfc->valor) && !rfcValido($objE->rfc->valor)){
				$objE->rfc->error = " *Ingrese un RFC valido";
			}
						
			$esValido = true;
			foreach($objE as $key=>$value) {
				if(!$value->esValido()){
					$esValido = false;
					break;
				}
			}
			//Si todo los datos son validos y no hay repetidos
			if($esValido){
				$sql="UPDATE empresa SET rfc=?, razonSocial=?, telefono=?, calle=?, numInterior=?, numExterior=?, colonia=?, municipio=?, email=?, codigoPostal=?,  nombreEmpresa=? where Usuario_idUsuario='".$_SESSION["idUsuario"]."' and elimina_empresa=0";
				$stmt = $conn->prepare($sql);
				if($stmt->bind_param("sssssssssis", $obj->rfc->valor, $obj->razonSocial->valor, $obj->telefono->valor,
									 $obj->calle->valor, $obj->numInterior->valor, $obj->numExterior->valor,
									 $obj->colonia->valor, $obj->municipio->valor, $obj->email->valor,
									 $obj->codigoPostal->valor, $obj->nombreEmpresa->valor))
				
				if($stmt->execute())
					$registroAgregado = true;
			}
		}     
    }
	else{
		$sql = "SELECT nombreUsuario, contrasenia FROM usuario where idUsuario = '".$_SESSION["idUsuario"]."' and elimina_usuario = 0";
		$resultadoUsuario = $conn->query($sql);
		if( $resultadoUsuario->num_rows > 0 ){		
			if($row = $resultadoUsuario->fetch_assoc()) {
				$obj->nombreUsuario->valor = $row["nombreUsuario"];
				$obj->contrasenia->valor = $row["contrasenia"];
				$obj->repetirContrasenia->valor = $row["contrasenia"];
				
				//Limpiar errores
                foreach($obj as $key=>$value) {
                    if(!$value->esValido()){
                        $value->error = "";
                    }
                }
			}
		}
		if( $_SESSION["tipoUsuario"] == "Empresa" ){
			$sql = "SELECT * FROM empresa WHERE Usuario_idUsuario = '".$_SESSION["idUsuario"]."' and elimina_empresa = 0";
			$resultadoUsuario = $conn->query($sql);
			if( $resultadoUsuario->num_rows > 0 ){		
				if($row = $resultadoUsuario->fetch_assoc()) {
					$objE->rfc->valor = $row["rfc"];
					$objE->razonSocial->valor = $row["razonSocial"];
					$objE->nombreEmpresa->valor = $row["nombreEmpresa"];
					$objE->telefono->valor = $row["telefono"];
					$objE->calle->valor = $row["calle"];
					$objE->numInterior->valor = $row["numInterior"];
					$objE->numExterior->valor = $row["numExterior"];
					$objE->colonia->valor = $row["colonia"];
					$objE->municipio->valor = $row["municipio"];
					$objE->email->valor = $row["email"];
					$objE->codigoPostal->valor = $row["codigoPostal"];
					
					//Limpiar errores
					foreach($objE as $key=>$value) {
						if(!$value->esValido()){
							$value->error = "";
						}
					}
				}
			}
		}
	}

?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        
		
        <label>Nombre Perfil</label> <span class="error"><?php echo $obj->nombreUsuario->error;?></span>
        <input type="text" name="<?php echo $obj->nombreUsuario->nombre;?>" value="<?php echo $obj->nombreUsuario->valor;?>">
        
        <label>Contraseña</label> <span class="error"><?php echo $obj->contrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->contrasenia->nombre;?>" value="<?php echo $obj->contrasenia->valor;?>">
        
        <label>Repetir Contraseña</label> <span class="error"><?php echo $obj->repetirContrasenia->error;?></span><br>
        <input type="password" name="<?php echo $obj->repetirContrasenia->nombre;?>">
		<?php if( $_SESSION["tipoUsuario"] == "Empresa" ){ ?>
			<label>RFC</label> <span class="error"><?php echo $objE->rfc->error;?></span>
			<input type="text" name="<?php echo $objE->rfc->nombre;?>" value="<?php echo $objE->rfc->valor;?>">
			
			<label>Razon Social</label> <span class="error"><?php echo $objE->razonSocial->error;?></span>
			<input type="text" name="<?php echo $objE->razonSocial->nombre;?>" value="<?php echo $objE->razonSocial->valor;?>">
			
			<label>Nombre de la Empresa</label> <span class="error"><?php echo $objE->nombreEmpresa->error;?></span>
			<input type="text" name="<?php echo $objE->nombreEmpresa->nombre;?>" value="<?php echo $objE->nombreEmpresa->valor;?>">
			
			<label>Teléfono</label> <span class="error"><?php echo $objE->telefono->error;?></span>
			<input type="text" name="<?php echo $objE->telefono->nombre;?>" value="<?php echo $objE->telefono->valor;?>">
			
			<label>Calle</label> <span class="error"><?php echo $objE->calle->error;?></span>
			<input type="text" name="<?php echo $objE->calle->nombre;?>" value="<?php echo $objE->calle->valor;?>">
			
			<label>Número Exterior</label> <span class="error"><?php echo $objE->numExterior->error;?></span>
			<input type="text" name="<?php echo $objE->numExterior->nombre;?>" value="<?php echo $objE->numExterior->valor;?>">
			
			<label>Número Interior</label> <span class="error"><?php echo $objE->numInterior->error;?></span>
			<input type="text" name="<?php echo $objE->numInterior->nombre;?>" value="<?php echo $objE->numInterior->valor;?>">
			
			<label>Colonia</label> <span class="error"><?php echo $objE->colonia->error;?></span>
			<input type="text" name="<?php echo $objE->colonia->nombre;?>" value="<?php echo $objE->colonia->valor;?>">
			
			<label>Municipio</label> <span class="error"><?php echo $objE->municipio->error;?></span>
			<input type="text" name="<?php echo $objE->municipio->nombre;?>" value="<?php echo $objE->municipio->valor;?>">
			
			<label>E-mail</label> <span class="error"><?php echo $objE->email->error;?></span>
			<input type="text" name="<?php echo $objE->email->nombre;?>" value="<?php echo $objE->email->valor;?>">
			
			<label>Código Postal</label> <span class="error"><?php echo $objE->codigoPostal->error;?></span>
			<input type="text" name="<?php echo $objE->codigoPostal->nombre;?>" value="<?php echo $objE->codigoPostal->valor;?>">
		<?php } ?>
                
        <br><br>
        <input type="submit" name="submit" value="Modificar">  
    </form>


<?php include('../assets/FinDocumento.php'); ?>