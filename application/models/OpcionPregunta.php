<?php 
/**
* 
*/
class OpcionPregunta
{

	public static function eliminarOpciones($pregunta_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "DELETE opciones_pregunta where pregunta_id = ".$pregunta_id." ";

	    $s = sqlsrv_prepare($conexion, $sql);

	    try {
	        sqlsrv_execute($s);
	    } catch (Exception $e) {
	        print_r($e);
	        exit;
	    }

	}
}
 ?>