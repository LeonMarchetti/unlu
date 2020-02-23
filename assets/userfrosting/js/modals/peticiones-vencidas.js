$(function() {
    $("#tablaPeticionesVencidas").ufTable({
        dataUrl: site.uri.public + "/api/unlu/peticiones-vencidas",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            }
        },
        addParams: {
            fecha_min: $("#input-date-min").val(),
            fecha_max: $("#input-date-max").val(),
        }
    });

    $("#tablaPeticionesVencidas").on("pagerComplete.ufTable", function () {
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
            window.open(`${site.uri.public}/api/unlu/as/${$(this).data('id')}`, "_blank");
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

    $("#input-date-min").change(function() {
        $("#tablaPeticionesVencidas").ufTable("refresh");
    });

    $("#input-date-max").change(function() {
        $("#tablaPeticionesVencidas").ufTable("refresh");
    });
});
