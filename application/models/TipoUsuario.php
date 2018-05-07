<?php 
/**
* 
*/
class TipoUsuario
{
    public static function guardarPermisos($permisos,$id){

//        print_r($permisos);
//        exit;
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $cadena="";
        foreach($permisos as $permiso){
                $cadena.=(empty($cadena))?$permiso:"|".$permiso;
        }//foreach
        $consulta = "UPDATE dbo.tipo_usuario set permisos = '".$cadena."' WHERE id = ".$id;

            $s = sqlsrv_prepare($conexion, $consulta);

            try {
                sqlsrv_execute($s);
            } catch (Exception $e) {
                print_r($e);
                exit;
            }

//      Doctrine_Query::create()->update('Usuario')->set('permisos','?',$cadena)->where('id=?',$id)->execute();
        $regi=My_Comun::obtenerSQL("tipo_usuario", "id", $id);
        Bitacora::guardar('Tipo Usuario','Permisos de tipo de usuario',$regi->nombre);
    }//function
}

 ?>