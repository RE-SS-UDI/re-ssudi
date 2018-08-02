urlEliminar='/backend/areaproy/eliminar';

$(document).ready(function(){
    var value = __obtenerTiempo ();
    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: "ingreso/salir", success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value //10 segundos
        })

    var filtro="/backend/areaproy/grid";
    if($("#fdescripcion").val()!="")       filtro+="/descripcion/"+$("#fdescripcion").val();
    if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
    
        $("#flexigrid").flexigrid({
        url: filtro,
        dataType: "xml",
        colModel: [

            {display: "Descripción",           name:"ap.descripcion",       width: 400, sortable: true, align: "center"},
            {display: "Zona",           name:"z.nombre",       width: 300, sortable: true, align: "center"},  
            {display: 'Estatus',           name:"ap.status",       width: 100, sortable : false, align: 'center'},
            {display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
            {display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
        ],
        sortname: "ap.descripcion"
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