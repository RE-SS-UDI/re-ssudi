urlEliminar='/backend/tipo-usuario/eliminar';

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


    
	var filtro="/backend/tipo-usuario/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fstatus").val()!="")      filtro+="/status/"+$("#fstatus").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Descripción",           name:"tu.descripcion",       width: 400, sortable: true, align: "center"},
			{display: 'Estatus',           name:"tu.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
			{display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
		],
		sortname: "tu.descripcion"
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
