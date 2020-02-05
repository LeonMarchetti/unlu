function toggleAyudaActaServicio() {
    /* Si el servicio seleccionado necesita de un acta para poder ser aprobado
    entonces se muestra un texto indicándolo. */
    if ($('option:selected', this).attr('necesita-acta')) {
        $("#ayuda-acta-servicio").show();
    } else {
        $("#ayuda-acta-servicio").hide();
    }
}

$(function() {
    $("#select-servicio").select2();
    $("#select-vinculacion").select2();

    // BUG: No funciona si utilizo .on("change ready")
    $("#select-servicio").ready(toggleAyudaActaServicio);
    $("#select-servicio").change(toggleAyudaActaServicio);
});