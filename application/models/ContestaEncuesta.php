<?php 
/**
* 
*/
class ContestaEncuesta
{
	
	public static function obtieneEncuestas($usuario_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = "SELECT p.id, p.nombre, p.apellido_pat, p.apellido_mat 
                  FROM persona p
                  WHERE id not in(
                    SELECT p.id
                    FROM persona p
                    inner join usuario u
                    on u.persona_id = p.id
                  )";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
	}

    public static function obtieneEncuestasUsuario($persona_id)
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
                  INNER JOIN usuario u
                  on u.persona_id = p.id
                  WHERE u.persona_id = '".$persona_id."'
                ";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    public static function obtieneEncuestasUsuarioZona($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        
         $sql = "SELECT e.id,e.nombre,z.id as 'zona_id', z.nombre as 'zona_nombre'
                FROM encuesta e
                INNER JOIN zona_encuesta ze
                on e.id = ze.encuesta_id
                INNER JOIN zona z
                on z.id = ze.zona_id
                INNER JOIN persona_zona em
                on z.id = em.zona_id        
                WHERE em.persona_id = '".$persona_id."'
              ";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    public static function obtieneEncuestas_UsuarioZonaByIdconcentrado($persona_id,$zona_id,$tipo_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        
        //  $sql = "SELECT e.id,e.nombre,z.nombre as 'Zona_nombre'
        //         FROM encuesta e
        //         INNER JOIN zona_encuesta ze
        //         on e.id = ze.encuesta_id
        //         INNER JOIN zona z
        //         on z.id = ze.zona_id
        //         INNER JOIN persona_zona em
        //         on z.id = em.zona_id        
        //         WHERE em.persona_id = '".$persona_id."' and z.id = '".$zona_id."'
        //       ";
        $sql = "SELECT distinct e.id,e.nombre
        from persona p
		inner join respuesta res
		on p.id = res.persona_id
		inner join pregunta pre
		on pre.id = res.pregunta_id
		inner join encuesta e
		on e.id = pre.encuesta_id      
        WHERE e.status = 1 AND res.tipo_persona_id = '".$tipo_id."' AND res.persona_id = '".$persona_id."' AND res.zona_id = '".$zona_id."'
      ";
              
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    

    public static function obtieneEncuestas_UsuarioZonaById($persona_id,$zona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        
        //  $sql = "SELECT e.id,e.nombre,z.nombre as 'Zona_nombre'
        //         FROM encuesta e
        //         INNER JOIN zona_encuesta ze
        //         on e.id = ze.encuesta_id
        //         INNER JOIN zona z
        //         on z.id = ze.zona_id
        //         INNER JOIN persona_zona em
        //         on z.id = em.zona_id        
        //         WHERE em.persona_id = '".$persona_id."' and z.id = '".$zona_id."'
        //       ";
        $sql = "SELECT e.id,e.nombre,z.nombre as 'Zona_nombre',tp.id as tipo_persona_id, tp.descripcion as tipo_persona
        FROM encuesta e
        INNER JOIN zona_encuesta ze
        on e.id = ze.encuesta_id
        INNER JOIN tipo_persona tp
        on tp.id = ze.tipo_id
        INNER JOIN zona z
        on z.id = ze.zona_id
        INNER JOIN persona_zona em
        on z.id = em.zona_id        
        WHERE e.status = 1 AND ze.tipo_id = em.tipo_id AND em.persona_id = '".$persona_id."' AND z.id = '".$zona_id."'
      ";
              
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    
    public static function obtieneEncuestas_UsuarioZonaTipoById($persona_id, $zona_id, $tipo_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        
        $sql = "SELECT e.id,e.nombre,z.nombre as 'Zona_nombre',tp.id as tipo_persona_id, tp.descripcion as tipo_persona
        FROM encuesta e
        INNER JOIN zona_encuesta ze
        on e.id = ze.encuesta_id
        INNER JOIN tipo_persona tp
        on tp.id = ze.tipo_id
        INNER JOIN zona z
        on z.id = ze.zona_id
        INNER JOIN persona_zona em
        on z.id = em.zona_id        
        WHERE ze.status = 1 AND ze.tipo_id = em.tipo_id AND em.persona_id = '".$persona_id."' AND z.id = '".$zona_id."' AND ze.tipo_id = '".$tipo_id."'
      ";
              
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    public static function obtieneTiposXZonas($zona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $per = Zend_Auth::getInstance()->getIdentity()->persona_id;

        $sql = "SELECT tp.*
        FROM tipo_persona tp  
        inner join persona_zona pz
        on pz.tipo_id = tp.id 
        WHERE tp.status = 1 AND pz.persona_id = ".$per."  AND tp.zona_id = '".$zona_id."' 
        ";
              
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

    public static function obtieneZona_UsuarioZona($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        
         $sql = "SELECT DISTINCT z.id,z.nombre as 'zona_nombre'
                FROM encuesta e
                INNER JOIN zona_encuesta ze
                on e.id = ze.encuesta_id
                INNER JOIN zona z
                on z.id = ze.zona_id
                INNER JOIN persona_zona em
                on z.id = em.zona_id        
                WHERE em.persona_id = '".$persona_id."'
              ";
        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion

}
 ?>