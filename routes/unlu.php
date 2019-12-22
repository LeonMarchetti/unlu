<?php

$app->group('/unlu', function () {
    $this->get('', 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:page')
         ->setName('unlu');
})->add('authGuard');

$app->group('/account', function () {
    $this->get('/register', 'UserFrosting\Sprinkle\Unlu\Controller\UnluAccountController:pageRegister')
        ->add('checkEnvironment')
        ->setName('register');
});

$app->group('/admin/users', function() {
    $this->get('/u/{user_name}', 'UserFrosting\Sprinkle\Unlu\Controller\UsuarioUnluController:pageInfo');
})->add('authGuard');

$app->group('/modals/unlu', function () {
    $this->get('/solicitar-vinculacion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarVinculacionModal');
    $this->get('/solicitar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:solicitarServicioModal');
    $this->get('/baja-solicitud', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:bajaSolicitudModal');

    $this->get('/aprobar-peticion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:aprobarPeticionModal');
    $this->get('/editar-peticion', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:editarPeticionModal');

    $this->get('/agregar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:agregarServicioModal');
    $this->get('/editar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:editarServicioModal');
    $this->get('/eliminar-servicio', 'UserFrosting\Sprinkle\Unlu\Controller\UnluModalController:eliminarServicioModal');
})->add('authGuard');

$app->group("/api/unlu", function() {
    $this->post("", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitarVinculacion');
    $this->post("/peticion", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:solicitarServicio');
    $this->post("/baja-solicitud", 'UserFrosting\Sprinkle\Unlu\Controller\UnluController:bajaSolicitud');

    $this->get("/p", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:listarPeticiones");
    $this->post("/p/{id}", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:editarPeticion");

    $this->get("/s", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:listarServicios");
    $this->post("/s", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:agregarServicio");
    $this->post("/s/{id}", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:editarServicio");
    $this->delete("/s/{id}", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:eliminarServicio");

    $this->get("/v", "UserFrosting\Sprinkle\Unlu\Controller\UnluController:listarVinculaciones");
});