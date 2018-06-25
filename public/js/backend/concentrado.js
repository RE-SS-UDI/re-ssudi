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


function muestraEncuesta(persona, encuesta)
{
    $.ajax({
        type: "POST",
        url: '/backend/concentrado/encuesta',
        data: {
            persona_id: persona, encuesta_id: encuesta
        }
        ,success: function(html)
        {
            $("#_dialogo-1").html(html);        
            $("#_dialogo-1").dialog({
                width: "1000",
                height: "auto",
                title: "Encuesta contestada",
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

function cargaTabla()
{
    console.log("default");

    $.ajax({
        url: '/backend/concentrado/tabla',
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function cargaTablaZona()
{
    // var zona_id = $("#Zid").val();
    // console.log(zona_id);

    var filtro2 = '/backend/concentrado/tabla';

	if ($('#Zid').val() != '') {
		var zona = $('#Zid').val();

          filtro2+="/Zid/"+zona;

          $.ajax({
            url: filtro2,
            success: function(res){
                $('#cuerpo_tabla').html(res);
            }
        });
    
    // $.ajax({
    //     url: '/backend/concentrado/tabla',
    //     type: 'POST',
    //     data: {zona_id: zona_id},
    //     success: function(res){
    //         $('#cuerpo_tabla').html(res);
    //     }
    // });
    
}
}


function cambiaZona(){
	var filtro2 = '/backend/concentrado/tabla';

	if ($('#encuestaCat').val() != '') {
		var zona = $('#encuestaCat').val();

          filtro2+="/encuestaCat/"+zona;

          $.ajax({
            url: filtro2,
            success: function(res){
                $('#cuerpo_tabla').html(res);
            }
        });

	//     $("#flexigrid").flexOptions({
	// 		url: filtro2,
	//         onSuccess: function(){
	//         }

	// 	}).flexReload();
	// }else{
	//     $("#flexigrid").flexOptions({
	// 		url: filtro2,
	//         onSuccess: function(){
	//         }

	// 	}).flexReload();

	}
}

function filtrar()
{
    var filtro='';

    $("#frmFiltrosCategoria :input").each(function(){
        if(this.id!='' && $("#"+this.id).val()!='')
            filtro+="/"+this.name+"/"+$("#"+this.id).val();
    });

    $.ajax({
        url: '/backend/concentrado/tabla'+filtro,
        success: function(res){
            $('#cuerpo_tabla').html(res);
        }
    });
    
}

function limpiar()
{
    cargaTabla();
}