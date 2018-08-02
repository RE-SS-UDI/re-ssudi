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

    if($("#fusuarioId").val()!="")       filtro+="/usuario/"+$("#fnombre").val();
    if($("#fmodelo").val()!="")       filtro+="/modelo/"+$("#fmodelo").val();
    if($("#faccion").val()!="")       filtro+="/accion/"+$("#faccion").val();
    if($("#freferencia").val()!="")       filtro+="/referencia/"+$("#freferencia").val();
    if($("#fdesde").val()!="")       filtro+="/desde/"+$("#fdesde").val();
    if($("#fhasta").val()!="")       filtro+="/hasta/"+$("#fhasta").val();

    $("#fdesde").datepicker({
        dateFormat: "yy-mm-dd"
        ,changeMonth: true
        ,changeYear: true
        ,maxDate: "+0d"
    });

    $("#fhasta").datepicker({
        dateFormat: "yy-mm-dd"
        ,changeMonth: true
        ,changeYear: true
        ,maxDate: "+0d"
    });

    $("#flexigrid").flexigrid({

        url: "/backend/bitacora/grid"
        ,dataType: "xml"
        ,colModel: [
            {display: "Fecha", name: "bitacora.updated_at", width: 200, sortable: true, align: "center"}
            ,{display: "Usuario", name: "usuario", width: 250, sortable: true, align: "center"}
            ,{display: "M&oacute;dulo", name: "modelo", width: 190, sortable: true, align: "center"}
            ,{display: "Acci&oacute;n", name: "accion", width: 300, sortable: true, align: "center"}
            ,{display: "Referencia", name: "referencia", width: 200, sortable: true, align: "center"}
            ,{display: "Id", name:"bitacora.id",width: 50, sortable: false, align: "center"}
            
        ]
        ,sortname: "bitacora.updated_at"
        ,sortorder: "desc"
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


/*
function filtrar(){

    var filtro = "/bitacora/grid";
    var filtro_ = "/bitacora/grid/inicio/1";
    var filtro__ = "";

    if($("#fusuarioId").val() != "")
        filtro__ += "/usuarioId/"+$("#fusuarioId").val();

    if($("#fmodelo").val() != "")
        filtro__ += "/modelo/"+$("#fmodelo").val();

    if($("#faccion").val() != "")
        filtro__ += "/accion/"+$("#faccion").val();

    if($("#freferencia").val() != "")
        filtro__ += "/referencia/"+$("#freferencia").val();
    
    if($("#fdesde").val() != "")
        filtro__ += "/desde/"+$("#fdesde").val();

    if($("#fhasta").val() != "")
        filtro__ += "/hasta/"+$("#fhasta").val();  

    filtro += filtro__;
    filtro_ += filtro__;
    
    $("#flexigrid-1").flexOptions({
        url: filtro_
        ,onSuccess: function(){

            $("#flexigrid-1").flexOptions({
                url: filtro
            });
        }
    }).flexReload();
}

function limpiar(){

    $("#fusuarioId").val("");
    $("#fmodelo").val("");
    $("#faccion").val("");
    $("#freferencia").val("");
    $("#fdesde").val("");
    $("#fhasta").val("");

    filtrar();
}
*/