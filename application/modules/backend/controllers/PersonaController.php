<?php
class Backend_PersonaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/persona.js?'.time());
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PERSONA")!==false;

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";


        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            $filtro .= " AND e.zona_id = ".$zona->id." ";
        }

        $nombre=$this->_getParam('nombre');
        $paterno=$this->_getParam('paterno');
        $materno=$this->_getParam('materno');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='' )
        {
            $nombre=explode(" ", trim($nombre));
            for($i=0; $i<=$nombre[$i]; $i++)
            {
                $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
        		if($nombre[$i]!="")
                    $filtro.=" AND (p.nombre LIKE '%".$nombre[$i]."%')";
            }//for
        }//if

        if($paterno!='' )
        {
            $paterno=explode(" ", trim($paterno));
            for($i=0; $i<=$paterno[$i]; $i++)
            {
                $paterno[$i]=trim(str_replace(array("'","\"",),array("�","�"),$paterno[$i]));
                if($paterno[$i]!="")
                    $filtro.=" AND (p.apellido_pat LIKE '%".$paterno[$i]."%') ";
            }//for
        }//if

        if($materno!='' )
        {
            $materno=explode(" ", trim($materno));
            for($i=0; $i<=$materno[$i]; $i++)
            {
                $materno[$i]=trim(str_replace(array("'","\"",),array("�","�"),$materno[$i]));
                if($materno[$i]!="")
                    $filtro.=" AND (p.apellido_mat LIKE '%".$materno[$i]."%') ";
            }//for
        }//if



        $consulta = "SELECT p.id, p.nombre, p.apellido_pat, p.apellido_mat, p.correo, p.telefono, p.status
                      FROM persona p
                      JOIN empresa e
                      ON e.id = p.empresa_id
                      WHERE ".$filtro;

  
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PERSONA");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_PERSONA");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
                $grid[$i]['apellido_pat'] =$registros['registros'][$k]->apellido_pat;
                $grid[$i]['apellido_mat'] =$registros['registros'][$k]->apellido_mat;
                $grid[$i]['correo'] =$registros['registros'][$k]->correo;
//                $grid[$i]['telefono'] =$registros['registros'][$k]->telefono;
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/persona/agregar\','.$registros['registros'][$k]->id.', \'frmPersona\',\'Ficha de persona\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
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


        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
        }else {

            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($this->view->zonaUser[0]->id);
        }  

		
        //$this->view->empresas = My_Comun::obtenerFiltroSQL('empresa', ' WHERE status = 1 ', ' nombre asc ');

        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("persona", "id", $_POST["id"]);
        }
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Persona";
        	$bitacora[0]["campo"] = "nombre";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega persona";
        	$bitacora[0]["editar"] = "Editar persona";

            $preId = My_Comun::guardarSQL("persona", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Persona";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar persona";
        $bitacora[0]["deshabilitar"] = "Deshabilitar persona";
        $bitacora[0]["habilitar"] = "Habilitar persona";
			
        echo My_Comun::eliminarSQL("persona", $_POST["id"], $bitacora);
    }//function 

}//class
?>