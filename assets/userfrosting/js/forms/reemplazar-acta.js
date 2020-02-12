$(function() {
    $(".ver-acta").click(function(e) {
        e.preventDefault();
        window.open(site.uri.public + "/api/unlu/a/" + $(this).data('id'), "_blank");
    });
});