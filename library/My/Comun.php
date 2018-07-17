<?php
class My_Comun{

	function __construct(){}

	// rutas
    const CORREO = 'esau.toc@hotmail.com';



public static function obtenersiguienteId(){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "SELECT (MAX(ID)+1) AS id FROM empresa_proyecto
				";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
}


public static function obtenerFiltroSQLMensajes($idPer){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				if exists(select permisos from usuario where id = ".$idPer."
				and permisos like '%VER_INICIO%')
				select u.usuario, m.id, m.persona_origen_id, 
				m.persona_destino_id, m.mensaje, m.status, m.asunto, 
				m.duedate_at, m.created_at, m.updated_at from mensaje m 
				join usuario u on m.persona_origen_id = u.id 
				where GETDATE() <= duedate_at and m.status=1 
				and (persona_destino_id= ".$idPer." or persona_destino_id = 0)
				order by m.created_at desc";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
}

public static function mensaje($numero){

		switch($numero)
		{
			case "-5"   : return "El registro no pudo ser guardado porque está duplicado."; break;
			case "1"    : return "El registro fue eliminado"; break;
			case "2"    : return "El registro fue deshabilitado"; break;
			case "3"    : return "Ocurrió un error al tratar de deshabilitar el registro, inténtelo de nuevo"; break;
			case "4"    : return "El registro fue habilitado"; break;
			case "5"    : return "Ocurrió un error al tratar de habilitar el registro, inténtelo de nuevo"; break;
			case "6"    : return "El País que no se puede eliminar porque dependen estados de el"; break;
			case "7"    : return "El registro no pudo ser eliminado ya que tiene información relacionada; por el momento, solo fue DESHABILITADO"; break;
			default     : return "No se encontró la descripción del error. ".$numero; break;    
		}
	}
     
public static function obtenerFiltroSqlUsuariosMensajes($tipo_usuario,$id_zona){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "

			if exists(SELECT * from usuario WHERE tipo_usuario= ".$tipo_usuario." ) 
				select RetVal=1, concat(p.nombre,' ',p.apellido_pat,' ',apellido_mat) as persona, 
				u.usuario, u.id, p.nombre, e.nombre, z.nombre 
				from usuario u join persona p on u.persona_id = p.id join empresa e 
				on p.empresa_id = e.id join zona z on e.zona_id = z.id 
				WHERE tipo_usuario= ".$tipo_usuario." and z.id = ".$id_zona."
				else if (".$tipo_usuario." != 0) 
				select RetVal=0
				else if (".$tipo_usuario." = 0)
				select RetVal=2"
				;
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}


public static function obtenerFiltroSqlUsuariosMensajesAdmins($tipo_usuario){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "

			if exists(SELECT * from usuario WHERE tipo_usuario= ".$tipo_usuario." ) 
				select RetVal=1, concat(p.nombre,' ',p.apellido_pat,' ',apellido_mat) as persona, 
			    u.usuario, u.id, p.nombre, e.nombre 
				from usuario u join persona p on u.persona_id = p.id join empresa e 
				on p.empresa_id = e.id  
				WHERE tipo_usuario= ".$tipo_usuario."
				else if (".$tipo_usuario." != 0) 
				select RetVal=0
				else if (".$tipo_usuario." = 0)
				select RetVal=2";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

	
//obtener mensajes para usuarios especificos y para todos
	public static function obtenerTipoMsje($id){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = " 
				if exists(select tu.id as tipo_usuario_id, tu.descripcion as desc_tu, m.id, m.persona_origen_id, 
					m.persona_destino_id, u.usuario, m.mensaje, m.status, m.asunto, m.duedate_at, m.created_at, 
					m.updated_at from mensaje m
					join usuario u 
					on m.persona_destino_id = u.id
					join tipo_usuario tu
					on tu.id = u.tipo_usuario
					where m.id = ".$id." )
select tu.id as tipo_usuario_id, tu.descripcion as desc_tu, m.id, m.persona_origen_id, 
					m.persona_destino_id, u.usuario, m.mensaje, m.status, m.asunto, m.duedate_at, m.created_at, 
					m.updated_at from mensaje m
					join usuario u 
					on m.persona_destino_id = u.id
					join tipo_usuario tu
					on tu.id = u.tipo_usuario
					where m.id = ".$id." 
else select			0 as tipo_usuario_id, 0 as desc_tu , m.id, m.persona_origen_id, 
					m.persona_destino_id, 'todos' as usuario, m.mensaje, m.status, m.asunto, m.duedate_at, m.created_at, 
					m.updated_at from mensaje m
					where m.id = ".$id;
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}
	
	public static function obtenerFiltroSQLCategorias($id){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = " select distinct c.id, c.nombre  
				from categoria c
				join encuesta e
				on e.categoria_id = c.id
				join zona_encuesta ze
				on ze.encuesta_id = e.id
				join zona z
				on z.id = ze.zona_id
				where z.id =
				 ".$id;
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}
	
public static function obtenerFiltroSQLConcentradoEncuestas($filtro_zona,$filtro_categoria){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		// $sql = "
		// 		select e.id, e.nombre, e.status, e.categoria_id from encuesta e
		// 		join zona_encuesta ze
		// 		on ze.encuesta_id = e.id
		// 		where e.status = 1 ".$filtro_categoria.$filtro_zona."
		// 		order by nombre asc";
		$sql = "
				select e.id, e.nombre, e.status, e.categoria_id from encuesta e
				join zona_encuesta ze
				on ze.encuesta_id = e.id
				where e.status = 1 and 1+1=2 ".$filtro_categoria.$filtro_zona."
				order by e.nombre asc";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}
	public static function obtenerFiltroSQLConcentrado($filtro_zona,$filtro_nombre,$filtro_estado){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		// $sql = "
		// select e.nombre as empresa, p.id, p.nombre, p.apellido_pat, p.apellido_mat, e.zona_id from persona p
		// join empresa e
		// on p.empresa_id = e.id
		// join zona z 
		// on z.id = e.zona_id
		// where p.status = 1 ".$filtro_zona.$filtro_nombre."
		//  order by p.nombre asc";
		$sql = "
		select e.nombre as empresa, p.id, p.nombre, p.apellido_pat, p.apellido_mat, uz.zona_id from persona p
		inner join persona_zona uz
		on p.id = uz.persona_id
		inner join zona zo
		on zo.id = uz.zona_id
		inner join empresa e
		on uz.zona_id = e.zona_id
		where p.status = 1 and 1=1 ".$filtro_zona.$filtro_nombre.$filtro_estado."
		 order by p.nombre asc";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

	public static function obtenerFiltroSQLConcentradoZ($filtro_zona,$filtro_nombre){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
		select e.nombre as empresa, p.id, p.nombre, p.apellido_pat, p.apellido_mat, e.zona_id from persona p
		join empresa e
		on p.empresa_id = e.id
		join zona z 
		on z.id = e.zona_id
		where p.status = 1 ".$filtro_zona.$filtro_nombre."
		 order by p.nombre asc";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

		//FILTROS ZONA PERSONA
	public static function obtenerFiltroSQLEmpresaRoot(){
			$conec = new Conexion;
			$conexion = $conec->abreConexion();
			$sql ="select * from empresa order by nombre asc";
			$stmt = sqlsrv_query( $conexion, $sql);
			$datos = array();
			while( $obj = sqlsrv_fetch_object($stmt)) {
				$datos[] = $obj;		     
			}
			return $datos;
		}

	public static function obtenerFiltroSQLEmpresa($zonaUser){
			$conec = new Conexion;
			$conexion = $conec->abreConexion();
			$sql ="Select * from empresa where zona_id=".$zonaUser." order by nombre asc";
			$stmt = sqlsrv_query( $conexion, $sql);
			$datos = array();
			while( $obj = sqlsrv_fetch_object($stmt)) {
				$datos[] = $obj;		     
			}
			return $datos;
		}

	public static function obtenerFiltroSQLEmpresaZonas($zonaUser){
		$conec = new Conexion;
		$conexion = $conec->abreConexion();
		$zonas= '';
		for($i = 0; $i >= count($zonaUser); $i++){
			$zonas .= " AND zona_id=".$zonaUser[$i]->id;
		}
		// foreach($zonaUser as $zonasU){
		// 	$zonas .= " AND zona_id=".$zonasU;
		// }
		
		$sql ="Select * from empresa where status =1 ".$zonas." order by nombre asc";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
		while( $obj = sqlsrv_fetch_object($stmt)) {
			$datos[] = $obj;		     
		}
		return $datos;
	}



     //zonas para root en empresa
public static function obtenerFiltroSQLZonasAdmin(){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select * from zona";
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

    //obtener el tipo de usuario
	public static function obtenertipoUSer($idPer){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select * from usuario where persona_id=".$idPer;
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

	     //zonas para root en empresa
	public static function obtenerFiltroSQLPersonaUsuario($filtro){
	$conec = new Conexion;
	$conexion = $conec->abreConexion();
	$sql = "select u.*, CONCAT(p.nombre, '', p.apellido_pat, '', apellido_mat) AS nombreP, p.correo as correoP 
	from usuario u INNER JOIN persona p ON u.persona_id = p.id ".$filtro;
	$stmt = sqlsrv_query( $conexion, $sql);
	$datos = array();
	while( $obj = sqlsrv_fetch_object($stmt)) {
		$datos[] = $obj;		     
	}
	return $datos;
	}



//obtener zona para cualquier usuario
	public static function obtenerFiltroSQLZonas($idZona){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select * from zona where id=".$idZona;
		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}





//obtener el id de la zona a la que pertenece el usuario
    public static function obtenerZonas($id){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select z.id as id
				from usuario u
				join persona p
				on u.persona_id = p.id
				join empresa e
				on p.empresa_id = e.id
				join zona z
				on e.zona_id = z.id 
				where u.persona_id = ".$id;


		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

	public static function obtenerZonasNew($id){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		$sql = "
				select p.zona_id as id
				from persona_zona p
				where p.persona_id = ".$id;


		$stmt = sqlsrv_query( $conexion, $sql);
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;		     
        }
		return $datos;
	}

    function aleatorio($maximo){
		
		$permitidos = "1234567890abcdefghijklmnopqrstuvwxyz";
		$i = 1;
		$_aleatorio = "";

		while($i <= $maximo){

			$_aleatorio .= $permitidos{mt_rand(0, strlen($permitidos))};
			$i++;
		}

		return $_aleatorio;        
	}

	public static function tienePermiso($permiso){   
		
		$permisos = explode("|", Zend_Auth::getInstance()->getIdentity()->permisos);                
		//print_r($permiso);
		if(in_array($permiso, $permisos))
			return true;
		else
			return false;
	}

	public static function crearQuery($modelo, $query = null){

		$con = Doctrine_Manager::getInstance()->connection();

		if(is_null($query)){
			$q = Doctrine_Query::create()->from($modelo);
			return $q;
		} 
		else{

			$q = $con->execute($query)->fetchAll();
			return $q;
		}
	}

	public static function crearQueryServer($modelo, $query = null){

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		if(is_null($query)){
			$sql = "SELECT * FROM dbo.".$modelo;
			return $q;
		} 
		else{

			$q = $con->execute($query)->fetchAll();
			return $q;
		}
	}
	public static function crearQuerySQL($modelo, $query = null,$pagina,$tampagina){

//		$con = Doctrine_Manager::getInstance()->connection();
		$conec = new Conexion;
        $conexion = $conec->abreConexion();


		if(is_null($query)){
//			$q = Doctrine_Query::create()->from($modelo);
			$sql = "SELECT * FROM dbo.".$modelo;

//			return $q;
		} 
		else{
//			$q = $con->execute($query)->fetchAll();
//			return $q;

			$sql = "DECLARE 
					  @pagenum  AS INT = ".$pagina.",
					  @pagesize AS INT = ".$tampagina.";

					SELECT * FROM dbo.".$modelo." ".$query;
			
		}
//print_r($sql);
//exit;
		$stmt = sqlsrv_query( $conexion, $sql);

//print_r($stmt);
//exit;
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	
        	$datos[] = $obj;
			      
        }
        return $datos; 
	}

	public static function crearQuerySQLTotal($modelo, $query = null,$pagina,$tampagina,$modelos){

//		$con = Doctrine_Manager::getInstance()->connection();
		$conec = new Conexion;
        $conexion = $conec->abreConexion();

        $generando_consula = "SELECT ";

        $joins = " FROM ";

        foreach ($modelos as $modelo) {
            $generando_consula .= $modelo.".id as ID".$modelo.", ".$modelo.".status as Status".$modelo.", ".$modelo.".*,";
            $joins .= $modelo." ".$modelo.",";
        }


        $generando_consula = trim($generando_consula,",");
        $joins = trim($joins,",");
//        print_r($generando_consula);
//        print_r($joins);

        $generando_consula .= $joins;

			$sql = "DECLARE 
					  @pagenum  AS INT = ".$pagina.",
					  @pagesize AS INT = ".$tampagina.";

					".$generando_consula." ".$query;
			
//print_r($sql);
//exit;
		$stmt = sqlsrv_query( $conexion, $sql);

//print_r($stmt);
//exit;
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	
        	$datos[] = $obj;
			      
        }
        return $datos; 
	}

	public static function obtener($modelo, $campo, $valor, $final = "", $orden = ""){

		$filtro = $campo." = '".$valor."' ";

		if($final != "")
			$filtro .= $final;

		$q = Doctrine_Query::create()->from($modelo)->where($filtro);

		if($orden != "")
			$q->orderBy($orden);
		
		//echo $q->getSqlQuery(); exit;
		return $q->execute()->getFirst();
	}

	public static function obtenerSQL($modelo, $campo, $valor, $final = "", $orden = ""){

		$filtro = $campo." = '".$valor."' ";

		if($final != "")
			$filtro .= $final;

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		if(is_null($filtro)){
//			$q = Doctrine_Query::create()->from($modelo);
			$sql = "SELECT TOP 1 * FROM dbo.".$modelo;

//			return $q;
		} 
		else{
//			$q = $con->execute($query)->fetchAll();
//			return $q;
			$sql = "SELECT TOP 1 * FROM dbo.".$modelo."  WHERE ".$filtro;
			
		}

		if($orden != "")
			$sql.=" ORDER BY ".$orden;

		$stmt = sqlsrv_query( $conexion, $sql);
		
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	return $obj;

        }

	}

	public static function obtenerSQLTexto($modelo, $campo, $valor, $orden = ""){

		$filtro = $campo." LIKE '%".$valor."%' ";

		if ($valor == '') {
			return '-1';
		}

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		if(is_null($filtro)){
//			$q = Doctrine_Query::create()->from($modelo);
			$sql = "SELECT TOP 1 * FROM dbo.".$modelo;

//			return $q;
		} 
		else{
//			$q = $con->execute($query)->fetchAll();
//			return $q;
			$sql = "SELECT TOP 1 * FROM dbo.".$modelo."  WHERE ".$filtro;
			
		}

		if($orden != "")
			$sql.=" ORDER BY ".$orden;

		$stmt = sqlsrv_query( $conexion, $sql);
		
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	return $obj;

        }

	}

	public static function obtenerFiltro($modelo, $filtro, $orden = ""){

		$q=Doctrine_Query::create()->from($modelo)->where($filtro);

		if($orden != "")
			$q->orderBy($orden);

		//echo $q->getSqlQuery(); //exit;

		return $q->execute();
	}

	public static function obtenerFiltroSQL($modelo, $filtro, $orden = ""){

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		if(is_null($filtro)){
//			$q = Doctrine_Query::create()->from($modelo);
			$sql = "SELECT * FROM dbo.".$modelo;

//			return $q;
		} 
		else{
//			$q = $con->execute($query)->fetchAll();
//			return $q;
			$sql = "SELECT * FROM dbo.".$modelo." ".$filtro;
			
		}

		if($orden != "")
			$sql.=" ORDER BY ".$orden;

		$stmt = sqlsrv_query( $conexion, $sql);

//print_r($stmt);
//exit;
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {

        	$datos[] = $obj;
			      
        }

		return $datos;
	}

	public static function guardar($modelo, $datos = array(), $id = 0, $bitacora = array())
	{
		$tabla = Doctrine_Core::getTable($modelo);

		if(!is_numeric($id) || $id==0 || $id=="0"){
			//print_r($datos);exit;
			$Modelo = new $modelo(); 
			unset($datos['id']);
		}else{
			$Modelo = $tabla->findOneById((int)$id);
		}

		foreach($datos as $campo){

			if(!is_null($campo)){

				$campo = str_replace(array("'", '"'), array("´", "´"), $campo);
			}
		}
		//print_r($datos);exit;
		$Modelo->fromArray($datos);
		//print_r($Modelo);exit;
		

		try{

			$Modelo->save();
			
			foreach($bitacora as $bitacora_){
				
				if($bitacora_["id"] == "")
					Bitacora::guardar($Modelo->id, $modelo, $bitacora_["agregar"], $Modelo[$bitacora_["campo"]]);
				else{

					$registro = My_Comun::obtener($bitacora_["modelo"], "id", $bitacora_["id"]);

					Bitacora::guardar($Modelo->id, $modelo, $bitacora_["editar"], $registro[$bitacora_["campo"]]);
				}
			}

			return $Modelo->id;
		}catch(Doctrine_Connection_Exception $e)
		{
			if($e->getPortableCode()==-5)
			{
				$m=$e->getMessage();

				preg_match_all('/".*?"/', str_replace("'", "\"", $m), $matches);


				$campo=explode("_",$matches[0][1]);

				return "El registro no pudo ser insertado porque el valor <b>".$matches[0][0]."</b> está repetido.";
			}
			else
			{
				try
				{
					return My_Comun::mensaje($e->getPortableCode()); 
				}
				catch(Exception $e1)
				{
					return My_Comun::mensaje(-100); 
				}
			}
		}
	}

	public static function guardarSQL($modelo, $datos = array(), $id = 0, $bitacora = array())
	{

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		$iddevuelto = 0;
		//Guardando nuevo
		if(!is_numeric($id) || $id==0 || $id=="0"){

			$consulta ="INSERT INTO dbo.".$modelo;
			$columnas = " ([";
			$valores = "(";

			foreach ($datos as $key => $value) {
				if ($key=='id' || $key=='id_estado') {
					continue;
				}
				$columnas .= $key."],[";
				$valores .= ((!is_numeric($value))?"'".$value."'":$value).",";
				
			}
 
				$columnas = substr($columnas,0,-2); 
				$valores = trim($valores,","); 
				$columnas .= ",[created_at],[updated_at])";
				$valores .= ",GETDATE(),GETDATE())";


				$consulta .= $columnas." values ".$valores;

				// print_r($consulta." //*");
//				exit;
				$consulta .= "; SELECT Scope_Identity() as id;";

					
					$s = sqlsrv_prepare($conexion, $consulta);

					//ejecutamos la consulta
					try {
						sqlsrv_execute($s);
						if( ($errors = sqlsrv_errors() ) != null) {
					        foreach( $errors as $error ) {
					        	if ($error[ 'code']==2601) {
						            return "¡ATENCIÓN! Ya existe un registro con la misma información.";
					        	}
					        	if ($error[ 'code']==2627) {
						            return "¡ATENCIÓN! Ya existe un registro con la misma información.";
					        	}
				//descomentar de ser necesario para saber que errores hay
					            // echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
					            // echo "code: ".$error[ 'code']."<br />";
					            // echo "message: ".$error[ 'message']."<br />";
					        }
					    }
						sqlsrv_next_result($s);

					$r = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC);
					} catch (Exception $e) {
				//descomentar de ser necesario para saber que errores hay
						print_r($e);
						exit;
					}
//				print_r($consulta);
//				exit;
				
				//$stmt = sqlsrv_query( $s, $consulta);
//				print_r($r['id']);
				
				
			if( $r === false ) {
				//descomentar de ser necesario para saber que errores hay
			    // die( print_r( sqlsrv_errors(), true));
			    return "No se pudo realizar el registro";
			}else{
				//$stmt = sqlsrv_query( $conexion, $consulta);
				$iddevuelto = $r['id'];
			}

		}else{//Actualizando
			$consulta = "UPDATE dbo.".$modelo;
			$a_actualizar = " SET ";
			$bandera = false;

			foreach ($datos as $key => $value) {
				if ($key=='id' ) {
					continue;
				}
				if ($key=='id_estado') {
					$bandera = true;
					continue;
				}
				$a_actualizar .= $key ."=".((!is_numeric($value))?"'".$value."'":$value).",";
			}
			$a_actualizar = trim($a_actualizar,",");
			
			if ($bandera){
				$consulta .= $a_actualizar." WHERE id_estado = ".$id;
			}else{
				$consulta .= $a_actualizar." WHERE id = ".$id;
			}
			
			
//			$consulta .= "; SELECT Scope_Identity() as id;";
//			print_r($consulta);
//			exit;

			$s = sqlsrv_prepare($conexion, $consulta);
//			print_r($s);
//			exit;
			try {
				sqlsrv_execute($s);
			} catch (Exception $e) {
				print_r($e);
				exit;
			}
			//sqlsrv_next_result($s);

			//$r = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC);

			
				
			if( $s === false ) {
			    die( print_r( sqlsrv_errors(), true));
			}else{
				//$stmt = sqlsrv_query( $conexion, $consulta);
				$iddevuelto = $id;
			}
		}

		

		try{

			
			foreach($bitacora as $bitacora_){
				
				if($bitacora_["id"] == "")
					Bitacora::guardar( $bitacora_["modelo"], $bitacora_["agregar"], $bitacora_["campo"]);
				else{

					$registro = My_Comun::obtenerSQL($bitacora_["modelo"], "id", $bitacora_["id"]);
					
					Bitacora::guardar( $bitacora_["modelo"], $bitacora_["editar"], $registro->$bitacora_["campo"]);
				}
			}
		return $iddevuelto;
			
		}catch(Exception $e)
		{
			if($e->getPortableCode()==-5)
			{
				$m=$e->getMessage();

				preg_match_all('/".*?"/', str_replace("'", "\"", $m), $matches);


				$campo=explode("_",$matches[0][1]);

				return "El registro no pudo ser insertado porque el valor <b>".$matches[0][0]."</b> está repetido.";
			}
			else
			{
				try
				{
					return My_Comun::mensaje($e->getPortableCode()); 
				}
				catch(Exception $e1)
				{
					return My_Comun::mensaje(-100); 
				}
			}
		}
	}


	public static function guardarSQLpersonaZona($modelo, $datos = array(), $id = 0, $bitacora = array())
	{

		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		$iddevuelto = 0;
		//Guardando nuevo
		if(!is_numeric($id) || $id==0 || $id=="0"){

			$consulta ="INSERT INTO dbo.".$modelo;
			$columnas = " ([";
			$valores = "(";

			foreach ($datos as $key => $value) {
				if ($key=='id') {
					continue;
				}
				$columnas .= $key."],[";
				$valores .= ((!is_numeric($value))?"'".$value."'":$value).",";
				
			}
 
				$columnas = substr($columnas,0,-2); 
				$valores = trim($valores,","); 
				//borrados para persona zona
				// $columnas .= ",[created_at],[updated_at])";
				// $valores .= ",GETDATE(),GETDATE())";
				$columnas .= ")";
				$valores .= ")";

				$consulta .= $columnas." values ".$valores;

				print_r($consulta." //*");
//				exit;
				//borrados para persona zona
				// $consulta .= "; SELECT Scope_Identity() as id;";
					
					$s = sqlsrv_prepare($conexion, $consulta);

					//ejecutamos la consulta
					try {
						sqlsrv_execute($s);
						if( ($errors = sqlsrv_errors() ) != null) {
					        foreach( $errors as $error ) {
					        	if ($error[ 'code']==2601) {
						            return "¡ATENCIÓN! Ya existe un registro con la misma información.";
					        	}
					        	if ($error[ 'code']==2627) {
						            return "¡ATENCIÓN! Ya existe un registro con la misma información.";
					        	}
				//descomentar de ser necesario para saber que errores hay
//					            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
//					            echo "code: ".$error[ 'code']."<br />";
//					            echo "message: ".$error[ 'message']."<br />";
					        }
					    }
						sqlsrv_next_result($s);

					$r = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC);
					} catch (Exception $e) {
				//descomentar de ser necesario para saber que errores hay
						// print_r($e);
						exit;
					}
//				print_r($consulta);
//				exit;
				
				//$stmt = sqlsrv_query( $s, $consulta);
//				print_r($r['id']);
				
				
			if( $r === false ) {
				//descomentar de ser necesario para saber que errores hay
			    die( print_r( sqlsrv_errors(), true));
			    return "¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.";
			}else{
				//$stmt = sqlsrv_query( $conexion, $consulta);
				$iddevuelto = $r['id'];
			}

		}else{//Actualizando
			$consulta = "UPDATE dbo.".$modelo;
			$a_actualizar = " SET ";

			foreach ($datos as $key => $value) {
				if ($key=='id') {
					continue;
				}
				$a_actualizar .= $key ."=".((!is_numeric($value))?"'".$value."'":$value).",";
			}
			$a_actualizar = trim($a_actualizar,",");
			$consulta .= $a_actualizar." WHERE id = ".$id;
			
//			$consulta .= "; SELECT Scope_Identity() as id;";
//			print_r($consulta);
//			exit;

			$s = sqlsrv_prepare($conexion, $consulta);
//			print_r($s);
//			exit;
			try {
				sqlsrv_execute($s);
			} catch (Exception $e) {
				print_r($e);
				exit;
			}
			//sqlsrv_next_result($s);

			//$r = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC);

			
				
			if( $s === false ) {
			    die( print_r( sqlsrv_errors(), true));
			}else{
				//$stmt = sqlsrv_query( $conexion, $consulta);
				$iddevuelto = $id;
			}
		}

		

		try{

			
			foreach($bitacora as $bitacora_){
				
				if($bitacora_["id"] == "")
					Bitacora::guardar( $bitacora_["modelo"], $bitacora_["agregar"], $bitacora_["campo"]);
				else{

					$registro = My_Comun::obtenerSQL($bitacora_["modelo"], "id", $bitacora_["id"]);
					
					Bitacora::guardar( $bitacora_["modelo"], $bitacora_["editar"], $registro->$bitacora_["campo"]);
				}
			}
		return $iddevuelto;
			
		}catch(Exception $e)
		{
			if($e->getPortableCode()==-5)
			{
				$m=$e->getMessage();

				preg_match_all('/".*?"/', str_replace("'", "\"", $m), $matches);


				$campo=explode("_",$matches[0][1]);

				return "El registro no pudo ser insertado porque el valor <b>".$matches[0][0]."</b> está repetido.";
			}
			else
			{
				try
				{
					return My_Comun::mensaje($e->getPortableCode()); 
				}
				catch(Exception $e1)
				{
					return My_Comun::mensaje(-100); 
				}
			}
		}
	}


	public static function registrosGrid($modelo, $filtro,$aliasrecibido,$modelos)
	{
		### Incializamos el arreglo de registros
		$registros=array();
		
		### Recibimos los parámetros de paginación y ordenamiento.
		if (isset($_POST['page']) != ""){$page = $_POST['page'];}
		if (isset($_POST['sortname']) != ""){$sortname =$_POST['sortname'];}
		if (isset($_POST['sortorder']) != ""){$sortorder = $_POST['sortorder'];}
		if (isset($_POST['qtype']) != ""){$qtype = $_POST['qtype'];}
		if (isset($_POST['query']) != ""){$query = $_POST['query'];}
		if (isset($_POST['rp']) != ""){$rp = $_POST['rp'];}
		
//print_r($_POST);
//exit;		
		$alias = $aliasrecibido;

		### Codificamos el filtro para evitar problemas con IE      
		$filtro= (My_Utf8::is_utf8($filtro))?$filtro:utf8_encode($filtro);
		
		//ejecutamos la consulta primero para saber cuantos se van a  mostrar en el paginador		
		$q=My_Comun::crearQuerySQL($modelo,$filtro,$page,$rp);

//		print_r($filtro); exit;
		
//		$registros['total']=$q->count();
		$registros['total']=count($q);
		$paginas=ceil($registros['total']/$rp);
		if($page>$paginas)
			$page=1;

		//Si hay mas de un modelo relacionado aqui cremos las uniones y las agregamos al filtro
		if (count($modelos) > 1) {
	        $union = " AND ".$modelos[0].".".$modelos[1]."_id = ";

	        for ($i=1; $i < count($modelos) ; $i++) { 

	            $union .= "".$modelos[$i].".id AND";

	        }

	        $union = substr($union,0,-3);

	        $filtro.=$union;
		}
			
		### Completamos la consulta con los datos de paginación y ordenamiento
		$offset = ($page-1)*$rp;
		$filtro .= ($qtype != '' && $query != '') ? " AND $alias.{$qtype} = '$query' " : '';
		$order = " $alias.{$sortname} $sortorder ";
//print_r($offset);
//print_r($filtro.=" ORDER BY ".$order." LIMIT ".$rp);
//print_r($order);
//print_r($rp);
//exit;
		//ordenamos y a su vez creamos la estructura del paginador anexandolo al filtro
		$filtro.=" ORDER BY ".$order." OFFSET (@pagenum - 1) * @pagesize ROWS 
				  FETCH NEXT @pagesize ROWS ONLY;";
//		print_r($filtro);
//		exit;

		//mandamos a ejecutar la consulta y traer los resultados
		$q=My_Comun::crearQuerySQLTotal($modelo,$filtro,$page,$rp,$modelos);

//		print_r($q);
//		exit;

		$registros['registros']=$q;  
		$registros['pagina']=$page;  
		
		return $registros;
	}

	public static function registrosGridQuery($consulta){
		$registros = array();
		
		// Parámetros de paginación y ordenamiento que vienen del POST del JS donde se carga el DataGrid.
		if(isset($_POST['page']) != ""){$page = $_POST['page'];}
		if(isset($_POST['sortname']) != ""){$sortname = $_POST['sortname'];}
		if(isset($_POST['sortorder']) != ""){$sortorder = $_POST['sortorder'];}
		if(isset($_POST['qtype']) != ""){$qtype = $_POST['qtype'];}
		if(isset($_POST['query']) != ""){$query = $_POST['query'];}
		if(isset($_POST['rp']) != ""){$rp = $_POST['rp'];}

		//$consulta = (My_Utf8::is_utf8($consulta)) ? $consulta : utf8_encode($consulta);
		
		// Ejecutar la consulta sin parámetros de paginación ni ordenamiento; para obtener el total de registros.
		$q = My_Comun::crearQuery(null, $consulta);



		$registros['total'] = count($q);

		$paginas = ceil($registros['total'] / $rp);
		if($page > $paginas)
			$page = 1;
			
		// Completar la consulta con los parámetros de paginación y ordenamiento
		$consulta .= " order by ".$sortname." ".$sortorder;
		$offset = ($page - 1) * $rp;        
		$consulta .= " limit ".$offset.", ".$rp;

		//echo $consulta;

		$registros['registros'] = My_Comun::crearQuery(null, $consulta);
		$registros['pagina'] = $page;  
		
		return $registros;
	}


	public static function registrosGridQuerySQL($consulta){
		$conec = new Conexion;
        $conexion = $conec->abreConexion();

		$registros = array();
		
		// Parámetros de paginación y ordenamiento que vienen del POST del JS donde se carga el DataGrid.
		if(isset($_POST['page']) != ""){$page = $_POST['page'];}
		if(isset($_POST['sortname']) != ""){$sortname = $_POST['sortname'];}
		if(isset($_POST['sortorder']) != ""){$sortorder = $_POST['sortorder'];}
		if(isset($_POST['qtype']) != ""){$qtype = $_POST['qtype'];}
		if(isset($_POST['query']) != ""){$query = $_POST['query'];}
		if(isset($_POST['rp']) != ""){$rp = $_POST['rp'];}

		//$consulta = (My_Utf8::is_utf8($consulta)) ? $consulta : utf8_encode($consulta);
		
		// Ejecutar la consulta sin parámetros de paginación ni ordenamiento; para obtener el total de registros.
		//$q = My_Comun::crearQuery(null, $consulta);
		$stmt = sqlsrv_query( $conexion, $consulta);

//print_r($consulta);
//exit;
		$datos = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	$datos[] = $obj;
        }

		$registros['total'] = count($datos);

		$paginas = ceil($registros['total'] / $rp);
		if($page > $paginas)
			$page = 1;
			
		// Completar la consulta con los parámetros de paginación y ordenamiento
		//$consulta .= " order by ".$sortname." ".$sortorder;
//		$offset = ($page - 1) * $rp;        
//		$consulta .= " limit ".$offset.", ".$rp;
		$consulta .= ($qtype != '' && $query != '') ? " AND $alias{$qtype} = '$query' " : '';
		$order = " $alias{$sortname} $sortorder ";
		//ordenamos y a su vez creamos la estructura del paginador anexandolo al filtro
		$limite.=" ORDER BY ".$order." OFFSET (@pagenum - 1) * @pagesize ROWS 
				  FETCH NEXT @pagesize ROWS ONLY;";
		$sql = "DECLARE 
					  @pagenum  AS INT = ".$page.",
					  @pagesize AS INT = ".$rp.";

					".$consulta." ".$limite;
		//echo $consulta;

		$stmt = sqlsrv_query( $conexion, $sql);
//print_r($sql);
//exit;
		$datos2 = array();
        while( $obj = sqlsrv_fetch_object($stmt)) {
        	
        	$dato2[] = $obj;
			      
        }
//print_r($sql);
//exit;
		$registros['registros'] = $dato2;
//		$registros['registros'] = My_Comun::crearQuery(null, $consulta);
		$registros['pagina'] = $page;  
		
		return $registros;
	}


	public static function armarGrid($registros, $grid){

		if(count($grid)>0){

			$columnas=array_keys($grid[0]);
		}

		$xml='<rows><page>'.$registros['pagina'].'</page><total>'.$registros['total'].'</total>';
		
		foreach($grid as $row){

			if(array_key_exists("id", $row))
				$xml .= '<row id="'.$row['id'].'">';
			else
				$xml .= '<row id="0">';

			foreach($columnas as $k=>$v)
			{
				if($v!='id')
				{
					$xml.='<cell><![CDATA['.$row[$v].']]></cell>';
				}
			}
			$xml .= '</row>';
		}
		echo $xml.="</rows>";        
	}

	public static function eliminar($modelo, $id, $bitacora = array())
	{   
		//Verificamos si el registro ya está deshabilitado para entonces habilitarlo
		$registro=My_Comun::obtener($modelo, "id", $id);
		if($registro->status==0)
		{
			$q = Doctrine_Query::create()->update($modelo)->set("status", "1")->where("id = ".$id);
			$q->execute();
			foreach($bitacora as $bitacora_)
			{
				Bitacora::guardar($bitacora_["id"], $bitacora_["modelo"], $bitacora_["habilitar"], $registro[$bitacora_["campo"]]);
			}
			return My_Comun::mensaje(4);
		}
		else
		{
			try
			{

				foreach($bitacora as $bitacora_){

					$registro = My_Comun::obtener($bitacora_["modelo"], "id", $bitacora_["id"]);
				}

				$q = Doctrine_Query::create()->delete($modelo)->where("id = ".$id);
				$q->execute();

				foreach($bitacora as $bitacora_){
					
					Bitacora::guardar($bitacora_["id"], $bitacora_["modelo"], $bitacora_["eliminar"], $registro[$bitacora_["campo"]]);
				}
				
				return My_Comun::mensaje(1);
			}
			catch (Exception $e)
			{

				//echo $e->getMessage();
				
				if($e->getPortableCode()=="-3")
				{ // Error de integridad referencial
					try
					{
						
						$q = Doctrine_Query::create()->update($modelo)->set("status", "0")->where("id = ".$id);
						$q->execute();
						
						foreach($bitacora as $bitacora_)
						{
					
							Bitacora::guardar($bitacora_["id"], $bitacora_["modelo"], $bitacora_["deshabilitar"], $registro[$bitacora_["campo"]]);
						}
						
						return My_Comun::mensaje(7);
					}
					catch (Exception $e1)
					{
						//echo $e1->getMessage();
						return My_Comun::mensaje(3);  
					}
					
				}
				else
				{
					return My_Comun::mensaje($e->getPortableCode()); 
				}
			}
		}
	}

	public static function eliminarSQL($modelo, $id, $bitacora = array())
	{   
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		//Verificamos si el registro ya está deshabilitado para entonces habilitarlo
		$registro=My_Comun::obtenerSQL($modelo, "id", $id);

		// echo '<script>console.log("'.$registro->id.'");</script>';

		if($registro->status==0)
		{
			$consulta = "UPDATE ".$modelo." SET status = 1 WHERE id = ".$id;
			$s = sqlsrv_prepare($conexion, $consulta);
//			print_r($s);
//			exit;
			try {
				sqlsrv_execute($s);
			} catch (Exception $e) {
				print_r($e);
				exit;
			}
			
			foreach($bitacora as $bitacora_)
			{
				Bitacora::guardar($bitacora_["modelo"], $bitacora_["habilitar"], $registro->$bitacora_["campo"]);
			}
			return My_Comun::mensaje(4);
		}
		else
		{
			try
			{

				foreach($bitacora as $bitacora_){

					$registro = My_Comun::obtenerSQL($bitacora_["modelo"], "id", $bitacora_["id"]);
				}

				$consulta = "DELETE ".$modelo." WHERE id = '".$id."'";
				$s = sqlsrv_prepare($conexion, $consulta);
	//			print_r($s);
	//			exit;
				
					$stmt = sqlsrv_execute($s);
					if( !$stmt ) {
				    	//die( print_r( sqlsrv_errors(), true));
				    	try
						{
							$consulta = "UPDATE ".$modelo." SET status = 0 WHERE id = ".$id;
							$s = sqlsrv_prepare($conexion, $consulta);
				//			print_r($s);
				//			exit;
							try {
								sqlsrv_execute($s);
							} catch (Exception $e) {
								print_r($e);
								exit;
							}
							
							foreach($bitacora as $bitacora_)
							{
						
								Bitacora::guardar($bitacora_["modelo"], $bitacora_["deshabilitar"], $registro->$bitacora_["campo"]);
							}
							
							return My_Comun::mensaje(7);
						}
						catch (Exception $e1)
						{
							//echo $e1->getMessage();
							return My_Comun::mensaje(3);  
						}
						
					}else{
						foreach($bitacora as $bitacora_){
						
							Bitacora::guardar($bitacora_["modelo"], $bitacora_["eliminar"], $registro->$bitacora_["campo"]);
						}
						return My_Comun::mensaje(1);
					}
				

			}
			catch (Exception $e)
			{

				//echo $e->getMessage();
				
				if($e->getPortableCode()=="-3")
				{ // Error de integridad referencial
					try
					{
						$consulta = "UPDATE ".$modelo." SET status = 0 WHERE id = ".$id;
						$s = sqlsrv_prepare($conexion, $consulta);
			//			print_r($s);
			//			exit;
						try {
							sqlsrv_execute($s);
						} catch (Exception $e) {
							print_r($e);
							exit;
						}
						
						foreach($bitacora as $bitacora_)
						{
					
							Bitacora::guardar($bitacora_["modelo"], $bitacora_["deshabilitar"], $registro->$bitacora_["campo"]);
						}
						
						return My_Comun::mensaje(7);
					}
					catch (Exception $e1)
					{
						//echo $e1->getMessage();
						return My_Comun::mensaje(3);  
					}
					
				}
				else
				{
					return My_Comun::mensaje($e->getPortableCode()); 
				}
			}
		}
	}

	public static function eliminarSQLPersonaZona($modelo, $id, $bitacora = array())
	{   
		$conec = new Conexion;
        $conexion = $conec->abreConexion();
		//Verificamos si el registro ya está deshabilitado para entonces habilitarlo
		$registro=My_Comun::obtenerSQL($modelo, "id", $id);
			try
			{
				// foreach($bitacora as $bitacora_){
				// 	$registro = My_Comun::obtenerSQL($bitacora_["modelo"], "id", $bitacora_["id"]);
				// }

				$consulta = "DELETE ".$modelo." WHERE id = '".$id."'";
				$s = sqlsrv_prepare($conexion, $consulta);
	//			print_r($s);
	//			exit;
				
					$stmt = sqlsrv_execute($s);
						// foreach($bitacora as $bitacora_){
						
						// 	Bitacora::guardar($bitacora_["modelo"], $bitacora_["eliminar"], $registro->$bitacora_["campo"]);
						// }
				return My_Comun::mensaje(1);

			}
			catch (Exception $e)
			{

				//echo $e->getMessage();
				
				if($e->getPortableCode()=="-3")
				{ // Error de integridad referencial
				
					return My_Comun::mensaje($e->getPortableCode()); 
				}
			}
			return My_Comun::mensaje(1);
	}


	public static function deshabilitar($modelo, $id, $bitacora = array()){

		try{
			
			$q = Doctrine_Query::create()->update($modelo)->set("status", "0")->where("id = ".$id);
			$q->execute();  
			
			foreach($bitacora as $bitacora_){

				$registro = My_Comun::obtener($bitacora_["modelo"], "id", $bitacora_["id"]);
				
				Bitacora::guardar($bitacora_["id"], $bitacora_["modelo"], $bitacora_["deshabilitar"], $registro[$bitacora_["campo"]]);
			}
			
			return My_Comun::mensaje(2);
		}catch (Exception $e){

			//echo $e1->getMessage();
		
			return My_Comun::mensaje(3); 
		}   
	}
	
	public static function habilitar($modelo, $id, $bitacora = array(),$extra=""){

		try{
			
			$q = Doctrine_Query::create()->update($modelo)->set("status", "1")->where("id = ".$id);
			$q->execute();  
			
			foreach($bitacora as $bitacora_){

				$registro = My_Comun::obtener($bitacora_["modelo"], "id", $bitacora_["id"]);
				
				Bitacora::guardar($bitacora_["id"], $bitacora_["modelo"], $bitacora_["habilitar"], $registro[$bitacora_["campo"]]);
			}
			
			return My_Comun::mensaje(4);
		}catch (Exception $e){
			//echo $e1->getMessage();
		
			return My_Comun::mensaje(5); 
		}   
	}   



	public static function FileSizeConvert($bytes){

		$bytes = floatval($bytes);

		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

		foreach($arBytes as $arItem){

			if($bytes >= $arItem["VALUE"]){

				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}

		return $result;
	}

	public static function correoElectronico($titulo, $cuerpo, $de, $de_nombre, $para, $para_nombre, $copia = "", $adjunto = ""){

	$config = array("auth" => "login", "username" => "ressudi@utj.edu.mx", "password" => "adminca02", "port" => 587);
	$transport = new Zend_Mail_Transport_Smtp("mail.utj.com.mx", $config);
      
  	$cuerpo_ = "
                    <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                    <html xmlns=\"http://www.w3.org/1999/xhtml\">
                    <head><title>Sinergia</title></head>
                    <body>
                        
                        <table style=\"width: 100%; font-family: Tahoma, Arial; font-size: 12px; border: none 0; border-spacing: 0; border-collapse: collapse; padding: 0;\" border=\"0\">
                            <tr>
                                <td style=\"height:3px; background-color: #ed3023;\"></td>
                            </tr>
                            
                            <tr>
                            	<td align=\"left\" style=\"padding: 10px;\">
								<img src=\"http://ca02.utj.edu.mx/public/img/logo_ca.png\" alt=\"Sinergia\" title=\"Sinergia\"/></td>
                            </tr>
                            
                            <tr>
                                <td style=\"height:3px; background-color: #0079c2;\"></td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"middle\" style=\"padding:10px; color:#14374A; font-size:18px;\">
                                    ".$titulo."
				</td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"top\" style=\"padding:20px; color:#444444; font-size:12px;\">
                                    ".$cuerpo."
				</td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"top\" style=\"color:#444444; font-size:10px;\">
                                    Este correo electrónico ha sido enviado de la página de Sinergia.
                                </td>
                            </tr>
                            <tr><td style=\"height:3px; background-color: #14374A;\"></td></tr>
			</table>
                    </body>
		</html>";
              
		
		$mail = new Zend_Mail();
		$mail->setBodyHtml(utf8_decode($cuerpo_));
		$mail->setFrom($de,  utf8_decode($de_nombre));
		$mail->addTo($para, utf8_decode($para_nombre));	
		$mail->setSubject(utf8_decode($titulo));

	try{
		$mail->send();
//		$mail->send($transport);
	}catch(Exception $e){
		echo("$e error");
	}//try
		return 1;
	}

	public static function envioCorreo($titulo, $cuerpo, $de, $de_nombre, $para, $para_nombre, $copia = "", $adjunto = ""){


	// $config = array("auth" => "login", "username" => "ressudi@utj.edu.mx", "password" => "adminca02",  "port" => 465);
	// $transport = new Zend_Mail_Transport_Smtp("mail.utj.com.mx", $config);
	
	$config = array('ssl' => 'ssl', "auth" => "login", "username" => "ressudi.utj@gmail.com", "password" => "ressudiutj2018",  "port" => 465);
	$transport = new Zend_Mail_Transport_Smtp("smtp.gmail.com", $config);
    Zend_Mail::setDefaultTransport($transport);
	

  	$cuerpo_ = "
                    <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                    <html xmlns=\"http://www.w3.org/1999/xhtml\">
                    <head><title>Sinergia</title></head>
                    <body>
                        
                        <table style=\"width: 100%; font-family: Tahoma, Arial; font-size: 12px; border: none 0; border-spacing: 0; border-collapse: collapse; padding: 0;\" border=\"0\">
                            <tr>
                                <td style=\"height:3px; background-color: #ed3023;\"></td>
                            </tr>
                            
                            <tr>
                            	<td align=\"left\" style=\"padding: 10px;\">
								<img src=\"http://ca02.utj.edu.mx/public/img/logo_ca2.png\" alt=\"Sinergia\" title=\"Sinergia\"/></td>
                            </tr>
                            
                            <tr>
                                <td style=\"height:3px; background-color: #0079c2;\"></td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"middle\" style=\"padding:10px; color:#14374A; font-size:18px;\">
                                    ".$titulo."
				</td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"top\" style=\"padding:20px; color:#444444; font-size:12px;\">
                                    ".$cuerpo."
				</td>
                            </tr>
                            
                            <tr>
                                <td align=\"left\" valign=\"top\" style=\"color:#444444; font-size:10px;\">
                                    Este correo electrónico ha sido enviado de la página de Sinergia.
                                </td>
                            </tr>
                            <tr><td style=\"height:3px; background-color: #14374A;\"></td></tr>
			</table>
                    </body>
		</html>";
/*
		$mail = new Zend_Mail("UTF-8"); //Usaremos codificación UTF-8 para el mensaje
		$mail->setFrom("ressudi@utj.edu.mx", "RESSUDI") //Cuenta de correo del remitente y nombre del remitente amostrar
		     ->setSubject($titulo) //Asunto
		     ->setBodyHtml($cuerpo_) //Cuerpo del mensaje indicando que será HTML y no texto plano. Usaríamos setBodyText() para texto plano
		     ->addTo($para); //Añadimos un destinatario
//		     ->addCc("copy@calat.com") //Añadimos un destinatario en copia
//		     ->addBcc("blind@calat"); //Añadimos un destinatario en copia oculta	
*/
		 $mail = new Zend_Mail();
		 $mail->setFrom($de,  utf8_decode($de_nombre));
		 $mail->addTo($para, utf8_decode($para_nombre));
		//  $mail->setFrom('ressudi@utj.edu.mx', 'You');
		//  $mail->addTo('alex.ibarra.roque@gmail.com', 'Anybody');	
		//  $mail->setSubject('My first email by Mailjet');
		//  $mail->setBodyText('Hello from Mailjet & Zend Framework !');
		 $mail->setSubject(utf8_decode($titulo));
		$mail->setBodyHtml(utf8_decode($cuerpo_));


		try {
		    $mail->send($transport);
		    return 1;
		} catch (Zend_Exception $e) {
		    return $e->getMessage();
		}
    }


}
?>