urlEliminar='/backend/tipo-persona/eliminar';

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
	var filtro="/backend/tipo-persona/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fpaterno").val()!="")       filtro+="/paterno/"+$("#fpaterno").val();
	if($("#fmaterno").val()!="")       filtro+="/materno/"+$("#fmaterno").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Descripcion",           name:"tp.descripcion",       width: 370, sortable: true, align: "center"},
			{display: 'status',         name:"tp.status",     width: 250, sortable : false, align: 'center'},
			{display: 'Zona',         name:"z.nombre",     width: 110, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Eliminar',         name:"eliminar",     width: 100, sortable : false, align: 'center'}
		],
		sortname: "tp.descripcion"
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
	var filtro2 = '/backend/tipo-persona/grid';

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
