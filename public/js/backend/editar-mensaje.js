urlEliminar='/backend/editarMensaje/eliminar';

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



    var filtro="/backend/editarMensaje/grid";
    if($("#fusuario").val()!="")       filtro+="/usuario/"+$("#fusuario").val();
    if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
    
        $("#flexigrid").flexigrid({
        url: filtro,
        dataType: "xml",
        colModel: [
            {display: "Usuario destino",           name:"usuario",       width: 300, sortable: true, align: "center"},
            {display: "Tipo de usuario",           name:"descripcion",       width: 242, sortable: true, align: "center"},
            {display: "Asunto",           name:"asunto",       width: 300, sortable: true, align: "center"},
            {display: 'Estatus',           name:"status",       width: 100, sortable : false, align: 'center'},
            {display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
            {display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
        ],
        sortname: "usuario"
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

            $('#fecha_limite').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: "+0d"
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


function cambiaTipoUsuario(id,idZona)
{
    if (id != '') {
        $.ajax({
            url: '/backend/editarMensaje/obtener-usuario',
            type: 'POST',
            data: {

                id: id,
                idZona: idZona

            },
            success: function(res){
                $('#usuario_destino').html(res);

                if ($('#usu').val() != '') {
                    $('#usuario_destino').val($('#usu').val());
                }       
            }
        });
    }
}