$(document).ready(function() {
    
    var value = __obtenerTiempo ();
    var server = window.location.hostname;
    var link = "http://"+server+"/backend/ingreso/salir";

    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: link, success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value  //10 segundos
        })
});


function guardarEncuesta(formulario,encuesta)
{
    $("#"+formulario).validate({
        submitHandler: function(frm)
        {
            
            $(frm).ajaxSubmit({
                success: function(respuesta){
                    if(!isNaN(respuesta))
                    {   
                        _mensajePersonalizado('#_mensaje-1',  'Se guardó las respuestas de la encuesta de forma correcta.',encuesta);

                    }else{                       
                        _mensaje("#_mensaje-1",  respuesta );
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

function _mensajePersonalizado(id, texto,encuesta){

	$(id).html(texto); 
                        
    $(id).dialog({              
        width: "auto",
        height: "auto",
        title: "¡Atención!",
        resizable: false,
        draggable:false,
        modal: true,
        buttons: [              
            {
                id: "aceptar-999",
                text : "Aceptar",
                class: "btn btn-rojo-1",
                click: function(){

                    window.location.href = '/backend/contesta-encuesta/index/encuesta/'+encuesta;
                }
            }
        ] //buttons
    }); //dialog
}

function eliminaSeleccion(persona,pregunta,tipo,valor)
{
//    alert(persona+pregunta+tipo+valor);
//    alert(valor);
    if (persona!='' && pregunta!='' && tipo!='' && valor!='') {
        $.ajax({
            url: '/backend/contesta-encuesta/eliminar',
            type: 'POST',
            data: {persona_id: persona,pregunta_id:pregunta,tipo:tipo,valor:valor},
            success:function(res){
                console.log(res);
            }
        });
        
    }

}