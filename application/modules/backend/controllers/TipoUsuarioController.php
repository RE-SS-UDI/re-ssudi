<?php
class Backend_TipoUsuarioController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/tipo-usuario.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_TIPO_USUARIO")!==false;

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='')
        {
            $nombre=explode(" ", trim($nombre));
            for($i=0; $i<=$nombre[$i]; $i++)
            {
                $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
        		if($nombre[$i]!="")
                    $filtro.=" AND (tu.descripcion LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if

        if (Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3) {
            $filtro .= " AND (tu.id <> 3) ";
        }

        $consulta = "SELECT tu.*
                      FROM tipo_usuario tu
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_TIPO_USUARIO");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_TIPO_USUARIO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['descripcion'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
               
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/tipo-usuario/agregar\','.$registros['registros'][$k]->id.', \'frmTipoUsuario\',\'Edición de Tipo de Usuario\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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
		
        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("tipo_usuario", "id", $_POST["id"]);
            $this->view->permisos=explode("|",$this->view->registro->permisos);
        }
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Tipo Usuario";
        	$bitacora[0]["campo"] = "descripcion";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega tipo de usuario";
        	$bitacora[0]["editar"] = "Editar tipo de usuario";

            $preId = My_Comun::guardarSQL("tipo_usuario", $_POST, $_POST["id"], $bitacora);

            TipoUsuario::guardarPermisos($_POST['permisos'],$preId);

            $usuarios = My_Comun::obtenerFiltroSQL('usuario', ' WHERE status = 1 and tipo_usuario = '.$preId, 'id asc');

            foreach ($usuarios as $usuario) {
                Usuario::guardarPermisos($_POST['permisos'],$usuario->id);
            }

            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Tipo Usuario";
        $bitacora[0]["campo"] = "descripcion";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar tipo de usuario";
        $bitacora[0]["deshabilitar"] = "Deshabilitar tipo de usuario";
        $bitacora[0]["habilitar"] = "Habilitar tipo de usuario";
			
        echo My_Comun::eliminarSQL("tipo_usuario", $_POST["id"], $bitacora);
    }//function

}//class
?>