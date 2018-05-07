<?php 
/**
* 
*/
class Preguntas
{
	
	public static function obtienePreguntasEncuesta($encuesta_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "  SELECT p.id, p.descripcion, p.tipo, p.encuesta_id, p.status
				  FROM pregunta p
				  WHERE p.status = 1 AND encuesta_id = ".$encuesta_id."
				";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
	}
}
 ?>