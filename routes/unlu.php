<?php

$app->group('/unlu', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:page');
})->add('authGuard');

$app->group('/admin/users', function() {
    $this->get('/u/{user_name}', 'UserFrosting\Sprinkle\Unlu\Controller\UsuarioUnluController:pageInfo');
})->add('authGuard');

$app->group('/modals/unlu', function () {
    $this->get('/solicitar-vinculacion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarVinculacionModal');
    $this->get('/solicitar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarServicioModal');
    $this->get('/baja-solicitud', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:bajaSolicitudModal');

    $this->get('/aprobar-peticion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:aprobarPeticionModal');
    $this->get('/editar-peticion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:editarPeticionModal');
})->add('authGuard');

$app->group("/api/unlu", function() {
    $this->post("", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitarVinculacion');
    $this->post("/peticion", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitarServicio');
    $this->post("/baja-solicitud", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:bajaSolicitud');

    $this->post("/p/{id}", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:editarPeticion");
});