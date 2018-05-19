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
			{display: "Apellido paterno",name:"p.apellido_pat",       width: 200, sortable: true, align: "center"},			
			{display: 'Apellido materno',         name:"p.apellido_mat",     width: 200, sortable : false, align: 'center'},
			{display: 'Correo',         name:"p.correo",     width: 240, sortable : false, align: 'center'},
//			{display: 'Tel&eacute;fono',         name:"p.telefono",     width: 160, sortable : false, align: 'center'},
			{display: 'Estatus',           name:"p.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
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