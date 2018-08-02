<?php 

/**
* 
*/
class Estado
{
	

    public static function changeStatus($estado_id, $status){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $estatus = true;
        $sql = "";
        // echo "<script>console.log( 'Debug Objects: " . $status . "' );</script>";

        if ($status == 1) {
            $estatus = false;
            $sql = "UPDATE estados SET status = 0, updated_at = GETDATE() WHERE id_estado = ".$estado_id." ";
        }else{
            $sql = "UPDATE estados SET status = 1, updated_at = GETDATE() WHERE id_estado = ".$estado_id." ";
        }
        // echo "<script>console.log( 'Debug Objects: " . $sql . "' );</script>";


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
    
   

}
 ?>