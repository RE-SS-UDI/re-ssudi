<?php
class Backend_BitacoraController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/bitacora.js?'.time());
        
    }//function
  
    public function indexAction(){
      
    	$sess=new Zend_Session_Namespace('permisos');
        print_r($sess->permisos);
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_USUARIO")!==false;
        $this->view->usuarios=My_Comun::obtenerFiltroSQL("persona"," where 1=1"," nombre ASC");

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        //Iniciamos filtro
        $filtro=" WHERE 1=1 ";

        $usuario=$this->_getParam('usuario');
        $modelo=$this->_getParam('modelo');
        $accion=$this->_getParam('accion');
        $referencia=$this->_getParam('referencia');
        $desde=$this->_getParam('desde');
        $hasta=$this->_getParam('hasta');
       
/*        if($usuario!='')
        {
            $nombre=explode(" ", trim($usuario));
            for($i=0; $i<=$usuario[$i]; $i++)
            {
                $usuario[$i]=trim(str_replace(array("'","\"",),array("�","�"),$usuario[$i]));
                if($nombre[$i]!="")
                    $filtro.=" AND (bitacora.usuario_id = '".$usuario[$i]."')  ";
            }//for
        }//if


        if($modelo!='')
        {
            $modelo=explode(" ", trim($modelo));
            for($i=0; $i<=$modelo[$i]; $i++)
            {
                $modelo[$i]=trim(str_replace(array("'","\"",),array("�","�"),$modelo[$i]));
                if($modelo[$i]!="")
                    $filtro.=" AND (bitacora.modelo LIKE '%".$modelo[$i]."%')  ";
            }//for
        }//if

        if($accion!='')
        {
            $accion=explode(" ", trim($accion));
            for($i=0; $i<=$accion[$i]; $i++)
            {
                $accion[$i]=trim(str_replace(array("'","\"",),array("�","�"),$accion[$i]));
                if($accion[$i]!="")
                    $filtro.=" AND (bitacora.accion LIKE '%".$accion[$i]."%')  ";
            }//for
        }//if

        if($referencia!='')
        {
            $referencia=explode(" ", trim($referencia));
            for($i=0; $i<=$referencia[$i]; $i++)
            {
                $referencia[$i]=trim(str_replace(array("'","\"",),array("�","�"),$referencia[$i]));
                if($referencia[$i]!="")
                    $filtro.=" AND (bitacora.referencia LIKE '%".$referencia[$i]."%')  ";
            }//for
        }//if

        if($desde!='' && $hasta!='')
        {
            $desde = $desde." 00:00:00";
            $hasta = $hasta." 23:59:59";

            $filtro.=" AND (bitacora.updated_at >= '".$desde."') AND (bitacora.updated_at <= '".$hasta."') ";
        }//if
*/

        $registros = Bitacora::grid($usuario,$modelo,$accion,$referencia,$desde,$hasta);


//        $registros = My_Comun::registrosGrid("bitacora", $filtro,$alias,$modelos);
        $grid=array();
        $i=0;

        $editar = My_Comun::tienePermiso("EDITAR_USUARIO");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_USUARIO");
//            print_r($registros);
//            exit;
        for($k=0;$k < count($registros['registros']); $k++)
        {
                $grid[$i]['updated_at']=date_format($registros['registros'][$k]->fecha,"d-m-Y H:i:s");
                $grid[$i]['usuario']=$registros['registros'][$k]->usuario;
                $grid[$i]['modelo']=$registros['registros'][$k]->modelo;
                $grid[$i]['accion']=$registros['registros'][$k]->accion;
                $grid[$i]['referencia']=$registros['registros'][$k]->referencia;
                $grid[$i]['bit_id']=$registros['registros'][$k]->id;
                
                            
            $i++;
        }//foreach
        My_Comun::armarGrid($registros,$grid);
    }//function
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
			
        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtener("BItacora", "id", $_POST["id"]);
        }
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Bitacora";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agregar bitacora";
        	$bitacora[0]["editar"] = "Editar bitacora";
                   
            $usuarioId = My_Comun::Guardar("Bitacora", $_POST, $_POST["id"], $bitacora);
            echo($usuarioId);
    }//guardar
	
    public function permisosAction(){
        $this->_helper->layout->disableLayout();
        
        $this->view->registro=My_Comun::obtener('Bitacora', "id", $_POST["id"]);
        $this->view->nombre  =$this->view->registro->nombre;
        $this->view->permisos=explode("|",$this->view->registro->permisos);

    }//function

    public function guardarpermisosAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE); 

        $registro=My_Comun::obtener('Bitacora', "id", $_POST["id"]);
        //print_r($_POST);
        //exit;
        Usuario::guardarPermisos($_POST['permisos'],$_POST['id']);
    }//function

    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
       // $regi=My_Comun::obtener("Usuario", "id", $_POST["id"]);
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Bitacora";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar bitacora";
        $bitacora[0]["deshabilitar"] = "Deshabilitar bitacora";
        $bitacora[0]["habilitar"] = "Habilitar bitacora";
			
        echo My_Comun::eliminar("Bitacora", $_POST["id"], $bitacora);
    }//function 

 
}//class
?>