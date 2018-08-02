<?php 


class empresaProyecto {

public static function verificaStatusEmpresaProyecto($proyecto_id,$empresa_id)
    {
        
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $sql = 
                " SELECT ep.id, ep.empresa_id, ep.proyecto_id, ep.status, ep.periodo, ep.nombre_alumno, ep.apellidop_alumno, ep.apellidom_alumno, ep.nombre_proyecto from empresa_proyecto ep join proyecto p on ep.proyecto_id = p.id join empresa e on ep.empresa_id = e.id where ep.proyecto_id = ".$proyecto_id." and ep.empresa_id = ".$empresa_id." and ep.status !=0";

        $stmt = sqlsrv_query( $conexion, $sql);
        $datos = array();

        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            $datos[] =  $obj;       
        }
        return $datos;

    }

}


 ?>