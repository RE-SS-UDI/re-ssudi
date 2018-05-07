<?php 

/**
* 
*/
class Encuesta
{
	
	public static function eliminaEncuestasAsignadas($zona_id){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "DELETE zona_encuesta where zona_id = ".$zona_id." ";

	    $s = sqlsrv_prepare($conexion, $sql);

	    try {
	        sqlsrv_execute($s);
	    } catch (Exception $e) {
	        print_r($e);
	        exit;
	    }	}
}
 ?>