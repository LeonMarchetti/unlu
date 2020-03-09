$(function() {
    /* Agrego reglas a los controles de nombre de usuario y email para
     * determinar si ya existen y en ese caso hacer que el validador del
     * formulario los rechace.
     */

    $('input[name="user_name"]').rules("add", {
        remote: {
            url: site.uri.public + "/api/unlu/existe-usuario"
        },
        messages: {
            remote: nombre_usuario_en_uso
        }
    });

    $('input[name="email"]').rules("add", {
        remote: {
            url: site.uri.public + "/api/unlu/existe-usuario"
        },
        messages: {
            remote: email_en_uso
        }
    });
});
