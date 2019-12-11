$(function() {
    /*  Si se cambia el solicitante entonces automáticamente se lo selecciona
        como integrante. */
    $("#select-solicitante").on("change", function() {
        $("#select-integrante-solicitante").val($("#select-solicitante").val());
    });

    $(".agregar-integrante-usuario").click(function() {
        $("#integrantes").append(
            $("#agregar-integrante-usuario").html()
        );
    });

    $(".agregar-integrante-no-usuario").click(function() {
        $("#integrantes").append(
            $("#agregar-integrante-no-usuario").html()
        );
    });

    $(".quitar-integrante").click(function() {
        if ($("#integrantes .form-control").length > 1) {
            $("#integrantes").find(".form-control").last().remove();
        }
    });
});
