$(document).ready(function() {
    var value = __obtenerTiempo ();
    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: "ingreso/salir", success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value  //10 segundos
        })


		var filtro = '/backend/asigna-encuesta/grid';
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
            {display: "Nombre de encuesta",           name:"e.nombre",       width: 300, sortable: true, align: "center"},
            {display: "Nombre de categoria",           name:"cat.nombre",       width: 260, sortable: true, align: "center"},
            {display: "Zona",           name:"z.nombre",       width: 260, sortable: true, align: "center"},
            {display: "Status", name:"ze.status", width: 150, sortable: true, align: "center"},
            {display: "Habilitar/Deshabilitar", name:"enable", value:"ze.id", width: 105, sortable: true, align: "center"},
            {display: "Remover", name:"remove", value:"ze.id", width: 105, sortable: true, align: "center"}


		],
		sortname: "e.nombre"
		,sortorder: "asc"
		,usepager: true
        ,useRp: false
        ,singleSelect: true
        ,resizable: false
        ,showToggleBtn: false
        ,rp: 10
        ,width: 1200
        ,height: 300
	});		
});



function cambiaZona(){
	var filtro2 = '/backend/asigna-encuesta/grid';

	if ($('#zona_id').val() != '') {
		var zona = $('#zona_id').val();
		var encuestas = '';
//		$('#lista_encuestas input:checked').each(function(indice,elemento){
//			console.log(indice);
//			console.log(elemento.value);
//	  		encuestas += elemento.value+',';
//		});

//		$('#encuestas_seleccionadas').val(encuestas);

  		filtro2+="/zona_id/"+zona;
//alert(filtro);
  		//filtro+="/encuestas/"+encuestas;

	    $("#flexigrid").flexOptions({
			url: filtro2,
	        onSuccess: function(){
	        }

		}).flexReload();
	}else{
	    $("#flexigrid").flexOptions({
			url: filtro2,
	        onSuccess: function(){
	        }

		}).flexReload();

	}
}

function guardarAsignacion(formulario)
{
    $("#"+formulario).validate({
        submitHandler: function(frm)
        {
            
            $(frm).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta))
                    {   

                        cambiaZona();
                        _mensaje('#_mensaje-1',  'Se guardó la asignación de forma correcta.');                       
                    }else{                       
                        _mensaje("#_mensaje-1",  respuesta );
                    }                        
                        
                } //success
                ,error: function(respuesta){
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
                } //error
            }) //ajaxSubmit*/
        } //submitHandler
    }) //validate
    
    $("#"+formulario).submit();     
}

function limpiarAsinacion(formulario){
	$('#'+formulario)[0].reset();
	cambiaZona();
}

function eliminar(id, estatus)
{
    console.log(id);
    var urlEliminar = '/backend/asigna-encuesta/eliminar';
    _confirmar(
    "#_mensaje-1"
    ,"¿Est&aacute; seguro que quiere "+(estatus==1?"eliminar":"activar")+" el registro?"
    ,function(){
            $("#es_mensaje-1").removeClass("hide");
            $("#texto_mensaje-1").addClass("hide");
            $("#si-999").addClass('hide');
            $("#no-999").addClass('hide');
            $.ajax({
                type: "POST",
                url: urlEliminar,
                data: {
                    id: id
                },
                success: function(respuesta)
                {
                    recargar();
                    _mensaje("#_mensaje-1", respuesta);
                },
                error: function(respuesta){
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error al tratar de eliminar el registro, int&eacute;ntelo de nuevo");
                } //error
            });
        }
    );
}//function

function cambiaStatus(id, estatus)
{
    console.log(id);
    console.log(estatus);
    var urlStatus = '/backend/asigna-encuesta/status';
    _confirmar(
    "#_mensaje-1"
    ,"¿Est&aacute; seguro que quiere "+(estatus==1?"Deshabilitar":"Habilitar")+" el registro?"
    ,function(){
            $("#es_mensaje-1").removeClass("hide");
            $("#texto_mensaje-1").addClass("hide");
            $("#si-999").addClass('hide');
            $("#no-999").addClass('hide');
            $.ajax({
                type: "POST",
                url: urlStatus,
                data: {
                    id: id, status: estatus
                },
                success: function(respuesta)
                {
                    recargar();
                    _mensaje("#_mensaje-1", respuesta);
                },
                error: function(respuesta){
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error al tratar de eliminar el registro, int&eacute;ntelo de nuevo");
                } //error
            });
        }
    );
}//function