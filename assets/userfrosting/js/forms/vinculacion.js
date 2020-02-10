$(function() {
    var select2_options = {
        allowClear: true,
        templateSelection: function(usuario) {
            if (!$(usuario.element).attr("value")) {
                return $(`<span>${$("#integrante-placeholder").val()}</span>`);
            }
            return $(`<span>${$(usuario.element).attr("full-name")}</span>`);
        },
        templateResult: function(usuario) {
            if (!$(usuario.element).attr("value")) {
                return $(`<span>${$("#integrante-placeholder").val()}</span>`);
            }
            return $(`<span><b>${$(usuario.element).attr("full-name")}</b><br>${$(usuario.element).attr("user-name")}</span>`);
        },
    };

    $("#select-solicitante").select2(select2_options);

    $("#select-solicitante").change(function() {
        // Al cambiar el solicitante:
        // * Cambio el nombre del responsable.
        // * Muestro el nombre del solicitante en la sección de integrantes.
        // * Deshabilito al solicitante como opción en los selects de integrantes.
        // * Si hay un select con el nuevo solicitante elegido entonces lo elimino.
        var full_name = $("option:selected", this).attr("full-name");
        $("#input-responsable, #input-integrante-solicitante").val(full_name);

        var old_id = $(this).data("old");
        var new_id = this.value;
        var $integrantes_select = $("#integrantes .select-integrante");

        $integrantes_select
            .children(`option[value="${new_id}"]`)
            .attr('disabled', true);

        $integrantes_select
            .children(`option[value="${old_id}"]`)
            .removeAttr('disabled');

        $integrantes_select
            .find(`option[value=${new_id}]:selected`)
            .parent()
            .remove();

        $(this).data("old", new_id);
    });

    // Integrantes
    function onSelectIntegranteChange() {
        // Cuando cambio el usuario en uno de los select:
        // * Deshabilito el usuario elegido en los demás select.
        // * Rehabilito el usuario anterior en los demás select.
        var old_id = $(this).data("old");
        var new_id = this.value;

        var siblings = $(this).siblings("select");

        if (new_id !== "") {
            siblings
                .children(`option[value="${new_id}"]`)
                .attr('disabled', true);
        }

        siblings
            .children(`option[value="${old_id}"]`)
            .removeAttr('disabled');

        $(this).data("old", new_id);
    }

    $(".agregar-integrante-usuario").click(function() {
        $("#integrantes").append($("#agregar-integrante-usuario").html());
    });

    $(".agregar-integrante-no-usuario").click(function() {
        $("#integrantes").append($("#agregar-integrante-no-usuario").html());
    });

    $(".select-integrante").change(onSelectIntegranteChange);

    $(".select-integrante").each(function() {
        // Al editar una vinculación, aplico la lógica de los selects a los que
        // ya vienen creados por el servidor
        var $hermanos = $(this).siblings("select");
        var ids = $hermanos
            .map(function() { return this.value })
            .toArray();

        var id_solicitante = $("#select-solicitante").val();
        ids.push(id_solicitante);

        for (i in ids) {
            $(this)
                .find(`option[value="${ids[i]}"]`)
                .attr('disabled', true);
        }
    });

    $("#integrantes").on("DOMNodeInserted", ".select-integrante", function() {
        // Al insertar este select deshabilito todas las opciones seleccionadas en los otros select
        // También deshabilito al solicitante como opción
        $(this).select2(select2_options);

        var $select = $(".select-integrante:first");
        var first_id = $select.val();
        var actual_ids = $("option[disabled]", $select)
            .map(function() { return this.value })
            .toArray();
        actual_ids.push(first_id);

        for (i in actual_ids) {
            $(this).find(`option[value="${actual_ids[i]}"]`).attr('disabled', true);
        }

        var id_solicitante = $("#select-solicitante").val();
        $(this).find(`option[value="${id_solicitante}"]`).attr('disabled', true);

        $(this).change(onSelectIntegranteChange);
    });

    $(".quitar-integrante").click(function() {
        if ($("#integrantes .form-control").length > 1) {
            // Si el integrante a quitar es un select de usuarios entonces
            // rehabilito ese usuario en los otros select.
            var control = $("#integrantes .form-control").last();

            if (control.hasClass("select-integrante")) {
                control
                    .siblings("select")
                    .children(`option[value="${control.val()}"]`)
                    .removeAttr("disabled");

                control.select2('destroy');
            }

            control.remove();
        }
    });
});
