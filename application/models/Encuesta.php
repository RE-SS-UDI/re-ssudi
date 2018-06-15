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
	    }	
    }

    public static function eliminaEncuestasAsignadasByEncuesta($encuesta_id){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "DELETE zona_encuesta where id = ".$encuesta_id." ";

	    $s = sqlsrv_prepare($conexion, $sql);

	    try {
            sqlsrv_execute($s);
            return My_Comun::mensaje(1);
	    } catch (Exception $e) {
            return My_Comun::mensaje(3);
	        print_r($e);
	        exit;
	    }	
    }

    public static function changeStatusEncuestasAsignadasByEncuesta($encuesta_id, $status){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $estatus = true;
        $sql = "";
        echo "<script>console.log( 'Debug Objects: " . $status . "' );</script>";

        if ($status == 1) {
            $estatus = false;
            $sql = "UPDATE zona_encuesta SET status = 0, updated_at = GETDATE() WHERE id = ".$encuesta_id." ";
        }else{
            $sql = "UPDATE zona_encuesta SET status = 1, updated_at = GETDATE() WHERE id = ".$encuesta_id." ";
        }
        echo "<script>console.log( 'Debug Objects: " . $sql . "' );</script>";


        $s = sqlsrv_prepare($conexion, $sql);

            try {
                sqlsrv_execute($s);
                if ($status){
                    return My_Comun::mensaje(2);
                }else{
                    return My_Comun::mensaje(4);
                }
            } catch (Exception $e) {
                if ($status){
                    return My_Comun::mensaje(3);
                }else{
                    return My_Comun::mensaje(5);
                }
                print_r($e);
                exit;
            }	
	
    }
    
    public static function inactiveEncuestasAsignadas($zona_id){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "UPDATE zona_encuesta SET status = 0, updated_at = GETDATE() WHERE zona_id = ".$zona_id." ";

	    $s = sqlsrv_prepare($conexion, $sql);

	    try {
	        sqlsrv_execute($s);
	    } catch (Exception $e) {
	        print_r($e);
	        exit;
	    }	
	}

    public static function verificaEncuesta($persona_id,$encuesta_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = " SELECT distinct concat(persona.nombre,' ',persona.apellido_pat,' ',persona.apellido_mat) as nombre, encuesta.nombre as encuesta, encuesta.id
			  FROM persona
			  INNER JOIN respuesta
			  on respuesta.persona_id = persona.id
			  INNER JOIN pregunta
			  on pregunta.id = respuesta.pregunta_id
			  INNER JOIN encuesta
			  on encuesta.id = pregunta.encuesta_id
			  WHERE persona.id = ".$persona_id." AND encuesta.id = ".$encuesta_id."";

		$stmt = sqlsrv_query( $conexion, $sql);
		$respuesta;
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	if ($obj->nombre == '') {
        		$respuesta = 0;
        	} else {
        		$respuesta = 1;
        	}
        }

		return $respuesta;

	}

    public static function obtieneEncuestasPersona($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "  SELECT e.id,e.nombre
                  FROM encuesta e
                  INNER JOIN zona_encuesta ze
                  on e.id = ze.encuesta_id
                  INNER JOIN zona z
                  on z.id = ze.zona_id
                  INNER JOIN empresa em
                  on z.id = em.zona_id
                  INNER JOIN persona p
                  on em.id = p.empresa_id         
                  WHERE p.id = '".$persona_id."'
                ";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    public static function obtieneEncuesta($persona_id,$encuesta_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "  SELECT e.id,e.nombre
                  FROM encuesta e
                  INNER JOIN zona_encuesta ze
                  on e.id = ze.encuesta_id
                  INNER JOIN zona z
                  on z.id = ze.zona_id
                  INNER JOIN empresa em
                  on z.id = em.zona_id
                  INNER JOIN persona p
                  on em.id = p.empresa_id         
                  WHERE p.id = '".$persona_id."' and e.id = '".$encuesta_id."'
                ";
        //print_r($sql);
        //exit;
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = '';
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos =  $obj;       
        }
        return $datos;
    }//funcion

}
 ?>