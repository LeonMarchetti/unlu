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
            fecha_min: $("#inputFechaMin").val(),
            fecha_max: $("#inputFechaMax").val()
        }
    });

    $("#tablaPeticionesVencidas").on("pagerComplete.ufTable", function () {
        $(this).find(".ver-acta-servicio").click(function(e) {
            e.preventDefault();
            window.open(`${site.uri.public}/api/unlu/as/${$(this).data('id')}`, "_blank");
        });
    });
});
