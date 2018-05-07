<?php 
/**
* 
*/
class Respuesta
{
	
	public static function obtieneRespuesta($persona_id,$pregunta_id)
	{
//		print_r($persona_id.'-'.$pregunta_id);
//		exit;
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "  SELECT r.id, r.descripcion
				  FROM respuesta r
				  INNER JOIN persona p
				  on p.id = r.persona_id
				  INNER JOIN pregunta pr
				  on pr.id = r.pregunta_id
				  WHERE r.persona_id = ".$persona_id." AND r.pregunta_id = ".$pregunta_id."
				";
        $stmt = sqlsrv_query( $conexion, $sql);

        if( $obj = sqlsrv_fetch_object($stmt)) {
        
			return $obj;
        }
        
	}

	public static function eliminaRespuestaTipo($persona_id,$pregunta_id,$tipo)
	{
//		print_r($persona_id.'-'.$tipo);
//		exit;
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "DELETE respuesta
				  WHERE persona_id = ".$persona_id." AND pregunta_id = ".$pregunta_id." AND tipo = '".$tipo."' ";
        $stmt = sqlsrv_query( $conexion, $sql);

        if( $obj = sqlsrv_fetch_object($stmt)) {
        
			return $obj;
        }
        
	}

	public static function obtieneRespuestaEspecial($descripcion,$pregunta_id)
	{
//		print_r($persona_id.'-'.$pregunta_id);
//		exit;
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "  SELECT r.id
				  FROM respuesta r
				  INNER JOIN pregunta pr
				  on pr.id = r.pregunta_id
				  WHERE r.descripcion = ".$descripcion." AND r.pregunta_id = ".$pregunta_id." AND r.persona_id = ".Zend_Auth::getInstance()->getIdentity()->persona_id."
				";
        $stmt = sqlsrv_query( $conexion, $sql);

        if( $obj = sqlsrv_fetch_object($stmt)) {
        
			return $obj;
        }
        
	}

	public static function eliminaRespuestaSeleccionada($persona_id,$pregunta_id,$tipo,$valor)
	{
//		print_r($persona_id.'-'.$tipo);
//		exit;
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "DELETE respuesta WHERE persona_id = ".$persona_id." AND pregunta_id = ".$pregunta_id." AND tipo = '".$tipo."' AND descripcion = '".$valor."' ";
        $stmt = sqlsrv_query( $conexion, $sql);

        if( $obj = sqlsrv_fetch_object($stmt)) {
        
			return '1';
        }
        
	}//function
}//clase
 ?>