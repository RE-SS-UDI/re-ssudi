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

    cargaTabla();

});


function cargaTabla()
{
    $.ajax({
        url: '/backend/concentradoProy/tabla',
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function filtrar()
{
    var filtro='';

    $("#frmFiltrosCategoria :input").each(function(){
        if(this.id!='' && $("#"+this.id).val()!='')
            filtro+="/"+this.name+"/"+$("#"+this.id).val();
    });

    $.ajax({
        url: '/backend/concentradoProy/tabla'+filtro,
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function limpiar()
{
    cargaTabla();
}

function agregarProyEmp(urlDestino, identificador, form, titulo, idProy, idEmp)
{
    $.ajax({
        type: "POST",
        url: urlDestino,
        data: {
            id: identificador,
            proyecto_id: idProy,
            empresa_id: idEmp
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



