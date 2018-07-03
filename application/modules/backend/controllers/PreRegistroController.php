<?php
class Backend_PreRegistroController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/pre-registro.js?'.time());
       
    }//function
 
    public function indexAction(){

        // usuario_id Zend_Auth::getInstance()->getIdentity()->id
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        // $this->view->tipos = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->id);
        // $this->view->empesas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->id);

        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

    	$sess=new Zend_Session_Namespace('permisos');
    	///$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PRE_REGISTRO")!==false;

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";


        //Verificamos el tipo d usurio
        // if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            // $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            // $filtro .= " AND pr.zona_id = ".$zona->id." ";
        // }

        $z = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $k = $z[0]->estadoId;
        
        // foreach ($z as $zoni) {
        //     $k = $zoni->zona_id ;
        // }

        $nombre=$this->_getParam('nombre');
        $materno=$this->_getParam('materno');
        $paterno=$this->_getParam('paterno');
        $status=$this->_getParam('status');
        $zona=$this->_getParam('zona_id');
        $estado=$this->_getParam('estado_id');
        $tipo=$this->_getParam('tipo_id');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='')
        {
            $filtro.=" AND (pr.nombre LIKE '%".$nombre."%') ";
        }
        if($paterno!='')
        {
            $filtro.=" AND (pr.apellido_pat LIKE '%".$paterno."%') ";
        }
        if($materno!='')
        {
            $filtro.=" AND (pr.apellido_mat LIKE '%".$materno."%') ";
        }
        if($zona!='')
        {
            $filtro.=" AND (pr.zona_id = '".$zona."') ";
        }
        if($tipo!='')
        {
            $filtro.=" AND (pr.tipo_id = '".$tipo."') ";
        }
        if($estado!='')
        {
            $filtro.=" AND (pr.estado_id = '".$estado."') ";
        }else{
            $filtro.=" AND (pr.estado_id = '".$k."') ";
        }

        $consulta = "SELECT pr.id, pr.nombre, pr.apellido_pat, pr.apellido_mat, pr.correo, pr.telefono, pr.status, z.nombre as zNombre, es.estado
                      FROM pre_registro pr
                      JOIN tipo_persona tp
                      ON tp.id = pr.tipo_id
                      JOIN estados es
                      ON es.id_estado = pr.estado_id
                      JOIN zona z
                      ON z.id = tp.zona_id
                      WHERE ".$filtro;

        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PRE_REGISTRO");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_PRE_REGISTRO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre.' '.$registros['registros'][$k]->apellido_pat.' '.$registros['registros'][$k]->apellido_mat;
                $grid[$i]['correo'] =$registros['registros'][$k]->correo;
                $grid[$i]['telefono'] =$registros['registros'][$k]->telefono;
                $grid[$i]['estado'] =$registros['registros'][$k]->estado;
                $grid[$i]['status']="En espera";
               
            if($registros['registros'][$k]->status == 0)
            {
                $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                
                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Eliminar"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle text-danger fa-lg "></i>';
            }
            else
            {
                    
                if($editar){
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/pre-registro/agregar\','.$registros['registros'][$k]->id.', \'frmPreRegistro\',\'Ficha de Pre-registro\' );" title="Visualizar"><i class="boton fa fa-eye fa-lg azul"></i></span>';
                }
                else{
                    $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                }

                if($eliminar){
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Deshabilitar / Habilitar"><i class="boton fa fa-times-circle fa-lg azul"></i></i></span>';
                }
                else{
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle fa-lg text-danger"></i>';						
                }
            }
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function

    
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);

//        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1', ' nombre asc');
        //$this->view->empresas = My_Comun::obtenerFiltroSQL('empresa', ' WHERE status = 1', ' nombre asc');

        // $zId = $this->_getParam('zona_id');

        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        // $this->view->zonasUser = Usuario::obtieneZonasXususario($idPer);

        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("pre_registro", "id", $_POST["id"]);
        }

        // if($this->view->tipoUser[0]->tipo_usuario == 3){

        //     $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
        // }else {

            // $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaZonas($this->view->zonasUser);
            $regi = My_Comun::obtenerSQL("pre_registro", "id", $_POST["id"]);
            $tp = My_Comun::obtenerSQL("tipo_persona", "id", $regi->tipo_id);
            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($tp->zona_id);
            
        // }  

    }//function

    public function checaEmailAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE); 

        // echo "cesar";
        
        $personas = My_Comun::obtenerFiltroSQL('persona', ' WHERE correo = \''. $_POST["email"].'\' ' , ' nombre asc');
        // $pre_reg = My_Comun::obtenerFiltroSQL('pre_registro', ' WHERE correo like '. $_POST["email"].' ', ' nombre asc');

        $tamP = count($personas);
        // $tamPr = sizeof($pre_reg);
        $opciones = 0;


        if ($tamP > 0 ){
            $opciones = 0;
        }else{
            $opciones = 1;
        }
        echo $opciones; 
    
        // echo "<script>console.log( 'Debug Objects: " .  $_POST["email"] . "' );</script>";
      }

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Pre-registro";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Modifica pre-registro";
        	$bitacora[0]["editar"] = "Editar pre-registro";

            $pre_registro = My_Comun::obtenerSQL('pre_registro','id',$_POST['id']);            

            $data = array();
            $data['nombre'] = $_POST['nombre'];
            $data['apellido_pat'] = $_POST['apellido_pat'];
            $data['apellido_mat'] = $_POST['apellido_mat'];
            $data['rfc'] = $_POST['rfc'];
            $data['curp'] = $_POST['curp'];
            $data['fecha_nacimiento'] = $_POST['fecha_nacimiento'];
            $data['genero'] = $_POST['genero'];
            $data['correo'] = $_POST['correo'];
            $data['telefono'] = $_POST['telefono'];
            $data['celular'] = $_POST['celular'];
            $data['empresa_id'] = $_POST['empresa_id'];

            $preId = My_Comun::guardarSQL("persona", $data, $data["id"], $bitacora);

            //Se define una cadena de caractares. Te recomiendo que uses esta.
            $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $pass = $this->generaUsuarioContrasena($cadena, 8);
            $armando = trim($_POST['nombre'].$_POST['apellido_pat'].$_POST['apellido_mat']," ");
            $usu = $this->generaUsuarioContrasena($armando,6);
            $data2 = array();
            $data2['persona_id'] = $preId;
            $data2['cambiado'] = 0;
            $data2['usuario'] = $usu;
            $data2['contrasena'] = $pass;
            //Nuevo
            $data2['tipo_usuario'] = 6;
            $data2['permisos'] = '';

            $usuId = My_Comun::guardarSQL("usuario", $data2, $data2["id"], $bitacora);
            
            $pre = My_Comun::eliminarSQL("pre_registro", $_POST["id"], $bitacora);

            $titulo = 'Bienvenido a Ressudi';
            $cuerpo = 'Ha sido aceptado en el sistema de "Ressudi", podrá aceder al sistema con la siguiente información:<br>';
            $cuerpo .= 'Usuario: '.$usu;
            $cuerpo .= '<br>Password: '.$pass;

            $respuesta = My_Comun::envioCorreo($titulo, $cuerpo, 'ressudi.utj@gmail.com', 'Ressudi', $_POST['correo'], $_POST['nombre'].' '.$_POST['apellido_pat'].' '.$_POST['apellido_mat']);
            // print_r($respuesta);
            
            echo($preId);
            // echo("Agregado correctamente!");
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Pre-registro";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar pre-registro";
        $bitacora[0]["deshabilitar"] = "Deshabilitar pre-registro";
        $bitacora[0]["habilitar"] = "Habilitar pre-registro";
			
        echo My_Comun::eliminarSQL("pre_registro", $_POST["id"], $bitacora);
    }//function 

    public function generaUsuarioContrasena($cadena,$longitud)
    {
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena=strlen($cadena);
         
        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
        $longitudPass=$longitud;
         
        //Creamos la contraseña
        for($i=1 ; $i<=$longitudPass ; $i++){
            //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
            $pos=rand(0,$longitudCadena-1);
         
            //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;

    }

    public function onChangeEstadoAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $estado=$this->_getParam('estado');
        $filtro = "WHERE status = 1";
          
        if($estado!='')
        {
            $filtro.=" AND (estado_id = $estado) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        echo json_encode($zonas);
    }

    public function onChangeZonaAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $zona=$this->_getParam('zona');
        $filtro = "WHERE status = 1";
          
        if($zona!='')
        {
            $filtro.=" AND (zona_id = $zona) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $tipo_Pregistro = My_Comun::obtenerFiltroSQL('tipo_persona',$filtro, ' descripcion asc');
        echo json_encode($tipo_Pregistro);
    }


}//class
?>