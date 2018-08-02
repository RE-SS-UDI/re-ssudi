urlEliminar='/backend/preguntas/eliminar';

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


	var filtro="/backend/preguntas/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
	if($("#ftipo").val()!="")       filtro+="/tipo/"+$("#ftipo").val();
	if($("#fcategoria").val()!="")      filtro+="/categoria/"+$("#fcategoria").val();
	if($("#fnombre_encuesta").val()!="")      filtro+="/nombre_encuesta/"+$("#fnombre_encuesta").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'},
			{display: "Descripción",           name:"p.descripcion",       width: 400, sortable: true, align: "center"},
			{display: "Tipo",           name:"p.tipo",       width: 180, sortable: true, align: "center"},
			{display: "Encuesta",           name:"e.nombre",       width: 250, sortable: true, align: "center"},
			{display: "Categor&iacute;a",           name:"c.nombre",       width: 250, sortable: true, align: "center"},
			{display: 'Estatus',           name:"p.status",       width: 100, sortable : false, align: 'center'}
		],
		sortname: "p.id"
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


function agregarPregunta(urlDestino, identificador, form, titulo)
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
			cambiaTipo();
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


function agregaOpciones()
{
	var tipo = $("#tipo").val();
	var cantidad = $("#cantidad").val();

	if (tipo != '') {
		$.ajax({
			url: '/backend/preguntas/agrega-opciones',
			type: 'POST',
			data: {tipo: tipo, cantidad: cantidad},
			success: function(res){
				$("#opciones").append(res);
			}
		});
	}

}

function cambiaTipo()
{
	if ($('#tipo').val() != '' && $('#tipo').val() != '1' && $('#tipo').val() != '2') {
		$('#etiqueta-cantidad').removeClass('hide');
		$('#input-cantidad').removeClass('hide');
		$('#btnAgregar').removeClass('hide');
	} else {
		$('#etiqueta-cantidad').addClass('hide');
		$('#input-cantidad').addClass('hide');
		$('#btnAgregar').addClass('hide');
	}
}

function eliminaOpcion(id)
{
	$('#opcion_'+id).remove();
}

function eliminarOpciones(id)
{
	
	$.ajax({
		url: '/backend/preguntas/eliminar-opciones',
		type: 'POST',
		data: {id: id},
		success: function(res)
		{
			console.log(id);
			if (res == 'El registro fue eliminado') {
				$('#opcion_'+id).remove();
			} 
		}
	});
	
}

function obtieneEncuestas()
{
	$.ajax({
		url: '/backend/preguntas/obtiene-encuestas',
		type: 'POST',
		data: {id: $('#fcategoria').val()},
		success: function(res)
		{
			$('#fnombre_encuesta').html(res);
		}
	});
	
}