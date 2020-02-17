$(function() {
    function attachRenderSuccessUfModal() {
        /*  Recargar página cuando el modal termina exitosamente.
            Hay que llamar a la función cada vez que se renderiza un modal. */
        $("body").on('renderSuccess.ufModal', function() {
            var modal = $(this).ufModal('getModal');
            var form = modal.find('.js-form');

            form
                .ufForm({
                    validator: page.validators
                })
                .on("submitSuccess.ufForm", function() {
                    window.location.reload(true);
                });
        });
    }

    // Modal para solicitar una vinculación
    $(".solicitar-vinculacion").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-vinculacion",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });

    // Modal para solicitar un servicio
    $(".solicitar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-servicio",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });

    // Modal para eliminar una petición
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

    // Modal para agregar un servicio
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

    /*  Asignar eventos a los botones en la tabla de vinculaciones cuando termina
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
    /* Fuente de datos de la tabla de peticiones. */
    $("#tablaPeticiones").ufTable({
        dataUrl: site.uri.public + "/api/unlu/p",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        }
    });

    Handlebars.registerHelper('ifPuedeAprobar', function(a, b, c, d, options) {
        // a -> row.necesita_acta
        // b -> row.ubicacion
        // c -> row.vinculacion
        // d -> row.vinculacion.id_acta
        if ((!a && !c) || (!a && d) || (b && !c) || (b && d)) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    /*  Asignar eventos a los botones en la tabla de peticiones cuando termina
        de renderizar la tabla. */
    $("#tablaPeticiones").on("pagerComplete.ufTable", function () {
        // Modal para borrar una petición
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

        // Modal para editar una petición
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

        $(this).find(".ver-acta-servicio").click(function(e) {
            e.preventDefault();
            window.open(`${site.uri.public}/api/unlu/as/${$(this).data('ubicacion')}`, "_blank");
        });

        $(this).find(".asignar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/asignar-acta-peticion",
                ajaxParams: {
                    id: $(this).data('id'), // id de la petición
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });
    });

    // Tabla de Actas ==========================================================
    /* Fuente de datos de la tabla de actas. */
    $("#tablaActas").ufTable({
        dataUrl: site.uri.public + "/api/unlu/a",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        }
    });

    /*  Asignar eventos a los botones en la tabla de actas cuando termina
        de renderizar la tabla. */
    $("#tablaActas").on("pagerComplete.ufTable", function () {
        $(this).find(".ver-acta").click(function(e) {
            e.preventDefault();

            window.open(site.uri.public + "/api/unlu/a/" + $(this).data('id'), "_blank");
        });

        $(this).find(".reemplazar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/reemplazar-acta",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });

        $(this).find(".eliminar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/eliminar-acta",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alerts-page")
            });

            attachRenderSuccessUfModal();
        });
    });

    // Modal para agregar un acta
    $(".agregar-acta").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/agregar-acta",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal();
    });
});

