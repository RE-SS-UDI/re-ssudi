<?php 
/**
* 
*/
class Conexion
{
	
	public static function abreConexion()
	{
		    $user = 'Admin';
            $pass = 'admin123';
            $database = 'ca02_db';  
            $serverName = "XHIROX\SQLEXPRESS"; //serverName\instanceName
            $connectionInfo = array( "Database"=>$database, "UID"=>$user, "PWD"=>$pass);
            $conn = sqlsrv_connect( $serverName, $connectionInfo);

            if( $conn ) {

            	return $conn;
                 //echo "Conexión establecida.<br />";

            }else{
                 echo "Conexión no se pudo establecer.<br />";
                 die( print_r( sqlsrv_errors(), true));

            }

	}
}
 ?>