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

    public static function obtieneEncuestasUsuario($usuario_id)
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
                  WHERE u.id = '".$usuario_id."'
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