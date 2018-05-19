<?php 

/**
* 
*/
class Proyecto
{


	public static function devolverProyectoGeneral($areaproyecto_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = 
        		" SELECT p.descripcion, p.id
                  FROM proyecto p
                  JOIN area_proyecto ap
                  on ap.id = p.area_id
                  WHERE ap.id = ".$areaproyecto_id;

        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();

        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;
    }//funcion
	

    public static function devolverProyecto($empresa_id,$areaproyecto_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = " SELECT distinct p.descripcion from empresa_proyecto ep
		JOIN empresa e
		on e.id = ep.empresa_id
		JOIN proyecto p
		on p.id = ep.proyecto_id
		JOIN area_proyecto ap
		on ap.id = p.area_id
		where ep.empresa_id = ".$empresa_id." and ap.id = ".$areaproyecto_id."";


		$stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	return $obj;
        }
	}

	 public static function devolverProyectoFiltrado($empresa_id,$areaproyecto_id,$descripcion)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = " SELECT distinct p.descripcion from empresa_proyecto ep
		JOIN empresa e
		on e.id = ep.empresa_id
		JOIN proyecto p
		on p.id = ep.proyecto_id
		JOIN area_proyecto ap
		on ap.id = p.area_id
		where ep.empresa_id = ".$empresa_id." and ap.id = ".$areaproyecto_id." and p.descripcion = '".$descripcion."'";


		$stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	return $obj;
        }
	}

    public static function verificaProyecto($empresa_id,$areaproyecto_id)
	{
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = " SELECT distinct p.descripcion, ap.descripcion from empresa_proyecto ep
		JOIN empresa e
		on e.id = ep.empresa_id
		JOIN proyecto p
		on p.id = ep.proyecto_id
		JOIN area_proyecto ap
		on ap.id = p.area_id
		where ep.empresa_id = ".$empresa_id." and ap.id = ".$areaproyecto_id."";


		$stmt = sqlsrv_query( $conexion, $sql);
		$respuesta;
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	if ($obj->descripcion == '') {
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
                  WHERE p.id = '".$persona_id."'";
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