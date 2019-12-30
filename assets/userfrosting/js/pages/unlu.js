$(function() {

    function attachRenderSuccessUfModal() {
        /*  Recargar página cuando el modal termina exitosamente.
            Hay que llamar a la función cada vez que se renderiza un modal. */
        $("body").on('renderSuccess.ufModal', function() {
            var modal = $(this).ufModal('getModal');
            var form = modal.find('.js-form');

            form.ufForm().on("submitSuccess.ufForm", function() {
                window.location.reload(true);
            });
        });
    }

    // Modal para agregar un postre
    $(".solicitar-vinculacion").click(function(e) {
        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-vinculacion",
            msgTarget: $("#alerts-page")
        });

        $("body").on('renderSuccess.ufModal', function() {
            var modal = $(this).ufModal('getModal');
            var form = modal.find('.js-form');

            var solicitanteWidget = modal.find('.form-solicitante');
            solicitanteWidget.ufCollection({
                dropdown: {
                    ajax: {
                        url: site.uri.public + '/api/unlu/u',
                    },
                    placeholder: "Seleccione un usuario",
                },
                dropdownTemplate: modal.find('#vinculacion-solicitante-opcion').html(),
                rowTemplate: modal.find('#vinculacion-solicitante-fila').html(),
            });

            form.ufForm().on("submitSuccess.ufForm", function() {
                window.location.reload(true);
            });
        });

        // attachRenderSuccessUfModal();
    });

    // Modal para agregar un postre
    $(".solicitar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-servicio",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });

    // Modal para agregar un postre
    $(".baja-solicitud").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/baja-solicitud",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });

    $(".ver-acta").click(function(e) {
        e.preventDefault();

        alert("TODO Acta N°: \"" + $(this).data("id") + "\"");
    });

    // Modal para editar un postre
    $(".agregar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/agregar-servicio",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });

    // Tabla de Vinculaciones ==================================================
    /* Fuente de datos de la tabla de vinculaciones. */
    $("#tablaVinculaciones").ufTable({
        dataUrl: site.uri.public + "/api/unlu/v",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        }
    });

    /*  Asignar eventos a los botones en la tabla de servicios cuando termina
        de renderizar la tabla. */
    $("#tablaVinculaciones").on("pagerComplete.ufTable", function () {

        // Editar vinculación
        $(this).find(".editar-vinculacion").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/editar-vinculacion",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });

        // Ver acta
        $(this).find(".ver-acta").click(function(e) {
            e.preventDefault();

            window.open(site.uri.public + "/api/unlu/a/" + $(this).data('id'), "_blank");
        });

        // Asignar acta
        $(this).find(".asignar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/asignar-acta",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });
    });

    // Tabla de Servicios ======================================================
    /* Fuente de datos de la tabla de servicios. */
    $("#tablaServicios").ufTable({
        dataUrl: site.uri.public + "/api/unlu/s",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        }
    });

    /*  Asignar eventos a los botones en la tabla de servicios cuando termina
        de renderizar la tabla. */
    $("#tablaServicios").on("pagerComplete.ufTable", function () {

        // Editar servicios
        $(this).find(".editar-servicio").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/editar-servicio",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });

        // Eliminar Servicios
        $(this).find(".eliminar-servicio").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/eliminar-servicio",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });
    });

    // Tabla de Peticiones======================================================
    /* Fuente de datos de la tabla de servicios. */
    $("#tablaPeticiones").ufTable({
        dataUrl: site.uri.public + "/api/unlu/p",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        }
    });

    /*  Asignar eventos a los botones en la tabla de servicios cuando termina
        de renderizar la tabla. */
    $("#tablaPeticiones").on("pagerComplete.ufTable", function () {

        // Modal para borrar un postre
        $(this).find(".aprobar-peticion").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/aprobar-peticion",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });

        // Modal para editar un postre
        $(this).find(".editar-peticion").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/editar-peticion",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });
    });
});

