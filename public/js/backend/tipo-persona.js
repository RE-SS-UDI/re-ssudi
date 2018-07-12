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
			{display: "Descripci&oacute;n",           name:"tp.descripcion",       width: 370, sortable: true, align: "center"},
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

function updateByZona(zona_id){
	var filtro2 = '/backend/tipo-persona/grid';
console.log(zona_id);
  		filtro2+="/zona_id/"+zona_id;

	    $("#flexigrid").flexOptions({
			url: filtro2,
	        onSuccess: function(){
	        }

		}).flexReload();
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


function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/tipo-persona/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
			var zonas = $('#zona_id');
			var cont = 0;
			zonas.empty();
			$('#zona_id').append('<option value="">Selecciona una zona</option>');
                for (var zona in objJSON) {
					if (cont < 1)
						updateByZona(objJSON[zona]['id']);


					console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
					);
					cont++;
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}
