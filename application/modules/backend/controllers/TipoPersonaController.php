<?php
class Backend_TipoPersonaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/tipo-persona.js?'.time());
       
    }//function
 
    public function indexAction(){

        // usuario_id Zend_Auth::getInstance()->getIdentity()->id
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

    	$sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_TIPO_PERSONA")!==false;

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

        $z = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $k = $z[0]->zona_id ;
        
        // foreach ($z as $zoni) {
        //     $k = $zoni->zona_id ;
        // }


        $zona=$this->_getParam('zona_id');
        $nombre=$this->_getParam('nombre');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        if($nombre!='')
        {
            $filtro.=" AND (tp.descripcion LIKE '%".$nombre."%')";
        }
        if($zona!='')
        {
            $filtro.=" AND (tp.zona_id = '".$zona."') ";
        }else{
            $filtro.=" AND (tp.zona_id = '".$k."') ";
        }

        $consulta = "SELECT tp.id, tp.descripcion, tp.status, tp.zona_id, z.nombre
                      FROM tipo_persona tp
                      JOIN zona z
                      ON z.id = tp.zona_id
                      WHERE ".$filtro;

        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_TIPO_PERSONA");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_TIPO_PERSONA");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
                $grid[$i]['zona'] =$registros['registros'][$k]->nombre;
               
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/tipo-persona/agregar\','.$registros['registros'][$k]->id.', \'frmPreRegistro\',\'Tipo Registro\' );" title="Visualizar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
            $this->view->registro=My_Comun::obtenerSQL("tipo_persona", "id", $_POST["id"]);
        }

        // if($this->view->tipoUser[0]->tipo_usuario == 3){

        //     $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
        // }else {

            // $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaZonas($this->view->zonasUser);
            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($this->view->registro->zona_id);
            $this->view->zonas2 = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
            
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
        	$bitacora[0]["modelo"] = "Tipo-persona";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Modifica tipo-persona";
        	$bitacora[0]["editar"] = "Editar tipo-persona";

            $preId = My_Comun::guardarSQL("tipo_persona", $_POST, $_POST["id"], $bitacora);

            echo($preId);
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
			
        echo My_Comun::eliminarSQL("tipo_persona", $_POST["id"], $bitacora);
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
    
}//class
?>