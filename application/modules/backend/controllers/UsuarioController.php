<?php
class backend_UsuarioController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/usuario.js?'.time());
       
    }//function
 
    public function indexAction(){
      
    	$sess=new Zend_Session_Namespace('permisos');
        print_r($sess->permisos);
        $this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_USUARIO")!==false;
        // $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        $this->view->tipos = Usuario::obtieneZonasTiposXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);


    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        //Iniciamos filtro
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        $estado=$this->_getParam('estado_id');
        $zona=$this->_getParam('zona_id');
        $tipo=$this->_getParam('tipo_id');

        if($this->_getParam('status')!="")			
            $filtro.=" AND u.status=".$this->_getParam('status');
        

        //Verificamos el tipo d usurio
        // if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
        //     $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        //     $filtro .= " AND e.zona_id = ".$zona->id." ";
        // }



            if($estado!='')
            {
                $filtro.=" AND (z.estado_id = '".$estado."') ";
            }
            else{
                $filtro.=" AND (z.estado_id = '0') ";
            }
            if($zona!='')
            {
                $filtro.=" AND (e.zona_id = '".$zona."') ";
            }
            else{
                $filtro.=" AND (e.zona_id = '0') ";
            }
            if($tipo!='')
            {
                $filtro.=" AND (p.tipo_id = '".$tipo."') ";

                if($nombre!='')
                {
                    $nombre=explode(" ", trim($nombre));
                    for($i=0; $i<=$nombre[$i]; $i++)
                    {
                        $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
                        if($nombre[$i]!="")
                            $filtro.=" AND (p.nombre LIKE '%".$nombre[$i]."%') OR (p.apellido_pat LIKE '%".$nombre[$i]."%') OR (p.apellido_mat LIKE '%".$nombre[$i]."%') ";
                    }//for
                }//if
            }
            else{
                $filtro.=" AND (p.tipo_id = '0') ";
            }

        
        $consulta = "SELECT u.id,u.status,p.nombre,p.apellido_pat, p.apellido_mat, u.usuario, u.tipo_usuario, tu.descripcion, z.nombre as zona, e.nombre as empresa
                      FROM usuario u
                      INNER JOIN persona p
                      on p.id = u.persona_id
                      INNER JOIN tipo_usuario tu
                      on tu.id = u.tipo_usuario
                      INNER JOIN empresa e
                      on e.id = p.empresa_id
                      INNER JOIN zona z
                      on z.id = e.zona_id
                    WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        // global $tipoFiltro;
        $GLOBALS['tipoFiltro'] = $tipo;
        // $tipoFiltro = $tipo;

        $grid=array();
 
    	$i=0;

        $permisos = My_Comun::tienePermiso("PERMISOS_USUARIO");
        $editar = My_Comun::tienePermiso("EDITAR_USUARIO");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_USUARIO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['nombre']=$registros['registros'][$k]->nombre.' '.$registros['registros'][$k]->apellido_pat.' '.$registros['registros'][$k]->apellido_mat;
//                $grid[$i]['usuario']=$registros['registros'][$k]->usuario;
                $grid[$i]['tipo']=$registros['registros'][$k]->descripcion;
                // $grid[$i]['zona']=$registros['registros'][$k]->zona;
                // $grid[$i]['empresa']=$registros['registros'][$k]->empresa;
                $grid[$i]['enviar'] = '<span onclick="reSendCredentials('.$registros['registros'][$k]->id.');" title="Usuario/contrasena"><i class="boton fa fa-envelope-o fa-lg azul"></i></span>';

            if($registros['registros'][$k]->status == 0)
            {
                //$grid[$i]['permisos'] = '<i class="boton fa fa-check fa-lg text-danger"></i>';   
                $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                
                // $grid[$i]['editar_zona'] = '<i class="boton fa fa-pencil-square-o fa-lg text-danger"></i>';
                
                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Eliminar"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle text-danger fa-lg "></i>';
            }
            else
            {

/*                if($permisos){
                    if ($registros['registros'][$k]->tipo_usuario != 3 ) {
                        $grid[$i]['permisos'] = '<i class="boton fa fa-check fa-lg text-danger"></i>';   
                    }else{
                        $grid[$i]['permisos'] = '<span onclick="permisos('.$registros['registros'][$k]->id.');" title="Permisos"><i class="boton fa fa-check fa-lg azul"></i></span>';
                    }
                }
                else{
                    $grid[$i]['permisos'] = '<i class="boton fa fa-check text-danger fa-lg"></i>';
                }
*/                

                if($editar)
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/usuario/agregar\','.$registros['registros'][$k]->id.', \'frm-1\',\'Editar Usuario\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
                else
                    $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';


                // if($permisos)
                //     $grid[$i]['editar_zona'] = '<span onclick="agregar(\'/backend/usuario/agregar-zona\','.$registros['registros'][$k]->id.', \'frm-1\',\'Agregar Zona\' );" title="Editar Zona"><i class="boton fa fa-pencil-square-o fa-lg azul"></i></span>';
                // else
                //     $grid[$i]['editar_zona'] = '<i class="boton fa fa-pencil-square-o fa-lg text-danger"></i>';


                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Deshabilitar / Habilitar"><i class="boton fa fa-times-circle fa-lg azul"></i></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle fa-lg text-danger"></i>';						
            }
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function


    public function agregarZonaAction(){
        
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        echo "<script>console.log( 'Debug Objects: " . $_POST["id"] . "' );</script>";

        $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');

        $this->view->zonasUsr = Usuario::obtieneZonasXususario($_POST["id"]);
        // $this->view->zonasUsr = My_Comun::obtenerFiltroSQL('persona_zona', ' WHERE usuario_id = '.$_POST["id"].' ', ' id asc');


        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);


        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario == 3){

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        }else {

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE id = 5 or id = 6  ', ' descripcion asc');
        } 



        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("usuario", "id", $_POST["id"]);



            $this->view->personas = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);
            $this->view->bandera = true;
            // $this->view->zonas = My_Comun::obtenerSQL("zona");

        }else{



            $this->view->personas = Persona::obtenerPersonasZonas($this->view->zonaUser[0]->id);
            $this->view->bandera = false;


        }

    }//function
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);

        $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');

		
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;

        $this->view->tipoUser = My_Comun::obtenertipoUSer($idPer);
        $this->view->zonaUser = My_Comun::obtenerZonas($idPer);

        

        if($this->view->tipoUser[0]->tipo_usuario == 3){

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        }else {

            $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE id = 5 or id = 6  ', ' descripcion asc');
        } 



        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("usuario", "id", $_POST["id"]);


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
        	$bitacora[0]["modelo"] = "Usuario";
        	$bitacora[0]["campo"] = "usuario";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agregar usuario";
        	$bitacora[0]["editar"] = "Editar usuario";
                   //print_r($_POST);
                   //exit;
            $tipo_usuario = My_Comun::obtenerSQL('tipo_usuario','id',$_POST['tipo_usuario']);
            $_POST['permisos'] = $tipo_usuario->permisos;
            $usuarioId = My_Comun::guardarSQL("usuario", $_POST, $_POST["id"], $bitacora);
            echo($usuarioId);
    }//guardar


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

        $zonaName = My_Comun::obtenerFiltroSQL('zona', ' WHERE id = '.$zona_id.' ', ' nombre asc');
        $UsrPersona = Usuario::obtieneUsuarioPersona($persona_id);    

        // echo("<script>console.log('PHP: $UsrPersona->id +  ');</script>");


        $usuarioId = Usuario::guardarSQLpersonaZona($zona_id, $UsrPersona->id);
        echo($usuarioId);

        if($usuarioId == null){
        for ($i=0; $i < $_POST['cantidad']; $i++) { 
            $time = time();
            $opciones .= '<div id="opcion_'.$time.'" class="col-xs-12 form-group">
                            <label class="col-xs-2 control-label">Descripción:</label>
                        <div class="col-xs-6">
                            <input type="text" value="'.$zonaName[0]->nombre.'" name="opciones[]" id="opcion_'.$time.'" class="form-control input-sm required" maxlength="100">
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
        $bitacora[0]["modelo"] = "usuario_zona";
        $bitacora[0]["campo"] = "zona_id";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar opción";
        $bitacora[0]["deshabilitar"] = "Deshabilitar opción";
        $bitacora[0]["habilitar"] = "Habilitar opción";

        echo '<script>console.log("'. $_POST["id"].'");</script>';

            
        echo My_Comun::eliminarSQL("usuario_zona", $_POST["id"], $bitacora);
    }//function


    public function eliminarOpcionesAgregadasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "usuario_zona";
        $bitacora[0]["campo"] = "zona_id";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar opción";
        $bitacora[0]["deshabilitar"] = "Deshabilitar opción";
        $bitacora[0]["habilitar"] = "Habilitar opción";

            
        echo My_Comun::eliminarSQLPersonaZona("usuario_zona", $_POST["id"], $bitacora);
    }//function



	
    public function permisosAction(){
        $this->_helper->layout->disableLayout();
        
        $this->view->registro=My_Comun::obtenerSQL('usuario', "id", $_POST["id"]);
        $this->view->nombre  =$this->view->registro->nombre;
        $this->view->permisos=explode("|",$this->view->registro->permisos);
//print_r($this->view->registro);
//exit;
    }//function

    public function guardarpermisosAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE); 

        $registro=My_Comun::obtenerSQL('usuario', "id", $_POST["id"]);
        //print_r($_POST);
        //exit;
        Usuario::guardarPermisos($_POST['permisos'],$_POST['id']);
    }//function

    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $regi=My_Comun::obtenerSQL("usuario", "id", $_POST["id"]);
            
        $bitacora = array();
        $bitacora[0]["modelo"] = "Usuario";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar usuario";
        $bitacora[0]["deshabilitar"] = "Deshabilitar usuario";
        $bitacora[0]["habilitar"] = "Habilitar usuario";
            
        echo My_Comun::eliminarSQL("usuario", $_POST["id"], $bitacora);
    }//function 

    public function exportarAction(){
        ### Deshabilitamos el layout y la vista
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
        $nombre= $this->_getParam('nombre');
        $tipo=$this->_getParam('tipo_id');
       
        $filtro=" WHERE 1=1 ";
        $i=6;
        $data = array();
        
        
        if($this->_getParam('status')!=""){         
            $filtro.=" AND u.status='".str_replace("'","�",$this->_getParam('status'))."' ";
            if($this->_getParam('status') == 0){
                $data[] = array("A$i" =>"Estatus:","B$i" => "Deshabilitado"); 
            }else{
                $data[] = array("A$i" =>"Estatus:","B$i" => "Habilitado");         
            }
          $i++;
        }
        
        if($nombre!=''){
            $data[] = array("A$i" =>"Nombre:","B$i" => $nombre);                
            $i++;
            $nombre=explode(" ", trim($nombre));
            for($j=0; $j<=$nombre[$j]; $j++){
                $nombre[$j]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$j]));
                if($nombre[$j]!=""){
                    $filtro.=" AND ( p.nombre LIKE '%".$nombre[$j]."%'  ) ";
                }
            }  
        }

        if($tipo!='')
            {
                $filtro.=" AND (p.tipo_id = '".$tipo."') ";
                $i++;
            }


        // if($GLOBALS['tipoFiltro'] !=''){
        // $filtro.=" AND (p.tipo_id = '".$GLOBALS['tipoFiltro']."') ";
        // $i++;
        // }
       
        $i++;
        // $registros=  My_Comun::obtenerFiltro("Usuario", $filtro, "nombre ASC");

       
        $registros = My_Comun::obtenerFiltroSQLPersonaUsuario($filtro);

         // encode response to json format
         $result = json_decode($registros, true);

        ini_set("memory_limit", "130M");
        ini_set('max_execution_time', 0);

        $objPHPExcel = new My_PHPExcel_Excel();
        
        
        $columns_name = array
        (
                "A$i" => array(
                        "name" => 'No. DE USUARIO',
                        "width" => 16
                        ),
                "B$i" => array(
                        "name" => 'NOMBRE ',
                        "width" => 30
                        ),
                "C$i" => array(
                        "name" => 'CORREO',
                        "width" => 50
                        ),
                "D$i" => array(
                        "name" => 'ESTATUS',
                        "width" => 13
                        )                           
        );

        //Datos tabla
        foreach($registros as $registro)
        {
            if($registro->status == "0"){
                $a = "Deshabilitado";
            }else{
                $a =  "Habilitado";
            }
            
            $i++;
            $data[] = array(                
                    "A$i" =>$registro->id,
                    "B$i" =>utf8_encode($registro->nombreP),
                    "C$i" =>$registro->correoP,
                     "D$i" =>$a
                    );
        }       
        $objPHPExcel->createExcel('Usuario', $columns_name, $data, 10,array('rango'=>'A4:G4','texto'=>'Usuarios'));
    }

    public function imprimirAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
        $nombre= $this->_getParam('nombre');
       
        $filtro=" WHERE 1=1 ";

        if($this->_getParam('status')!=""){         
            $filtro.=" AND u.status='".str_replace("'","�",$this->_getParam('status'))."' ";
            if($this->_getParam('status') == 0){
                $data[] = array("A$i" =>"Estatus:","B$i" => "Deshabilitado"); 
            }else{
                $data[] = array("A$i" =>"Estatus:","B$i" => "Habilitado");         
            }
          $i++;
        }
        
        if($nombre!=''){
            $data[] = array("A$i" =>"Nombre:","B$i" => $nombre);                
            $i++;
            $nombre=explode(" ", trim($nombre));
            for($j=0; $j<=$nombre[$j]; $j++){
                $nombre[$j]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$j]));
                if($nombre[$j]!=""){
                    $filtro.=" AND ( p.nombre LIKE '%".$nombre[$j]."%'  ) ";
                }
            }  
        }
        
        

        // $registros = My_Comun::obtenerFiltroSQL("usuario", $filtro);
        // $registros = My_Comun::obtenerFiltroSQL("usuario"); 
        $registros = My_Comun::obtenerFiltroSQLPersonaUsuario($filtro);
        // encode response to json format
        $result = json_decode($registros, true);
       
        $pdf= new My_Fpdf_Pdf();
        
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->Header("IMPRESIÓN DE USUARIOS");

        $pdf->SetFont('Arial','B',11);
        $pdf->SetWidths(array(35,55,55,40));
        $pdf->Row(array('NO. DE USUARIO','NOMBRE','CORREO','ESTATUS'),0,1);
        if (empty($registros)){
            $pdf->Row(array('0','0','0','0'),0,1);
        }

        foreach($user as $mydata)
        {
            echo $mydata->name . "\n";
            foreach($mydata->values as $values)
            {
                echo $values->value . "\n";
            }
        }   
        
        $pdf->SetFont('Arial','',10);
        foreach($registros as $registro)
        {
            $estatus = '';
                switch($registro->status){
                case 0: $estatus = 'Inhabilitado'; break;
                case 1: $estatus = 'Habilitado'; break;
            }

            $pdf->Row
            (
                array
                (                    
                    $registro->id, utf8_encode($registro->nombreP), $registro->correoP, $estatus
                ),0,1           
            );
        }
                
       $pdf->Output();
    }

    public function recuperarAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $usuario_id=$this->_getParam('usuario');
        // $usuario_id = $_POST['usuario'];
        
        $usuario = My_Comun::obtenerSQL('usuario', 'id', $usuario_id, ' and status = 1');
        $persona = My_Comun::obtenerSQL('persona', 'id', $usuario->persona_id, ' and status = 1');


        // echo("<script>console.log('PHP: persona id: ".$persona->id."');</script>");


        $titulo = "Ressudi UTJ ";
        $cuerpo = "
            Hola ".$persona->nombre.",

            <p><strong>Sistema Administrativo RESSUDI</strong></p>

            <a href=\"http://ca02.utj.edu.mx/\">http://ca02.utj.edu.mx/</a>

            <br />

            <p>A continuacion, usuario y contrase&ntilde;a:</p>

            
            <strong>Usuario:</strong>&nbsp;".$usuario->usuario."
            <br />
            <strong> Contrase&ntilde;a:</strong>&nbsp;".$usuario->contrasena."
        ";

        // echo("<script>console.log('PHP: usuario contra: ".$usuario->contrasena."');</script>");
        // echo("<script>console.log('PHP: usuario user: ".$usuario->usuario."');</script>");
        // echo("<script>console.log('PHP: usuario correo: ".$persona->correo."');</script>");
        
        echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi@utj.edu.mx','Sinergia', $persona->correo, $persona->nombre);

        // echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi.utj@gmail.com','Sinergia', $persona->correo, $persona->nombre);

        // echo My_Comun::envioCorreo($titulo, $cuerpo,'ressudi@utj.edu.mx','Sinergia', $usuario->correo, $persona->nombre);
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
        $zonas = My_Comun::obtenerFiltroSQL('tipo_persona', $filtro, ' descripcion asc');
        echo json_encode($zonas);
    }


}//class
?>