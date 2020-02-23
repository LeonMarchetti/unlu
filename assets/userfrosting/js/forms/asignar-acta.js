$(function() {
    $("#tablaAsignarActa").ufTable({
        dataUrl: site.uri.public + "/api/unlu/a",
        msgTarget: "#js-form-alerts",
        tablesorter: {
            widgetOptions: {
                pager_size: 5
            },
        }
    });

    $("#tablaAsignarActa").on("pagerComplete.ufTable", function () {
        $(this).find(".ver-acta").click(function(e) {
            e.preventDefault();
            window.open(`${site.uri.public}/api/unlu/a/${$(this).data('id')}`, "_blank");
        });
    });
});
