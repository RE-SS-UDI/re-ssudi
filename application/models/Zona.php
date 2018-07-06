<?php 

/**
* 
*/
class Zona
{
	
	public static function obtieneZonaUsuario($persona_id){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "SELECT z.id as zona
				  FROM zona z
				  JOIN empresa e
				  ON z.id = e.zona_id
				  JOIN persona p
				  ON e.id = p.empresa_id
				  WHERE p.id = ".$persona_id."
				";

        $stmt = sqlsrv_query( $conexion, $sql);
        if( $obj = sqlsrv_fetch_object($stmt)) {

            return $obj->zona;
        }
    }
}
 ?>