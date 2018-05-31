urlEliminar='/backend/usuario/eliminar';

$(document).ready(function(){
	var server = window.location.hostname;
	var link = "http://"+server+"/backend/ingreso/salir";
	var value = __obtenerTiempo ();
    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: link, success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value  //10 segundos
		})
		
	var filtro="/backend/usuario/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"p.nombre",       width: 280, sortable: true, align: "center"},
//			{display: "Usuario",           name:"u.usuario",       width: 100, sortable: true, align: "center"},
			{display: "Tipo de usuario",name:"tu.descripcion",       width: 150, sortable: true, align: "center"},			
			{display: "Zona empresa",name:"z.nombre",       width: 156, sortable: true, align: "center"},			
			{display: "Empresa",name:"e.nombre",       width: 250, sortable: true, align: "center"},			
//			{display: 'Permisos',         name:"permisos",     width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Edita zona ',           name:"editar_zona",       width: 100, sortable : false, align: 'center'},
			{display: 'Habilitar/Deshabilitar',         name:"eliminar",     width: 155, sortable : false, align: 'center'}
		],
		sortname: "p.nombre"
		,sortorder: "asc"
		,usepager: true
        ,useRp: false
        ,singleSelect: true
        ,resizable: false
        ,showToggleBtn: false
        ,rp: 10
        ,width: 1200
        ,height: 400
	});

    $('#fecha_nacimiento').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: "+0d"
    });

});


function permisos(id){
	$.ajax({
		type: "POST"
		,url: "/backend/usuario/permisos"
		,data: {
			id: id
		}
		,success: function(html){
			$("#_dialogo-1").html(html);
			$("#frm1").validate({
				submitHandler: function(form){
					$("#es-1").removeClass('hide');
					$("#frm1").addClass('hide');
					$("#aceptar-1").addClass('hide');
					$("#cancelar-1").addClass('hide');
					$(form).ajaxSubmit({
						success: function(respuesta){
							$("#_dialogo-1").dialog("close");
						}
					})
				}
			})
			$("#_dialogo-1").dialog({
				width: "900"
				,height: "auto"
				,title: "Usuario"
				,resizable: false
				,draggable: false
				,modal: true
				,buttons: [
				]//buttons
			})//dialog
		}
	});
}//function


function masZonas()
{
	var tipo = "extra";
	var cantidad = 1;
	var zona_id = $("#zona_id").val();
	var persona_id = $("#persona_id").val(); 
	// console.log(persona_id);

	if (tipo != '') {
		$.ajax({
			url: '/backend/usuario/mas-zonas',
			type: 'POST',
			data: {tipo: tipo, cantidad: cantidad, zona_id: zona_id, persona_id: persona_id},
			success: function(res){
				$("#opciones").append(res);
			}
		});
	}
}

function eliminaOpcion(id)
{
	$('#opcion_'+id).remove();
}

function eliminaOpcionA(id)
{
	$.ajax({
		success: function(res){
			$('#opcion_z'+id).remove();
		}
	
});
}


function eliminarOpciones(id)
{
	$.ajax({
		url: '/backend/usuario/eliminar-opciones',
		type: 'POST',
		data: {id: id},
		success: function(res)
		{
			if (res == 'El registro fue eliminado') {
				$('#opcion_'+id).remove();
				
			} 
		}
	});
}


function eliminarOpcionesAgregadas(id)
{
	$.ajax({
		url: '/backend/usuario/eliminar-opciones-agregadas',
		type: 'POST',
		data: {id: id},
		success: function(res)
		{
			// console.log(id);
			if (res == 'El registro fue eliminado') {
				$('#opcion_z'.id).remove();
				eliminaOpcionA(id);
			} 
		}
	});
	
}



function guardarPermisos(){
    $("#frm-1").validate({
        submitHandler: function(form){
            $(form).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta)){   
                        $("#_dialogo-1").dialog("close");
			 			window.location="/backend/usuario";                   
                    }else{                       
                    	_mensaje("#_mensaje-1",  respuesta );
                    }                        
                        
                } //success
                ,error: function(respuesta){
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
                } //error
            }) //ajaxSubmit
        } //submitHandler
    }) //validate
    $("#frm-1").submit();    
}