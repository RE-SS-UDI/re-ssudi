urlEliminar='/backend/persona/eliminar';

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


	var filtro="/backend/persona/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fpaterno").val()!="")       filtro+="/paterno/"+$("#fpaterno").val();
	if($("#fmaterno").val()!="")       filtro+="/materno/"+$("#fmaterno").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"p.nombre",       width: 200, sortable: true, align: "center"},
			{display: "Apellido paterno",name:"p.apellido_pat",       width: 150, sortable: true, align: "center"},			
			{display: 'Apellido materno',         name:"p.apellido_mat",     width: 150, sortable : true, align: 'center'},
			{display: 'Correo',         name:"p.correo",     width: 240, sortable : false, align: 'center'},
//			{display: 'Tel&eacute;fono',         name:"p.telefono",     width: 160, sortable : false, align: 'center'},
			{display: 'Estatus',           name:"p.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
            {display: 'Edita zona ',           name:"editar_zona",       width: 100, sortable : false, align: 'center'},
            {display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
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

});


function agregar(urlDestino, identificador, form, titulo)
{
    $.ajax({
        type: "POST",
        url: urlDestino,
        data: {
            id: identificador
        }
        ,success: function(html)
        {
            $("#_dialogo-1").html(html);        

            $('#fecha_nacimiento').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                maxDate: "+0d"
            });

            $("#_dialogo-1").dialog({
                width: "900",
                height: "auto",
                title: titulo,
                resizable: false,
                draggable: false,
                modal: true
            })//dialog
            
        }//success
        ,error: function(respuesta){
            _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
        } //error
    }) //$.ajax
}

function masZonas()
{
	var tipo = "extra";
	var cantidad = 1;
	var zona_id = $("#zona_id").val();
	var persona_id = $("#persona_id").val(); 
	var tipo_id = $("#tipo_id").val();
	
	// console.log(persona_id);

	if (tipo != '') {
		$.ajax({
			url: '/backend/persona/mas-zonas',
			type: 'POST',
			data: {tipo: tipo, cantidad: cantidad, zona_id: zona_id, persona_id: persona_id, tipo_id: tipo_id},
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
		url: '/backend/persona/eliminar-opciones',
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
		url: '/backend/persona/eliminar-opciones-agregadas',
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

function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/persona/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_idS');
            zonas.empty();
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#zona_idS').append('<option value="">-Selecciona una zona-</option>');
			$('#tipo_idS').append('<option value="">-Selecciona un tipo-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaEstadoAZ(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/persona/on-change-estado-az',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_id');
            zonas.empty();
			$('#zona_id').append('<option value="">-Selecciona una zona-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaZonaAZ(zona_id) {
	console.log("zona seleccionada: "+zona_id.value);
	var zona = zona_id.value;
	
	// updateByEstado(estado);

	if (zona != '') {
		$.ajax({
			url: '/backend/persona/on-change-zona-az',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
				console.log('zona cambiada');
                var objJSON = eval("(function(){return " + res + ";})()");
            var tipos = $('#tipo_id');
            tipos.empty();
			$('#tipo_id').append('<option value="">-Selecciona un tipo-</option>');
                for (var tipo in objJSON) {
                    console.log(objJSON[tipo]['descripcion']);
                    tipos.append(
                        $('<option>', {
                        value: objJSON[tipo]['id']
                        }).text(objJSON[tipo]['descripcion'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}


function cambiaZona(zona_id){

	var zona = zona_id.value;
	console.log("zona slelected: "+zona);
		var estado = $('#estado_idS').val();
		console.log("estado pre-seleccionado: "+estado);
		//  updateByEstadoZona(estado,zona);

if (zona != '') {
		$.ajax({
			url: '/backend/pre-registro/on-change-zona',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#tipo_idS').append('<option value="">-Selecciona un tipo-</option>');
                for (var tipo in objJSON) {
                    console.log("de "+objJSON[tipo]['descripcion']);
                    // tipo.append(
                    //     $('<option>', {
                    //     value: objJSON[tipo]['id']
                    //     }).text(objJSON[tipo]['descripcion'])
                    // );
                     $('#tipo_idS').append('<option value=' + objJSON[tipo]['id'] + '>' + objJSON[tipo]['descripcion'] + '</option>');
                }
            }
		});
	}
}