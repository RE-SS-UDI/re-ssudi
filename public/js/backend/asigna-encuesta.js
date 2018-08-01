$(document).ready(function () {
    var value = __obtenerTiempo();
    $('#mainContent').idle({
        onIdle: function () {
            $.ajax({
                url: "ingreso/salir",
                success: function (result) {
                    __mensajeSinBoton('#_mensaje-1', 'Cerrando aplicacion por falta de actividad...');
                    __delayRefreshPage(600);
                }
            });
        },
        idle: value //10 segundos
    })


    var filtro = '/backend/asigna-encuesta/grid';
    $("#flexigrid").flexigrid({
        url: filtro,
        dataType: "xml",
        colModel: [{
                display: "Nombre de encuesta",
                name: "e.nombre",
                width: 170,
                sortable: true,
                align: "center"
            },
            {
                display: "Nombre de categoria",
                name: "cat.nombre",
                width: 170,
                sortable: true,
                align: "center"
            },
            {
                display: "Zona",
                name: "z.nombre",
                width: 150,
                sortable: true,
                align: "center"
            },
            {
                display: "Estado",
                name: "es.estado",
                width: 150,
                sortable: true,
                align: "center"
            },
            {
                display: "Tipo",
                name: "tp.descripcion",
                width: 150,
                sortable: true,
                align: "center"
            },
            {
                display: "Status",
                name: "ze.status",
                width: 150,
                sortable: true,
                align: "center"
            },
            {
                display: "Habilitar/Deshabilitar",
                name: "enable",
                value: "ze.id",
                width: 105,
                sortable: true,
                align: "center"
            },
            {
                display: "Remover",
                name: "remove",
                value: "ze.id",
                width: 105,
                sortable: true,
                align: "center"
            }


        ],
        sortname: "e.nombre",
        sortorder: "asc",
        usepager: true,
        useRp: false,
        singleSelect: true,
        resizable: false,
        showToggleBtn: false,
        rp: 10,
        width: 1200,
        height: 300
    });
});





function guardarAsignacion(formulario) {
    $("#" + formulario).validate({
        submitHandler: function (frm) {

            $(frm).ajaxSubmit({
                success: function (respuesta) {
                        if (!isNaN(respuesta)) {

                            // cambiaZona();
                            // var tipo_id = $('#tipo_id').val();
                            // cambiaTipo(tipo_id);
                            recargar();

                            _mensaje('#_mensaje-1', 'Se guardó la asignación de forma correcta.');
                        } else {
                            _mensaje("#_mensaje-1", respuesta);
                        }

                    } //success
                    ,
                error: function (respuesta) {
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
                } //error
            }) //ajaxSubmit*/
        } //submitHandler
    }) //validate

    $("#" + formulario).submit();
}

function limpiarAsinacion(formulario) {
    $('#' + formulario)[0].reset();
    cambiaZona();
}

function eliminar(id, estatus) {
    console.log(id);
    var urlEliminar = '/backend/asigna-encuesta/eliminar';
    _confirmar(
        "#_mensaje-1", "¿Est&aacute; seguro que quiere " + (estatus == 1 ? "eliminar" : "activar") + " el registro?",
        function () {
            $("#es_mensaje-1").removeClass("hide");
            $("#texto_mensaje-1").addClass("hide");
            $("#si-999").addClass('hide');
            $("#no-999").addClass('hide');
            $.ajax({
                type: "POST",
                url: urlEliminar,
                data: {
                    id: id
                },
                success: function (respuesta) {
                    recargar();
                    _mensaje("#_mensaje-1", respuesta);
                },
                error: function (respuesta) {
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error al tratar de eliminar el registro, int&eacute;ntelo de nuevo");
                } //error
            });
        }
    );
} //function

function cambiaStatus(id, estatus) {
    console.log(id);
    console.log(estatus);
    var urlStatus = '/backend/asigna-encuesta/status';
    _confirmar(
        "#_mensaje-1", "¿Est&aacute; seguro que quiere " + (estatus == 1 ? "Deshabilitar" : "Habilitar") + " el registro?",
        function () {
            $("#es_mensaje-1").removeClass("hide");
            $("#texto_mensaje-1").addClass("hide");
            $("#si-999").addClass('hide');
            $("#no-999").addClass('hide');
            $.ajax({
                type: "POST",
                url: urlStatus,
                data: {
                    id: id,
                    status: estatus
                },
                success: function (respuesta) {
                    recargar();
                    _mensaje("#_mensaje-1", respuesta);
                },
                error: function (respuesta) {
                    _mensaje("#_mensaje-1", "Ocurri&oacute; un error al tratar de eliminar el registro, int&eacute;ntelo de nuevo");
                } //error
            });
        }
    );
} //function

function updateByEstado(estado_id) {
    var filtro2 = '/backend/asigna-encuesta/grid';
    console.log(zona_id);
    filtro2 += "/estado_id/" + estado_id;

    $("#flexigrid").flexOptions({
        url: filtro2,
        onSuccess: function () {}

    }).flexReload();
}

function updateByZona(estado_id,zona_id) {
    var filtro2 = '/backend/asigna-encuesta/grid';
    console.log(zona_id);
    var tipo_id = $('#tipo_id').val();
    filtro2 += "/estado_id/" + estado_id;
    filtro2 += "/zona_id/" + zona_id;
    filtro2 += "/tipo_id/" + tipo_id;

    $("#flexigrid").flexOptions({
        url: filtro2,
        onSuccess: function () {}

    }).flexReload();
}

function updateByEstadoZonaTipo(estado_id,zona_id, tipo_id) {
    var filtro2 = '/backend/asigna-encuesta/grid';
    console.log("Estado selected: " + estado_id);
    console.log("Zona selected: " + zona_id);
    console.log("Tipo selected: " + tipo_id);
    filtro2 += "/estado_id/" + estado_id;
    filtro2 += "/zona_id/" + zona_id;
    filtro2 += "/tipo_id/" + tipo_id;

    $("#flexigrid").flexOptions({
        url: filtro2,
        onSuccess: function () {}

    }).flexReload();
}



function cambiaZona() {
    var zona_id = $('#zona_id').val();
    console.log("zona seleccionada: " + zona_id);
    var zona = zona_id;
    var estado = $('#estado_id').val();

    // updateByZona(estado,zona);

    if (zona != '') {
        $.ajax({
            url: '/backend/asigna-encuesta/on-change-zona',
            type: 'POST',
            data: {
                zona: zona
            },
            success: function (res) {
                console.log('zona cambiada');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
                var tipo = $('#tipo_id');
                tipo.empty();
                for (var tipos in objJSON) {
                    // console.log(objJSON[zona]['nombre']);
                    tipo.append(
                        $('<option>', {
                            value: objJSON[tipos]['id']
                        }).text(objJSON[tipos]['descripcion'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
                updateByZona(estado,zona);
            }
        });
    }
}

    function cambiaTipo(tipo_id) {
        console.log("tipo seleccionado: " + tipo_id.value);
        var tipo = tipo_id.value;
        var zona = $('#zona_id').val();
        var estado = $('#estado_id').val();

        updateByEstadoZonaTipo(estado,zona,tipo);
    }

    // var filtro2 = '/backend/asigna-encuesta/grid';
    // if ($('#zona_id').val() != '') {
    // 	var zona = $('#zona_id').val();
    // 	var encuestas = '';

    // filtro2+="/zona_id/"+zona;

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

    // }



function cambiaCategoria(categoria_id) {
    console.log("caregoria seleccionada: " + categoria_id.value);
    var categoria = categoria_id.value;

    // updateByEstado(categoria);

    if (categoria != '') {
        $.ajax({
            url: '/backend/asigna-encuesta/on-change-categoria',
            type: 'POST',
            data: {
                categoria: categoria
            },
            success: function (res) {
                console.log('categoria cambiada');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
                var encuestas = $('#encuesta_id');
                encuestas.empty();
                $('#encuesta_id').append('<option value="">-Selecciona una encuesta-</option>');
                for (var encuesta in objJSON) {
                    // console.log(objJSON[zona]['nombre']);
                    encuestas.append(
                        $('<option>', {
                            value: objJSON[encuesta]['id']
                        }).text(objJSON[encuesta]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
        });
    }
}

function cambiaEstado(estado_id) {
    console.log("estado seleccionado: " + estado_id.value);
    var estado = estado_id.value;

    updateByEstado(estado);

    if (estado != '') {
        $.ajax({
            url: '/backend/asigna-encuesta/on-change-estado',
            type: 'POST',
            data: {
                estado: estado
            },
            success: function (res) {
                console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
                var zona = $('#zona_id');
                zona.empty();
                var tipo = $('#tipo_id');
                tipo.empty();
                $('#zona_id').append('<option value="">Selecciona una zona</option>');
                $('#tipo_id').append('<option value="">Selecciona un tipo</option>');
                for (var zonas in objJSON) {
                    // console.log(objJSON[zona]['nombre']);
                    zona.append(
                        $('<option>', {
                            value: objJSON[zonas]['id']
                        }).text(objJSON[zonas]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
        });
    }
}