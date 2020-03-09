$(function() {
    function attachRenderSuccessUfModal(tablaRefresh, msgTarget, hasValidator) {
        /**
         * Vincula el modal a la página.
         *
         * @param {string}  tablaRefresh    Selector de la tabla a refrescar al terminar este modal.
         * @param {string}  msgTarget       Selector del destino del flujo de alertas.
         * @param {boolean} hasValidator    Si el formulario del modal usa un validador.
         */
        $("body").on('renderSuccess.ufModal', function() {
            var modal = $(this).ufModal('getModal');
            var form = modal.find('.js-form');
            var validator = hasValidator ? { validator: page.validators } : {};

            form.ufForm(validator)
                .on("submitSuccess.ufForm", function() {
                    // Refrescar tabla
                    $(tablaRefresh).ufTable("refresh");
                    // Sacar el modal
                    $("body").ufModal("destroy");
                    $("body").removeClass("modal-open");
                    $(".modal-backdrop").remove();
                    // Actualizar el flujo de alertas
                    $(msgTarget).ufAlerts("fetch").ufAlerts("render");
                });
        });
    }

    // Inicialización de sectores de alertas:
    $("#alertas-vinculaciones").ufAlerts();
    $("#alertas-servicios").ufAlerts();
    $("#alertas-peticiones").ufAlerts();
    $("#alertas-actas").ufAlerts();

    // Modal para solicitar una vinculación
    $(".solicitar-vinculacion").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-vinculacion",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal("#tablaVinculaciones", "#alerts-page", true);
    });

    // Modal para solicitar un servicio
    $(".solicitar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/solicitar-servicio",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal("#tablaPeticiones", "#alerts-page", true);
    });

    // Modal para eliminar una petición
    $(".baja-solicitud").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/baja-solicitud",
            msgTarget: $("#alerts-page")
        });

        attachRenderSuccessUfModal("#tablaPeticiones", "#alerts-page", false);
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

    Handlebars.registerHelper('ifURL', function(texto, options) {
        var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/

        if (regexp.test(texto)) {
            return options.fn(this);
        } else {
            return options.inverse(this);
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
                msgTarget: $("#alertas-vinculaciones")
            });

            attachRenderSuccessUfModal("#tablaVinculaciones", "#alertas-vinculaciones", true);
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

            attachRenderSuccessUfModal("#tablaVinculaciones", "#alertas-vinculaciones", false);
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
        $(this).find(".editar-servicio").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/editar-servicio",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alertas-servicios")
            });

            attachRenderSuccessUfModal("#tablaServicios", "#alertas-servicios", true);
        });

        // Eliminar Servicios
        $(this).find(".eliminar-servicio").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/eliminar-servicio",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alertas-servicios")
            });

            attachRenderSuccessUfModal("#tablaServicios", "#alertas-servicios", false);
        });
    });

    // Modal para agregar un servicio
    $(".agregar-servicio").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/agregar-servicio",
            msgTarget: $("#alertas-servicios")
        });

        attachRenderSuccessUfModal("#tablaServicios", "#alertas-servicios", true);
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
        // b -> row.acta
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
                msgTarget: $("#alertas-peticiones")
            });

            attachRenderSuccessUfModal("#tablaPeticiones", "#alertas-peticiones", false);
        });

        // Modal para editar una petición
        $(this).find(".editar-peticion").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/editar-peticion",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alertas-peticiones")
            });

            attachRenderSuccessUfModal("#tablaPeticiones", "#alertas-peticiones", true);
        });

        $(this).find(".ver-acta-servicio").click(function(e) {
            e.preventDefault();
            window.open(`${site.uri.public}/api/unlu/as/${$(this).data('id')}`, "_blank");
        });

        $(this).find(".asignar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/asignar-acta-peticion",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alertas-peticiones")
            });

            attachRenderSuccessUfModal("#tablaPeticiones", "#alertas-peticiones", true);
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
                msgTarget: $("#alertas-actas")
            });

            attachRenderSuccessUfModal("#tablaActas", "#alertas-actas", true);
        });

        $(this).find(".eliminar-acta").click(function(e) {
            e.preventDefault();

            $("body").ufModal({
                sourceUrl: site.uri.public + "/modals/unlu/eliminar-acta",
                ajaxParams: {
                    id: $(this).data('id')
                },
                msgTarget: $("#alertas-actas")
            });

            attachRenderSuccessUfModal("#tablaActas", "#alertas-actas", false);
        });
    });

    // Modal para agregar un acta
    $(".agregar-acta").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/agregar-acta",
            msgTarget: $("#alertas-actas")
        });

        attachRenderSuccessUfModal("#tablaActas", "#alertas-actas", true);
    });

    $(".informe-peticiones").click(function(e) {
        e.preventDefault();

        $("body").ufModal({
            sourceUrl: site.uri.public + "/modals/unlu/informe-peticiones",
            ajaxParams: {
                fecha_min: $("#inputInformePeticionesMin").val(),
                fecha_max: $("#inputInformePeticionesMax").val()
            },
            msgTarget: $("#alerts-page"),
        });

        attachRenderSuccessUfModal("", "#alerts-page", false);
    });
});

