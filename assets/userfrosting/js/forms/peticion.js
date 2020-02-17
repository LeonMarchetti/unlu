function onChangeSelectServicio() {
    /*  Si el servicio seleccionado necesita de un acta para poder ser aprobado
        entonces se muestra un texto indicándolo. */
    if ($('option:selected', this).attr('necesita-acta')) {
        $("#ayuda-acta-servicio").show();

    } else {
        $("#ayuda-acta-servicio").hide();
    }

    /*  Si el servicio seleccionado necesita de una vinculación para poder
        solicitarlo entonces se deshabilita la opción "Sin vinculación" de las
        opciones disponibles. */
    if ($('option:selected', this).attr('necesita-vinculacion')) {
        $("#select-vinculacion")
            .children('option[value=""]')
            .attr("disabled", true);
        $("#ayuda-vinculacion-servicio").show();

    } else {
        $("#select-vinculacion")
            .children('option[value=""]')
            .removeAttr("disabled");
        $("#ayuda-vinculacion-servicio").hide();
    }
}

$(function() {
    $("#select-servicio").select2();
    $("#select-vinculacion").select2();

    // BUG: No funciona si utilizo .on("change ready")
    $("#select-servicio").ready(onChangeSelectServicio);
    $("#select-servicio").change(onChangeSelectServicio);
});