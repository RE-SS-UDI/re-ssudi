<?php
	
	function limpiarCadena($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
	
	function validarFrase( $datoValidar ){
		if( empty( $datoValidar ) )
			return "*Se requiere un nombre ";
        else if( !preg_match( "/^[a-zA-Z ]*$/",  $datoValidar ) ) 
			return "*Solo letras y espacios son aceptados";
		return "";
	}
	
	function validarContrasenia( $datoValidar ){
		if( empty( $datoValidar ) )
			return "*Se requiere un contraseña ";
		else if( strlen( $datoValidar ) < 8 )
			return "*La clave debe tener al menos 8 caracteres";
		else if( strlen( $datoValidar ) > 20 )
			return "*La clave no puede tener más de 20 caracteres";
		else if( !preg_match( "/(?=[a-z])/" , $datoValidar ) )
			return "*La clave debe tener al menos una letra minúscula";     
		else if( !preg_match( "/(?=[A-Z])/", $datoValidar ) )
			return "*La clave debe tener al menos una letra mayúscula";
		else if( !preg_match( "/(?=\d)/", $datoValidar ) )
			return "*La clave debe tener al menos un caracter numérico";
		return "";
	}
	
	function validarContrasenias( $contrasenia, $contrasenia2 ){
		if( strcmp( $contrasenia, $contrasenia2 ) != 0 )
			return "*Las contraseñas no coinciden";
		return "";
	}

?>