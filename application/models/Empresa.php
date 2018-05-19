<?php 

/**
* 
*/
class Empresa
{



	    public static function obtenerIdEmpresa($usuario_id){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select e.id from usuario u 
        join persona p on u.persona_id = p.id
        join empresa e on p.empresa_id = e.id
        where u.id = ".$usuario_id;


		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}   

}

?>