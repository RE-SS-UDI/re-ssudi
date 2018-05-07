<?php
class backend_UsuarioController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/usuario.js');
       
    }//function
 
    public function indexAction(){
      
    	$sess=new Zend_Session_Namespace('permisos');
        print_r($sess->permisos);
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_USUARIO")!==false;

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

        if($this->_getParam('status')!="")			
            $filtro.=" AND u.status=".$this->_getParam('status');
        
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
        //Verificamos el tipo d usurio
        if(Zend_Auth::getInstance()->getIdentity()->tipo_usuario != 3){
            $zona = Usuario::obtieneZonaUsuario(Zend_Auth::getInstance()->getIdentity()->id);
            $filtro .= " AND e.zona_id = ".$zona->id." ";
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

        $grid=array();
 
    	$i=0;

        $permisos = My_Comun::tienePermiso("PERMISOS_USUARIO");
        $editar = My_Comun::tienePermiso("EDITAR_USUARIO");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_USUARIO");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $grid[$i]['nombre']=$registros['registros'][$k]->nombre.' '.$registros['registros'][$k]->apellido_pat.' '.$registros['registros'][$k]->apellido_mat;
                $grid[$i]['usuario']=$registros['registros'][$k]->usuario;
                $grid[$i]['tipo']=$registros['registros'][$k]->descripcion;
                $grid[$i]['zona']=$registros['registros'][$k]->zona;
                $grid[$i]['empresa']=$registros['registros'][$k]->empresa;

            if($registros['registros'][$k]->status == 0)
            {
                $grid[$i]['permisos'] = '<i class="boton fa fa-check fa-lg text-danger"></i>';   
                $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                
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
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/usuario/agregar\','.$registros['registros'][$k]->id.', \'frm-1\',\'Usuario\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
                else
                    $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';

                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Deshabilitar / Habilitar"><i class="boton fa fa-times-circle fa-lg azul"></i></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle fa-lg text-danger"></i>';						
            }
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);

        $this->view->tipos = My_Comun::obtenerFiltroSQL('tipo_usuario', ' WHERE status = 1 ', ' descripcion asc');
        $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
			
        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("usuario", "id", $_POST["id"]);
            $this->view->personas = My_Comun::obtenerSQL("persona", "id", $this->view->registro->persona_id);
            $this->view->bandera = true;
        }else{
            $this->view->personas = Persona::obtenerPersonas();
            $this->view->bandera = false;
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
       
        $filtro=" 1=1 ";
        $i=6;
        $data = array();
        
        
        if($this->_getParam('status')!=""){         
            $filtro.=" AND status='".str_replace("'","�",$this->_getParam('status'))."' ";
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
                    $filtro.=" AND ( nombre LIKE '%".$nombre[$j]."%'  ) ";
                }
            }  
        }

       
        $i++;
        $registros=  My_Comun::obtenerFiltro("Usuario", $filtro, "nombre ASC");

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
                    "B$i" =>utf8_encode($registro->nombre),
                    "C$i" =>$registro->correo_electronico,
                     "D$i" =>$a
                    );
        }       
        $objPHPExcel->createExcel('Usuario', $columns_name, $data, 10,array('rango'=>'A4:G4','texto'=>'Usuarios'));
    }

    public function imprimirAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
          
        $nombre= $this->_getParam('nombre');
       
        $filtro=" 1=1 ";

        if($this->_getParam('status')!=""){         
            $filtro.=" AND status='".str_replace("'","�",$this->_getParam('status'))."' ";
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
                    $filtro.=" AND ( nombre LIKE '%".$nombre[$j]."%'  ) ";
                }
            }  
        }
        
        

        $registros = My_Comun::obtenerFiltroSQL("usuario", $filtro);
       
        $pdf= new My_Fpdf_Pdf();
        
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->Header("IMPRESIÓN DE USUARIOS");

        $pdf->SetFont('Arial','B',11);
        $pdf->SetWidths(array(35,55,55,40));
        $pdf->Row(array('NO. DE USUARIO','NOMBRE','CORREO','ESTATUS'),0,1);
        
        $pdf->SetFont('Arial','',10);
        foreach($registros as $registro)
        {
            $estatus = '';
            switch($registro['status']){
                case 0: $estatus = 'Inhabilitado'; break;
                case 1: $estatus = 'Habilitado'; break;
            }

            $pdf->Row
            (
                array
                (                    
                    $registro->id, utf8_encode($registro->nombre), $registro->correo_electronico, $estatus
                ),0,1           
            );
        }
                
       $pdf->Output();
    }




}//class
?>