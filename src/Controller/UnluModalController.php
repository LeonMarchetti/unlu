<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Servicio;
use UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;


class UnluModalController extends SimpleController {

    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        // Valores por defecto de la vinculación que provienen de los datos del usuario actual.
        $vinculacion = [
            "responsable" => $currentUser->full_name,
            "cargo" => $currentUser->rol,
            "correo" => $currentUser->email,
            "telefono" => $currentUser->telefono,
        ];

        // Lista de tipos de usuario
        $tipos_de_usuario = TipoUsuario::all();
        $usuarios = Usuario::all();

        return $this->ci->view->render($response, 'modals/vinculacion.html.twig', [
            "vinc" => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
            "form" => [
                "action" => "api/unlu",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function solicitarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        $servicios = Servicio::all();

        if ($authorizer->checkAccess($currentUser, 'admin_unlu')) {
            // Usuario administrador
            $vinculaciones = Vinculacion::all();

        } else {
            $vinculaciones = $currentUser->vinculaciones;
        }

        return $this->ci->view->render($response, 'modals/peticion.html.twig', [
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            "form" => [
                "action" => "api/unlu/peticion",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function bajaSolicitudModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        $peticiones = Peticion::all();

        return $this->ci->view->render($response, 'modals/baja-solicitud.html.twig', [
            "peticiones" => $peticiones,
            "form" => [
                "action" => "api/unlu/baja-solicitud",
                "method" => "POST",
                "submit_text" => "Borrar"
            ]
        ]);
    }

    public function aprobarPeticionModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $peticion = $this->getPeticionFromParams($params);
        if (!$peticion) {
            throw new NotFoundException();
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        return $this->ci->view->render($response, 'modals/aprobar-peticion.html.twig', [
            'peticion' => $peticion,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
            ],
        ]);
    }

    public function editarPeticionModal(Request $request, Response $response, $args) {

        // GET parameters
        $params = $request->getQueryParams();

        $peticion = $this->getPeticionFromParams($params);
        if (!$peticion) {
            throw new NotFoundException();
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /*  Como origen de datos del select creo una lista con solamente la
            vinculación y el servicio a las que está asignada la petición (ya
            que comparto el formulario con "Solicitar Servicio").
        */
        $servicios = [ $peticion->servicio ];
        $vinculaciones = [ $peticion->vinculacion ];

        /*  edicion => true: Como comparto con el formulario de "Solicitar Ser-
            vicio", cuando quiero editar una petición y solo tener disponibles
            para editar la descripción y las observaciones con esta opción in-
            dico que se deshabiliten los demás controles.
        */
        return $this->ci->view->render($response, 'modals/editar-peticion.html.twig', [
            "edicion" => true,
            'peticion' => $peticion,
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
                "method" => "POST",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    public function editarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getServicioFromParams($request->getQueryParams());
        if (!$servicio) {
            throw new NotFoundException();
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/servicio.html.twig', [
            "servicio" => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
                "method" => "POST",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    public function eliminarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getServicioFromParams($request->getQueryParams());

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/eliminar-servicio.html.twig', [
            'servicio' => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
            ],
        ]);
    }

    protected function getPeticionFromParams($params) {
        $schema = new RequestSchema("schema://requests/unlu/peticion/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $peticion = Peticion::find($data["id"]);

        return $peticion;
    }

    protected function getServicioFromParams($params) {
        $schema = new RequestSchema("schema://requests/unlu/servicio/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = Servicio::find($data["id"]);
        return $servicio;
    }
}