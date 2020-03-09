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
        $('select[name="id_vinculacion"]')
            .children('option[value=""]')
            .attr("disabled", true);
        $("#ayuda-vinculacion-servicio").show();

    } else {
        $('select[name="id_vinculacion"]')
            .children('option[value=""]')
            .removeAttr("disabled");
        $("#ayuda-vinculacion-servicio").hide();
    }
}

$(function() {
    var select2VinculacionOptions = {
        allowClear: true,
        templateSelection: function(usuario) {
            if (!$(usuario.element).attr("value")) {
                return $(`<span>${vinculacion_placeholder}</span>`);
            }
            return $(`<span>${$(usuario.element).attr("actividad")}</span>`);
        },
        templateResult: function(usuario) {
            if (!$(usuario.element).attr("value")) {
                return $(`<span>${vinculacion_placeholder}</span>`);
            }
            return $(`<span><b>${$(usuario.element).attr("actividad")}</b><br>${$(usuario.element).attr("responsable")}</span>`);
        },
    };

    $("#select-servicio").select2();
    $('select[name="id_vinculacion"]').select2(select2VinculacionOptions);

    // BUG: No funciona si utilizo .on("change ready")
    $("#select-servicio").ready(onChangeSelectServicio);
    $("#select-servicio").change(onChangeSelectServicio);
});
