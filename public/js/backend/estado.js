urlEliminar='/backend/estado/eliminar';

$(document).ready(function(){
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
	var filtro="/backend/estado/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fpaterno").val()!="")       filtro+="/paterno/"+$("#fpaterno").val();
	if($("#fmaterno").val()!="")       filtro+="/materno/"+$("#fmaterno").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Estado",           name:"es.estado",       width: 370, sortable: true, align: "center"},
			{display: 'status',         name:"es.status",     width: 250, sortable : false, align: 'center'},
			{display: 'Habilitar',           name:"habilitar",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Eliminar',         name:"eliminar",     width: 100, sortable : false, align: 'center'}
		],
		sortname: "es.estado"
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
        maxDate: "+0d",
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort: 
            ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
            "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
    });        
});

function guardarPreregistro(formulario, frmFiltro, filtroinicial, urlImprimir, urlExportar)
{
	    $("#"+formulario).validate({
	        submitHandler: function(frm)
	        {
	            
	            $(frm).ajaxSubmit({
	                success: function(respuesta){
	                    if(!isNaN(respuesta))
	                    {   
							
	                        $("#_dialogo-1").dialog("close");
	                        recargar();
	                        _mensaje('#_mensaje-1',  'Se guardó de forma correcta');                       
	                    }else{    
							$("#_dialogo-1").dialog("close");
							recargar();                  
							// _mensaje("#_mensaje-1",  respuesta ); 
							_mensaje("#_mensaje-1",  'Se guardó de forma correcta' );
							
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


function cambiaZona(){
	var filtro2 = '/backend/estado/grid';

	if ($('#zona_id').val() != '') {
		var zona = $('#zona_id').val();
console.log(zona);
  		filtro2+="/zona_id/"+zona;

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

function cambiaStatus(id, estatus)
{
    console.log("id a cambiar "+id);
    console.log("Estatus actual "+estatus);
    var urlStatus = '/backend/estado/status';
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
