<?php

class Backend_ConcentradoProyController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/concentrado-proy.js?'.time());
    }

    public function indexAction()
    {

    }

    

    public function tablaAction()
    {
        // action body
        $this->_helper->layout->disableLayout();

        $nombre=$this->_getParam('nombre');

        $filtro_nombre = '';
        $filtro_zona = '';
        $filtro_zonaUser = '';

        if($nombre!='')
            {
                $filtro_nombre = " where nombre LIKE '%".$nombre."%'";
            }else{
                $filtro_zona .= " WHERE 1=1 ";
            }

        // $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        // $filtro_zona .= " AND zona_id = ".$zona->id." ";

        $zona = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $filtro_zona .= " AND ( 1=2 ";

        foreach($zona as $zonas)
        {
            $filtro_zona .= " OR zona_id = ".$zonas->id." ";
        }
        $filtro_zona .= " ) ";

        
        $idPer = Zend_Auth::getInstance()->getIdentity()->persona_id;
        $this->view->tipo_usuario = Zend_Auth::getInstance()->getIdentity()->tipo_usuario;

        // $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
        $this->view->zonaUser = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

        foreach($this->view->zonaUser as $zonasU)
        {
            $filtro_zonaUser .= " OR zona_id = ".$zonasU->id." ";
        }



        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario == 3){     // es root
            $filtro = '';
            // $filtro = $filtro_nombre.$filtro_zona;
            $this->view->areaproyectos = My_Comun::obtenerFiltroSQL("area_proyecto", "where status = 1 and (1=2 ".$filtro_zonaUser. ")", "descripcion asc");
            $this->view->empresas = My_Comun::obtenerFiltroSQL("empresa",$filtro, "nombre asc");

            echo "<script>console.log( 'Debug zona: " .  $filtro_zonaUser. "' );</script>";
            echo "<script>console.log( 'Debug empresas: " .  $filtro_nombre.$filtro_zona . "' );</script>";
        }
        //new code 
        else if (Zend_Auth::getInstance()->getIdentity()->tipo_usuario == 6)    // es empresa 
        {


            $this->view->idEmpresa = Empresa::obtenerIdEmpresa($idPer);
            $filtro_idEmpresa .= " and id = ".$this->view->idEmpresa[0]->id." ";
            $this->view->areaproyectos = ConcentradoProyecto::obtenerAreasProyectos($this->view->zonaUser[0]->id,$this->view->idEmpresa[0]->id );
            $this->view->empresas = My_Comun::obtenerFiltroSQL("empresa",$filtro_nombre.$filtro_zona.$filtro_idEmpresa, "nombre asc");
            
            
        }
        else 
        {

            $this->view->areaproyectos = My_Comun::obtenerFiltroSQL("area_proyecto", "where status = 1 and (1=2 ".$filtro_zonaUser. ")", "descripcion asc");
            $this->view->empresas = My_Comun::obtenerFiltroSQL("empresa",$filtro_nombre.$filtro_zona, "nombre asc");

            echo "<script>console.log( 'Debug zona: " .  $filtro_zonaUser. "' );</script>";
            echo "<script>console.log( 'Debug empresas: " .  $filtro_nombre.$filtro_zona . "' );</script>";
        }

        $this->view->proyectos_empresas = My_Comun::obtenerFiltroSQL("empresa",$filtro_nombre.$filtro_zona, "nombre asc");
    }


/*    public function pdfAction()
    {
        $oPdf = new Zend_Pdf();
        $oPdf = Zend_Pdf::load("/public/santander.pdf");
        $oPdf->load("./public/santander.pdf");
        Set headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename=filename.pdf');
        echo $oPdf->render();
        Prevent anything else from being outputted
        die();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $pdf = Zend_Pdf::load('/public/santander.pdf');
        $this->getResponse()->setHeader('Content-type', 'application/x-pdf', true);
        $this->getResponse()->setHeader('Content-disposition', 'inline; filename=filetrace.pdf', true);
        $this->getResponse()->setBody($pdf->render());
    }*/

    public function agregarProyEmpresaAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
        
        $sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_CONCENTRADOPROY")!==false;
        $this->view->siguienteId = My_Comun::obtenersiguienteId();


        if($_POST["id"]!="0"){
            $this->view->registroProyectoEmpresa=My_Comun::obtenerSQL("empresa_proyecto", "id", $_POST["id"]);
        }

        $this->view->idProy = My_Comun::obtenerSQL("proyecto", "id", $_POST["proyecto_id"]);
        $this->view->idEmp =  My_Comun::obtenerSQL("empresa", "id", $_POST["empresa_id"]);

    }

    public function guardarproyectoempresaAction()
    {   

        $siguienteId = 0;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
            $bitacora = array();
            $bitacora[0]["modelo"] = "Concentrado Proyecto";
            $bitacora[0]["campo"] = $_POST["nombre_proyecto "];
            $bitacora[0]["id"] = $_POST["id"];
            $bitacora[0]["agregar"] = "Asigna proyecto a empresa";
            $bitacora[0]["editar"] = "Edita proyecto de empresa";
            $data = array();
            $data['id'] = $_POST['id'];
            $data['empresa_id'] = $_POST['empresa_id'];
            $data['proyecto_id'] = $_POST['proyecto_id'];
            $data['status'] = $_POST['status'];
            $data['periodo'] = $_POST['periodo'];
            $data['observaciones'] = $_POST['observaciones'];
            if(isset($_FILES['file']) || $_POST['tiene_archivos'] != 0){
                $data['tiene_archivos'] = 1;            
            }else {
                $data['tiene_archivos'] = 0;
            }
            $data['ano'] = $_POST['ano'];
            $data['nombre_alumno'] = $_POST['nombre_alumno'];
            $data['apellidop_alumno'] = $_POST['apellidop_alumno'];
            $data['apellidom_alumno'] = $_POST['apellidom_alumno'];
            $data['nombre_proyecto'] = $_POST['nombre_proyecto'];
            $siguienteId = $_POST['siguienteId'];
            $id = $_POST['id'];
            $inicial = 1;
            echo My_Comun::guardarSQL("empresa_proyecto", $data, $data["id"], $bitacora);
            
            if($_FILES['file']['size'] != 0 && $_FILES['file']['error'] == 0) {    
                    if(isset($_FILES['file'])){
                        $ext = pathinfo($_FILES['file']['name']);
                        if ($id==0) {
                            if ($siguienteId > 0) {
                                $_FILES['file']['name'] = $siguienteId.'.'.$ext['extension'];
                            }else{
                                $_FILES['file']['name'] = $inicial.'.'.$ext['extension'];
                            }                            
                        }else {
                            $_FILES['file']['name'] = $id.'.'.$ext['extension'];
                        }
                    }
                    $adapter = new Zend_File_Transfer_Adapter_Http(); 
                    $adapter->addValidator('Extension', false, 'zip');
                    $adapter->addValidator('FilesSize',
                      false,
                      array('min' => '10kB', 'max' => '20MB'));
                    $adapter->setDestination('../public/proyectos');
                    if (!$adapter->receive()) {
                        $messages = $adapter->getMessages();
                        echo implode("\n", $messages);
                    }
            }
    }   

}



