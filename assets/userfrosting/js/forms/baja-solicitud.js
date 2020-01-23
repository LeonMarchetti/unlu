$(function() {
    $("#select-peticion").select2({
        templateResult: function(peticion) {
            var linea_peticion = `<b>${$(peticion.element).attr("peticion")}</b>`;
            var linea_servicio = `Servicio: <u>${$(peticion.element).attr("servicio")}</u>`;
            return $(`<span>${linea_peticion}<br>${linea_servicio}</span>`);
        },
        templateSelection: function(peticion) {
            return $(`<span>${$(peticion.element).attr("peticion")}</span>`);
        }
    });
});