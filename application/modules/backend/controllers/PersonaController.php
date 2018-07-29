<?php
class Backend_PersonaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/persona.js?'.time());

       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_PERSONA")!==false;
        
        $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->tipo_personas = My_Comun::obtenerFiltroSQL('tipo_persona', ' WHERE status = 1 ', ' descripcion asc');


    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";


        //Verificamos el tipo d usurio
        // if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
        //     $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        //     $filtro .= " AND e.zona_id = ".$zona->id." ";
        // }

        $nombre=$this->_getParam('nombre');
        $paterno=$this->_getParam('paterno');
        $materno=$this->_getParam('materno');
        $status=$this->_getParam('status');
        $zona=$this->_getParam('zona_idS');
        $estado=$this->_getParam('estado_idS');
        $tipo=$this->_getParam('tipo_idS');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND p.status=".$this->_getParam('status');
        
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

        if($zona!='')
        {
            $filtro.=" AND (e.zona_id = '".$zona."') ";
        }else{
            $filtro.=" AND (e.zona_id = '0') ";
        }
         if($tipo!='')
         {
             $filtro.=" AND (p.tipo_id = '".$tipo."') ";
         }else{
            $filtro.=" AND (p.tipo_id = '0') ";
         }
        if($estado!='')
        {
            $filtro.=" AND (z.estado_id = '".$estado."') ";
        }else{
            $filtro.=" AND (z.estado_id = '0') ";
        }
        // if($estado!='')
        // {
        //     $filtro.=" AND (pr.estado_id = '".$estado."') ";
        // }else{
        //     $filtro.=" AND (pr.estado_id = '".$k."') ";
        // }



        $consulta = "SELECT p.id, p.nombre, p.apellido_pat, p.apellido_mat, p.correo, p.telefono, p.status
                      FROM persona p
                      Inner JOIN empresa e
                      ON e.id = p.empresa_id
                      Inner JOIN zona z
                      ON z.id = e.zona_id
                      WHERE ".$filtro;

  
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_PERSONA");
        $eliminar = My_Comun::tienePermiso("ELIMINAR_PERSONA");
        $permisos = My_Comun::tienePermiso("PERMISOS_PERSONA");
            
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
                $grid[$i]['editar_zona'] = '<i class="boton fa fa-pencil-square-o fa-lg text-danger"></i>';

                
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

                if($permisos)
                    $grid[$i]['editar_zona'] = '<span onclick="agregar(\'/backend/persona/agregar-zona\','.$registros['registros'][$k]->id.', \'frm-1\',\'Agregar Zona\' );" title="Editar Zona"><i class="boton fa fa-pencil-square-o fa-lg azul"></i></span>';
                else
                    $grid[$i]['editar_zona'] = '<i class="boton fa fa-pencil-square-o fa-lg text-danger"></i>';


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
    
    // public function agregarAction(){
    //     $this->_helper->layout->disableLayout();
    //     $this->view->llave = My_Comun::aleatorio(20);


    //     $idPer = Zend_Auth::getInstance()->getIdentity()->id;
    //     $this->view->zonaUser = My_Comun::obtenerZonas($idPer);
    //     $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);

    //     if($this->view->tipoUser[0]->tipo_usuario == 3){

    //         $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
    //     }else {

    //         $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($this->view->zonaUser[0]->id);
    //     }  

		
    //     //$this->view->empresas = My_Comun::obtenerFiltroSQL('empresa', ' WHERE status = 1 ', ' nombre asc ');

    //     if($_POST["id"]!="0"){
    //         $this->view->registro=My_Comun::obtenerSQL("persona", "id", $_POST["id"]);
    //     }
    // }//function

    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);


        $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        
        $this->view->tipo_personas = My_Comun::obtenerFiltroSQL('tipo_persona', ' WHERE status = 1 ', ' descripcion asc');

		
        $idPer = Zend_Auth::getInstance()->getIdentity()->persona_id;

        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);

        

        if($this->view->tipoUser[0]->tipo_usuario == 3){
            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        }else {
            // $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresa($this->view->zonaUser[0]->id);
            $this->view->empresas = My_Comun::obtenerFiltroSQLEmpresaRoot();
            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE id = 5 or id = 6  ', ' descripcion asc');
        } 



        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("persona", "id", $_POST["id"]);


            $this->view->personas = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);
            $this->view->bandera = true;


        }else{


            if($this->view->tipoUser[0]->tipo_usuario == 3){

                $this->view->personas = Persona::obtenerPersonas();
                $this->view->bandera = false;

            }else {

                $this->view->personas = Persona::obtenerPersonasZonas($this->view->zonaUser[0]->id);
                $this->view->bandera = false;
            } 


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

    public function guardazonaAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Usuario";
        	$bitacora[0]["campo"] = "uduario_id";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agregar usuario-zona";
        	$bitacora[0]["editar"] = "Editar usuario-zona";
                   //print_r($_POST);
                   //exit;
            
            // $catego_id = My_Comun::obtenerSQL('tipo_usuario','id',$_POST['tipo_usuario']);
            // $encuesta_id = My_Comun::obtenerSQL('tipo_usuario','id',$_POST['tipo_usuario']);

            $usuarioId = My_Comun::guardarSQLpersonaZona("persona_zona", $_POST, "0", $bitacora);
            echo($usuarioId);
    }//guardarZona

    public function masZonasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $tipo = $_POST['tipo'];
        $opciones = '';
        $zona_id = $_POST['zona_id'];
        $persona_id = $_POST['persona_id'];
        $tipo_id = $_POST['tipo_id'];

        $zonaName = My_Comun::obtenerFiltroSQL('zona', ' WHERE id = '.$zona_id.' ', ' nombre asc');
        $tiposPersona = My_Comun::obtenerFiltroSQL('tipo_persona', ' WHERE id = '.$tipo_id.' ', ' descripcion asc');
        $UsrPersona = Usuario::obtieneUsuarioPersona($persona_id);  
        // $idPer = Zend_Auth::getInstance()->getIdentity()->id;  

        // echo("<script>console.log('PHP: $UsrPersona->id +  ');</script>");


        $usuarioId = Usuario::guardarSQLpersonaZona($zona_id, $persona_id, $tipo_id);
        echo($usuarioId);

        if($usuarioId == null){
        for ($i=0; $i < $_POST['cantidad']; $i++) { 
            $time = time();
            $opciones .= '<div id="opcion_'.$time.'" class="col-xs-12 form-group">
                            <label class="col-xs-2 control-label">Descripción:</label>
                        <div class="col-xs-4">
                            <input type="text" value="'.$zonaName[0]->nombre.' '.$tiposPersona[0]->descripcion.'" name="opciones[]" id="opcion_'.$time.'" class="form-control input-sm required" maxlength="100">
                        </div>
                        <div class="col-xs-2">
                            <a class="btn btn-danger" title="Eliminar" onclick="eliminaOpcion(\''.$time.'\')"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Eliminar</a>
                        </div>
                      </div>
                    ';
        }
        echo $opciones;
        }
    }

    
    public function eliminarOpcionesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "persona_zona";
        $bitacora[0]["campo"] = "zona_id";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar opción";
        $bitacora[0]["deshabilitar"] = "Deshabilitar opción";
        $bitacora[0]["habilitar"] = "Habilitar opción";

        echo '<script>console.log("'. $_POST["id"].'");</script>';

            
        echo My_Comun::eliminarSQL("persona_zona", $_POST["id"], $bitacora);
    }//function

    public function eliminarOpcionesAgregadasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "persona_zona";
        $bitacora[0]["campo"] = "zona_id";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar opción";
        $bitacora[0]["deshabilitar"] = "Deshabilitar opción";
        $bitacora[0]["habilitar"] = "Habilitar opción";

            
        echo My_Comun::eliminarSQLPersonaZona("persona_zona", $_POST["id"], $bitacora);
    }//function

    
    // function eliminarAction()
    // {
    //     $this->_helper->layout->disableLayout();
    //     $this->_helper->viewRenderer->setNoRender(TRUE);
        
    //     $regi=My_Comun::obtenerSQL("usuario", "id", $_POST["id"]);
            
    //     $bitacora = array();
    //     $bitacora[0]["modelo"] = "Usuario";
    //     $bitacora[0]["campo"] = "nombre";
    //     $bitacora[0]["id"] = $_POST["id"];
    //     $bitacora[0]["eliminar"] = "Eliminar usuario";
    //     $bitacora[0]["deshabilitar"] = "Deshabilitar usuario";
    //     $bitacora[0]["habilitar"] = "Habilitar usuario";
            
    //     echo My_Comun::eliminarSQL("usuario", $_POST["id"], $bitacora);
    // }//function 

    public function agregarZonaAction(){
        
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        echo "<script>console.log( 'Debug Objects: " . $_POST["id"] . "' );</script>";

        $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' WHERE status = 1 ', ' estado asc');

        // $persona_fromUsr = Usuario::ObtienePersonaFromUsuario($_POST["id"]);
        // $this->view->zonasUsr = Usuario::obtieneZonasXususario($_POST["id"]); 
        $this->view->zonasUsr = Usuario::obtieneZonasTiposXususario($_POST["id"]); 

        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        // $this->view->zonaUser = My_Comun::obtenerZonas($_POST["id"]);


        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        }else {

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE id = 5 or id = 6  ', ' descripcion asc');
        } 

        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("usuario", "persona_id", $_POST["id"]);
            $this->view->personas = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);
            $this->view->bandera = true;
            // $this->view->zonas = My_Comun::obtenerSQL("zona");
        }else{
            $this->view->personas = Persona::obtenerPersonasZonas($this->view->zonaUser[0]->id);
            $this->view->bandera = false;
        }
    }//function


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

    public function onChangeEstadoAzAction(){
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

    public function onChangeZonaAzAction(){
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

    public function onChangeEmpresaAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $empresa=$this->_getParam('empresa');
        $filtro = "WHERE status = 1";
        $filtro2 = "WHERE status = 1";
          
        if($empresa!='')
        {
            $filtro.=" AND (id = $empresa) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $zonas_empresa = My_Comun::obtenerFiltroSQLZonaPersonaXempresa($empresa);
        echo json_encode($zonas_empresa);
    }

    public function onChangeTipoAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $tipo=$this->_getParam('tipo');
        $filtro = "WHERE status = 1";
          
        if($tipo!='')
        {
            $filtro.=" AND (tipo_id = $tipo) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $tipo_Pregistro = My_Comun::obtenerFiltroSQL('persona',$filtro, ' nombre asc');
        echo json_encode($tipo_Pregistro);
    }



}//class

?>