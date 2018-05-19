<?php 

/**
* 
*/
class ConcentradoProyecto
{

  public static function obtenerAreasProyectos($id_zona,$empresa_id){
    $conec = new Conexion;
    $conexion = $conec->abreConexion();
    $sql = "

    select distinct ap.descripcion, ap.id from area_proyecto ap 
    join proyecto p on ap.id = p.area_id
    join empresa_proyecto ep on ep.proyecto_id = p.id where ap.zona_id = '".$id_zona."' and ep.empresa_id = '".$empresa_id."'


    ";

    $stmt = sqlsrv_query( $conexion, $sql);
    $datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
          $datos[] = $obj;         
        }
    return $datos;
  }

}
 ?>